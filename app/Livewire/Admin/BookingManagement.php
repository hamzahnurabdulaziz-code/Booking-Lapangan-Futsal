<?php

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Field;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kelola Booking — FutsalKita')]
class BookingManagement extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $fieldId = 'all';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $date = '';

    // Cancel modal state
    public bool $showCancelModal = false;
    public ?int $bookingIdToCancel = null;
    public ?Booking $bookingToCancel = null;
    public string $cancellationReason = '';

    public function mount(): void
    {
        // default empty date means any date
    }

    /**
     * Open cancel modal.
     */
    public function promptCancel(int $bookingId): void
    {
        $booking = Booking::with(['field', 'user'])->findOrFail($bookingId);

        if (!$booking->canBeCancelledBy(Auth::user())) {
            session()->flash('error', 'Booking tidak dapat dibatalkan (waktu telah lewat atau status bukan Terkonfirmasi).');
            return;
        }

        $this->bookingIdToCancel = $bookingId;
        $this->bookingToCancel = $booking;
        $this->cancellationReason = '';
        $this->showCancelModal = true;
    }

    /**
     * Submit cancellation by admin with mandatory reason.
     */
    public function submitCancel(BookingService $bookingService): void
    {
        $this->validate([
            'cancellationReason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'cancellationReason.required' => 'Admin wajib mengisi alasan pembatalan booking.',
            'cancellationReason.min' => 'Alasan pembatalan minimal 3 karakter.',
            'cancellationReason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        $booking = Booking::findOrFail($this->bookingIdToCancel);

        try {
            $bookingService->cancelBooking(Auth::user(), $booking, $this->cancellationReason);
            session()->flash('success', "Booking {$booking->booking_code} berhasil dibatalkan oleh admin.");
            $this->closeCancelModal();
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal membatalkan booking: ' . $e->getMessage());
        }
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->bookingIdToCancel = null;
        $this->bookingToCancel = null;
        $this->cancellationReason = '';
    }

    /**
     * Mark booking as completed (only after ends_at).
     */
    public function completeBooking(int $bookingId, BookingService $bookingService): void
    {
        $booking = Booking::findOrFail($bookingId);

        try {
            $bookingService->completeBooking(Auth::user(), $booking);
            session()->flash('success', "Booking {$booking->booking_code} berhasil ditandai sebagai Selesai.");
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyelesaikan booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $query = Booking::with(['field', 'user'])->orderBy('starts_at', 'desc');

        if (!empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('booking_code', 'like', "%{$term}%")
                  ->orWhere('team_name', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', "%{$term}%")
                         ->orWhere('phone', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%");
                  });
            });
        }

        if ($this->fieldId !== 'all' && is_numeric($this->fieldId)) {
            $query->where('field_id', (int) $this->fieldId);
        }

        if ($this->status !== 'all' && in_array($this->status, ['confirmed', 'completed', 'cancelled'])) {
            $query->where('status', $this->status);
        }

        if (!empty($this->date)) {
            $dayStartUtc = Carbon::createFromFormat('Y-m-d', $this->date, $tz)->startOfDay()->setTimezone('UTC');
            $dayEndUtc = Carbon::createFromFormat('Y-m-d', $this->date, $tz)->endOfDay()->setTimezone('UTC');
            $query->whereBetween('starts_at', [$dayStartUtc, $dayEndUtc]);
        }

        $bookings = $query->paginate(15);
        $fields = Field::orderBy('name')->get();

        return view('livewire.admin.booking-management', [
            'bookings' => $bookings,
            'fields' => $fields,
        ]);
    }
}
