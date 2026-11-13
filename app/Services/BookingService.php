<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Calculate and return slot availability for a field on a given date in Asia/Jakarta timezone.
     *
     * @param int $fieldId
     * @param string $dateString Format: Y-m-d
     * @param int $durationHours
     * @return array<int, array{
     *     start_hour: int,
     *     end_hour: int,
     *     starts_at_local: string,
     *     ends_at_local: string,
     *     label: string,
     *     is_available: bool,
     *     is_past: bool,
     *     reason: string|null
     * }>
     */
    public function getAvailableSlots(int $fieldId, string $dateString, int $durationHours = 1): array
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $openingHour = (int) config('futsal.opening_hour', 8);
        $closingHour = (int) config('futsal.closing_hour', 23);

        $date = Carbon::createFromFormat('Y-m-d', $dateString, $tz)->startOfDay();
        $now = Carbon::now($tz);

        $field = Field::find($fieldId);
        $isFieldActive = $field !== null && $field->is_active;

        // Fetch all blocking bookings for this field on this day (converting to UTC window)
        $dayStartUtc = $date->copy()->setTime($openingHour, 0, 0)->setTimezone('UTC');
        $dayEndUtc = $date->copy()->setTime($closingHour, 0, 0)->setTimezone('UTC');

        $existingBookings = Booking::query()
            ->where('field_id', $fieldId)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
            ->where(function ($query) use ($dayStartUtc, $dayEndUtc) {
                $query->where('starts_at', '<', $dayEndUtc)
                      ->where('ends_at', '>', $dayStartUtc);
            })
            ->get();

        $slots = [];

        for ($hour = $openingHour; $hour <= $closingHour - $durationHours; $hour++) {
            $slotStartLocal = $date->copy()->setTime($hour, 0, 0);
            $slotEndLocal = $date->copy()->setTime($hour + $durationHours, 0, 0);

            $slotStartUtc = $slotStartLocal->copy()->setTimezone('UTC');
            $slotEndUtc = $slotEndLocal->copy()->setTimezone('UTC');

            $isPast = $slotStartLocal->lessThanOrEqualTo($now);

            // Check if overlaps with any confirmed or completed bookings
            $hasConflict = false;
            foreach ($existingBookings as $b) {
                // Interval [start, end) overlap: existing.starts_at < requested.ends_at AND existing.ends_at > requested.starts_at
                if ($b->starts_at->lessThan($slotEndUtc) && $b->ends_at->greaterThan($slotStartUtc)) {
                    $hasConflict = true;
                    break;
                }
            }

            $isAvailable = true;
            $reason = null;

            if (!$isFieldActive) {
                $isAvailable = false;
                $reason = 'Lapangan sedang tidak aktif.';
            } elseif ($isPast) {
                $isAvailable = false;
                $reason = 'Waktu telah lewat.';
            } elseif ($hasConflict) {
                $isAvailable = false;
                $reason = 'Jadwal telah terisi.';
            }

            $slots[] = [
                'start_hour' => $hour,
                'end_hour' => $hour + $durationHours,
                'starts_at_local' => $slotStartLocal->toDateTimeString(),
                'ends_at_local' => $slotEndLocal->toDateTimeString(),
                'label' => sprintf('%02d:00 - %02d:00', $hour, $hour + $durationHours),
                'is_available' => $isAvailable,
                'is_past' => $isPast,
                'reason' => $reason,
            ];
        }

        return $slots;
    }

    /**
     * Create a new booking with row locking and idempotency protection.
     *
     * @param User $user
     * @param array{
     *     field_id: int,
     *     selected_date: string,
     *     start_hour: int,
     *     duration_hours: int,
     *     team_name?: string|null,
     *     notes?: string|null,
     *     request_key?: string|null
     * } $data
     * @return Booking
     *
     * @throws ValidationException
     */
    public function createBooking(User $user, array $data): Booking
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $openingHour = (int) config('futsal.opening_hour', 8);
        $closingHour = (int) config('futsal.closing_hour', 23);
        $minDuration = (int) config('futsal.min_duration_hours', 1);
        $maxDuration = (int) config('futsal.max_duration_hours', 3);
        $maxAdvanceDays = (int) config('futsal.max_advance_days', 30);

        $fieldId = (int) $data['field_id'];
        $dateStr = (string) $data['selected_date'];
        $startHour = (int) $data['start_hour'];
        $duration = (int) ($data['duration_hours'] ?? 1);
        $teamName = isset($data['team_name']) ? trim((string) $data['team_name']) : null;
        $notes = isset($data['notes']) ? trim((string) $data['notes']) : null;
        $requestKey = !empty($data['request_key']) ? (string) $data['request_key'] : (string) Str::uuid();

        // 1. Validate duration
        if ($duration < $minDuration || $duration > $maxDuration) {
            throw ValidationException::withMessages([
                'duration_hours' => "Durasi booking harus antara {$minDuration} sampai {$maxDuration} jam.",
            ]);
        }

        // 2. Validate operational hours
        if ($startHour < $openingHour || ($startHour + $duration) > $closingHour) {
            throw ValidationException::withMessages([
                'start_hour' => "Jam bermain harus berada dalam rentang jam operasional ({$openingHour}:00 - {$closingHour}:00 WIB).",
            ]);
        }

        // 3. Construct local date times in Asia/Jakarta
        try {
            $requestedDate = Carbon::createFromFormat('Y-m-d', $dateStr, $tz)->startOfDay();
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'selected_date' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD.',
            ]);
        }

        $nowLocal = Carbon::now($tz);
        $todayLocal = $nowLocal->copy()->startOfDay();
        $maxDateLocal = $todayLocal->copy()->addDays($maxAdvanceDays)->endOfDay();

        if ($requestedDate->lessThan($todayLocal)) {
            throw ValidationException::withMessages([
                'selected_date' => 'Tanggal booking tidak boleh di masa lampau.',
            ]);
        }

        if ($requestedDate->greaterThan($maxDateLocal)) {
            throw ValidationException::withMessages([
                'selected_date' => "Booking maksimal dilakukan untuk {$maxAdvanceDays} hari ke depan.",
            ]);
        }

        $startsAtLocal = $requestedDate->copy()->setTime($startHour, 0, 0);
        $endsAtLocal = $requestedDate->copy()->setTime($startHour + $duration, 0, 0);

        if ($startsAtLocal->lessThanOrEqualTo($nowLocal)) {
            throw ValidationException::withMessages([
                'start_hour' => 'Waktu mulai bermain harus di masa mendatang.',
            ]);
        }

        // Convert to UTC for database storage
        $startsAtUtc = $startsAtLocal->copy()->setTimezone('UTC');
        $endsAtUtc = $endsAtLocal->copy()->setTimezone('UTC');

        // Check idempotency first: if booking with same request_key exists
        $existingByKey = Booking::where('request_key', $requestKey)->first();
        if ($existingByKey !== null) {
            if ($existingByKey->user_id !== $user->id) {
                throw ValidationException::withMessages([
                    'request_key' => 'Token idempotensi tidak valid untuk pengguna ini.',
                ]);
            }

            // If same parameters, return existing booking (safe retry)
            if (
                $existingByKey->field_id === $fieldId &&
                $existingByKey->starts_at->equalTo($startsAtUtc) &&
                $existingByKey->ends_at->equalTo($endsAtUtc)
            ) {
                return $existingByKey;
            }

            throw ValidationException::withMessages([
                'request_key' => 'Permintaan dengan token yang sama memiliki data berbeda.',
            ]);
        }

        // 4. Concurrency protection via DB Transaction & row lock on the field
        return DB::transaction(function () use ($fieldId, $startsAtUtc, $endsAtUtc, $user, $teamName, $notes, $requestKey) {
            // Lock field row first (deterministic lock order)
            $field = Field::where('id', $fieldId)->lockForUpdate()->first();

            if ($field === null) {
                throw ValidationException::withMessages([
                    'field_id' => 'Lapangan tidak ditemukan.',
                ]);
            }

            if (!$field->is_active) {
                throw ValidationException::withMessages([
                    'field_id' => 'Lapangan ini sedang tidak aktif dan tidak dapat dipesan.',
                ]);
            }

            // Recheck for overlapping bookings inside the transaction
            $conflict = Booking::query()
                ->where('field_id', $fieldId)
                ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
                ->where('starts_at', '<', $endsAtUtc)
                ->where('ends_at', '>', $startsAtUtc)
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'conflict' => 'Jadwal yang dipilih sudah terisi oleh pemesan lain. Silakan pilih jam atau lapangan lain.',
                ]);
            }

            // Generate unique booking code: FK-YYYYMMDD-XXXX
            $datePrefix = Carbon::now('Asia/Jakarta')->format('Ymd');
            $randomString = strtoupper(Str::random(4));
            $bookingCode = "FK-{$datePrefix}-{$randomString}";

            // Ensure unique booking code
            while (Booking::where('booking_code', $bookingCode)->exists()) {
                $randomString = strtoupper(Str::random(4));
                $bookingCode = "FK-{$datePrefix}-{$randomString}";
            }

            return Booking::create([
                'booking_code' => $bookingCode,
                'request_key' => $requestKey,
                'user_id' => $user->id,
                'field_id' => $field->id,
                'starts_at' => $startsAtUtc,
                'ends_at' => $endsAtUtc,
                'team_name' => $teamName,
                'notes' => $notes,
                'status' => BookingStatus::Confirmed,
            ]);
        });
    }

    /**
     * Cancel an existing booking.
     *
     * @param User $user
     * @param Booking $booking
     * @param string|null $reason
     * @return Booking
     *
     * @throws ValidationException
     */
    public function cancelBooking(User $user, Booking $booking, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($user, $booking, $reason) {
            // Lock field first to maintain strict lock hierarchy
            Field::where('id', $booking->field_id)->lockForUpdate()->first();

            // Refresh booking record under lock
            $freshBooking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if ($freshBooking->status !== BookingStatus::Confirmed) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya booking berstatus Terkonfirmasi yang dapat dibatalkan.',
                ]);
            }

            if (now()->greaterThanOrEqualTo($freshBooking->starts_at)) {
                throw ValidationException::withMessages([
                    'starts_at' => 'Booking tidak dapat dibatalkan karena waktu bermain telah dimulai atau lewat.',
                ]);
            }

            if (!$user->isAdmin()) {
                if ($freshBooking->user_id !== $user->id) {
                    throw ValidationException::withMessages([
                        'unauthorized' => 'Anda tidak memiliki hak untuk membatalkan booking ini.',
                    ]);
                }
            } else {
                // Admin must provide cancellation reason
                if (empty(trim((string) $reason))) {
                    throw ValidationException::withMessages([
                        'cancellation_reason' => 'Admin wajib mengisi alasan pembatalan booking.',
                    ]);
                }
            }

            $freshBooking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason ? trim($reason) : 'Dibatalkan oleh pelanggan',
            ]);

            return $freshBooking;
        });
    }

    /**
     * Mark a confirmed booking as completed (admin only, after ends_at).
     *
     * @param User $user
     * @param Booking $booking
     * @return Booking
     *
     * @throws ValidationException
     */
    public function completeBooking(User $user, Booking $booking): Booking
    {
        if (!$user->isAdmin()) {
            throw ValidationException::withMessages([
                'unauthorized' => 'Hanya administrator yang dapat menandai booking sebagai selesai.',
            ]);
        }

        return DB::transaction(function () use ($booking) {
            Field::where('id', $booking->field_id)->lockForUpdate()->first();

            $freshBooking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            if ($freshBooking->status !== BookingStatus::Confirmed) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya booking berstatus Terkonfirmasi yang dapat ditandai selesai.',
                ]);
            }

            if (now()->lessThan($freshBooking->ends_at)) {
                throw ValidationException::withMessages([
                    'ends_at' => 'Booking hanya dapat ditandai selesai setelah waktu bermain berakhir.',
                ]);
            }

            $freshBooking->update([
                'status' => BookingStatus::Completed,
            ]);

            return $freshBooking;
        });
    }
}
