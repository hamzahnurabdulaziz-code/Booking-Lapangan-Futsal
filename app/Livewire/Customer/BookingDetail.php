<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingDetail extends Component
{
    public Booking $booking;

    public bool $confirmingCancel = false;

    public function mount(int $id): void
    {
        $this->booking = Booking::with(['field', 'user'])->findOrFail($id);

        // Authorization check: only owner or admin can view
        if (!Auth::user()->isAdmin() && $this->booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk melihat booking ini.');
        }
    }

    public function promptCancel(): void
    {
        if (!$this->booking->canBeCancelledBy(Auth::user())) {
            session()->flash('error', 'Booking tidak dapat dibatalkan.');
            return;
        }

        $this->confirmingCancel = true;
    }

    public function cancelBooking(BookingService $bookingService): void
    {
        try {
            $this->booking = $bookingService->cancelBooking(Auth::user(), $this->booking);
            $this->confirmingCancel = false;
            session()->flash('success', 'Booking berhasil dibatalkan.');
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
            $this->confirmingCancel = false;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal membatalkan booking: ' . $e->getMessage());
            $this->confirmingCancel = false;
        }
    }

    public function render()
    {
        return view('livewire.customer.booking-detail')
            ->title('Detail Booking #' . $this->booking->booking_code . ' — FutsalKita');
    }
}
