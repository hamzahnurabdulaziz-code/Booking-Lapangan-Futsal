<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Booking Saya — FutsalKita')]
class BookingIndex extends Component
{
    #[Url]
    public string $tab = 'upcoming'; // upcoming, completed, cancelled

    public string $search = '';

    // Cancellation modal state
    public bool $confirmingCancel = false;
    public ?int $bookingIdToCancel = null;
    public ?Booking $bookingToCancel = null;

    /**
     * Open confirmation modal to cancel booking.
     */
    public function promptCancel(int $bookingId): void
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        if (!$booking->canBeCancelledBy(Auth::user())) {
            session()->flash('error', 'Booking tidak dapat dibatalkan karena waktu bermain telah dimulai atau status bukan terkonfirmasi.');
            return;
        }

        $this->bookingIdToCancel = $bookingId;
        $this->bookingToCancel = $booking;
        $this->confirmingCancel = true;
    }

    /**
     * Cancel the selected booking.
     */
    public function cancelBooking(BookingService $bookingService): void
    {
        if (!$this->bookingIdToCancel) {
            return;
        }

        $booking = Booking::where('user_id', Auth::id())->findOrFail($this->bookingIdToCancel);

        try {
            $bookingService->cancelBooking(Auth::user(), $booking);

            session()->flash('success', "Booking {$booking->booking_code} berhasil dibatalkan. Jadwal kini telah tersedia kembali.");
            $this->closeCancelModal();
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
            $this->closeCancelModal();
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal membatalkan booking: ' . $e->getMessage());
            $this->closeCancelModal();
        }
    }

    public function closeCancelModal(): void
    {
        $this->confirmingCancel = false;
        $this->bookingIdToCancel = null;
        $this->bookingToCancel = null;
    }

    public function render()
    {
        $user = Auth::user();
        $query = Booking::with('field')
            ->where('user_id', $user->id);

        if (!empty(trim($this->search))) {
            $query->where('booking_code', 'like', '%' . trim($this->search) . '%');
        }

        if ($this->tab === 'upcoming') {
            $query->where('status', BookingStatus::Confirmed)
                  ->where('starts_at', '>', now())
                  ->orderBy('starts_at', 'asc');
        } elseif ($this->tab === 'completed') {
            $query->where(function ($q) {
                $q->where('status', BookingStatus::Completed)
                  ->orWhere(function ($sub) {
                      $sub->where('status', BookingStatus::Confirmed)
                          ->where('starts_at', '<=', now());
                  });
            })->orderBy('starts_at', 'desc');
        } elseif ($this->tab === 'cancelled') {
            $query->where('status', BookingStatus::Cancelled)
                  ->orderBy('updated_at', 'desc');
        }

        $bookings = $query->get();

        return view('livewire.customer.booking-index', [
            'bookings' => $bookings,
        ]);
    }
}
