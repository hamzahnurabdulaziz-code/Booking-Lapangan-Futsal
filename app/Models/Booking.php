<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'request_key',
        'user_id',
        'field_id',
        'starts_at',
        'ends_at',
        'team_name',
        'notes',
        'status',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * Get the user who made the booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the booked field.
     *
     * @return BelongsTo<Field, $this>
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Scope a query to only include blocking bookings (confirmed or completed).
     *
     * @param Builder<Booking> $query
     * @return Builder<Booking>
     */
    public function scopeBlocking(Builder $query): Builder
    {
        return $query->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed]);
    }

    /**
     * Get starts_at in Asia/Jakarta timezone.
     */
    public function getStartsAtLocalAttribute(): Carbon
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        return $this->starts_at->copy()->setTimezone($tz);
    }

    /**
     * Get ends_at in Asia/Jakarta timezone.
     */
    public function getEndsAtLocalAttribute(): Carbon
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        return $this->ends_at->copy()->setTimezone($tz);
    }

    /**
     * Determine if this booking can be cancelled by the given user.
     */
    public function canBeCancelledBy(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($this->status !== BookingStatus::Confirmed) {
            return false;
        }

        // Must be before starts_at
        if (now()->greaterThanOrEqualTo($this->starts_at)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Determine if this booking can be marked completed by the given user.
     */
    public function canBeCompletedBy(?User $user): bool
    {
        if ($user === null || !$user->isAdmin()) {
            return false;
        }

        if ($this->status !== BookingStatus::Confirmed) {
            return false;
        }

        // Admin can mark complete only after ends_at has passed
        return now()->greaterThanOrEqualTo($this->ends_at);
    }
}
