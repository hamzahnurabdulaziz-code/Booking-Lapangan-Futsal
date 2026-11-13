<?php

namespace App\Livewire\PublicPages;

use App\Models\Field;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class FieldDetail extends Component
{
    public Field $field;

    #[Url]
    public string $date = '';

    #[Url]
    public ?int $hour = null;

    #[Url]
    public int $duration = 1;

    public function mount(Field $field): void
    {
        $this->field = $field;

        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $today = Carbon::now($tz)->toDateString();

        if (empty($this->date)) {
            $this->date = $today;
        }

        // Validate date bounds (today up to 30 days ahead)
        $minDate = Carbon::now($tz)->startOfDay();
        $maxDate = $minDate->copy()->addDays(config('futsal.max_advance_days', 30))->endOfDay();

        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $this->date, $tz)->startOfDay();
            if ($parsedDate->lessThan($minDate) || $parsedDate->greaterThan($maxDate)) {
                $this->date = $today;
            }
        } catch (\Throwable $e) {
            $this->date = $today;
        }

        if ($this->duration < 1 || $this->duration > 3) {
            $this->duration = 1;
        }
    }

    /**
     * Change selected duration and reset selected hour if needed.
     */
    public function setDuration(int $hours): void
    {
        if (in_array($hours, [1, 2, 3])) {
            $this->duration = $hours;
            $this->hour = null;
        }
    }

    /**
     * Select a specific start hour.
     */
    public function selectSlot(int $startHour): void
    {
        $this->hour = $startHour;
    }

    /**
     * Proceed to booking summary or redirect to login with stored session.
     */
    public function proceedToBooking()
    {
        if (!$this->field->is_active) {
            session()->flash('error', 'Lapangan ini sedang nonaktif dan tidak dapat dipesan.');
            return;
        }

        if ($this->hour === null) {
            session()->flash('error', 'Silakan pilih jam mulai terlebih dahulu dari daftar slot.');
            return;
        }

        $bookingData = [
            'field_id' => $this->field->id,
            'selected_date' => $this->date,
            'start_hour' => $this->hour,
            'duration_hours' => $this->duration,
        ];

        // Store in session so it persists across login/register
        session(['intended_booking' => $bookingData]);

        if (!Auth::check()) {
            return $this->redirectRoute('login', navigate: true);
        }

        return $this->redirectRoute('customer.booking.create', navigate: true);
    }

    public function render(BookingService $bookingService)
    {
        $slots = $bookingService->getAvailableSlots($this->field->id, $this->date, $this->duration);

        // Check if currently selected hour is still available
        $selectedSlotInfo = null;
        if ($this->hour !== null) {
            foreach ($slots as $slot) {
                if ($slot['start_hour'] === $this->hour) {
                    $selectedSlotInfo = $slot;
                    break;
                }
            }
        }

        return view('livewire.public-pages.field-detail', [
            'slots' => $slots,
            'selectedSlotInfo' => $selectedSlotInfo,
        ])->title($this->field->name . ' — FutsalKita');
    }
}
