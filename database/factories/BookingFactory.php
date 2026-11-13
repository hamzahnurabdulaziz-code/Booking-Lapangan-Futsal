<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now('UTC')->addDays(fake()->numberBetween(1, 10))->setHour(10)->setMinute(0)->setSecond(0);
        $end = $start->copy()->addHours(2);
        $dateStr = $start->format('Ymd');

        return [
            'booking_code' => 'FK-' . $dateStr . '-' . strtoupper(Str::random(4)),
            'request_key' => fake()->uuid(),
            'user_id' => User::factory(),
            'field_id' => Field::factory(),
            'starts_at' => $start,
            'ends_at' => $end,
            'team_name' => fake()->company(),
            'notes' => fake()->sentence(),
            'status' => BookingStatus::Confirmed,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Completed,
            'starts_at' => Carbon::now('UTC')->subDays(2)->setHour(10)->setMinute(0)->setSecond(0),
            'ends_at' => Carbon::now('UTC')->subDays(2)->setHour(12)->setMinute(0)->setSecond(0),
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(?string $reason = 'Dibatalkan oleh pelanggan'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
