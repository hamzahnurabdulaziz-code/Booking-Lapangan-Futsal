<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Field;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ringkasan Booking — FutsalKita')]
class BookingCreate extends Component
{
    public ?int $fieldId = null;
    public string $selectedDate = '';
    public ?int $startHour = null;
    public int $durationHours = 1;
    public string $teamName = '';
    public string $notes = '';

    // Idempotency token per attempt session
    public string $requestKey = '';

    // Success state
    public ?Booking $confirmedBooking = null;

    public function mount(): void
    {
        // Try restoring from session if available
        $intended = session('intended_booking', []);

        $this->fieldId = $intended['field_id'] ?? request()->query('field_id');
        $this->selectedDate = $intended['selected_date'] ?? request()->query('date', Carbon::now('Asia/Jakarta')->toDateString());
        $this->startHour = isset($intended['start_hour']) ? (int) $intended['start_hour'] : (request()->has('hour') ? (int) request()->query('hour') : null);
        $this->durationHours = isset($intended['duration_hours']) ? (int) $intended['duration_hours'] : (request()->has('duration') ? (int) request()->query('duration') : 1);

        if (empty($this->fieldId) || $this->startHour === null) {
            $this->redirectRoute('fields.index', navigate: true);
            return;
        }

        $this->requestKey = (string) Str::uuid();
    }

    /**
     * Submit booking request to server.
     */
    public function submitBooking(BookingService $bookingService): void
    {
        $this->validate([
            'fieldId' => ['required', 'exists:fields,id'],
            'selectedDate' => ['required', 'date'],
            'startHour' => ['required', 'integer', 'min:8', 'max:22'],
            'durationHours' => ['required', 'integer', 'min:1', 'max:3'],
            'teamName' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'teamName.max' => 'Nama tim tidak boleh melebihi 100 karakter.',
            'notes.max' => 'Catatan tidak boleh melebihi 500 karakter.',
        ]);

        try {
            $booking = $bookingService->createBooking(Auth::user(), [
                'field_id' => $this->fieldId,
                'selected_date' => $this->selectedDate,
                'start_hour' => $this->startHour,
                'duration_hours' => $this->durationHours,
                'team_name' => $this->teamName,
                'notes' => $this->notes,
                'request_key' => $this->requestKey,
            ]);

            // Clear session pending choice
            session()->forget('intended_booking');

            $this->confirmedBooking = $booking;
        } catch (ValidationException $e) {
            // Re-throw to display errors in view
            throw $e;
        } catch (\Throwable $e) {
            session()->flash('error', 'Terjadi kesalahan sistem saat memproses booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $field = Field::find($this->fieldId);

        $endHour = ($this->startHour !== null) ? ($this->startHour + $this->durationHours) : null;

        return view('livewire.customer.booking-create', [
            'field' => $field,
            'endHour' => $endHour,
        ]);
    }
}
