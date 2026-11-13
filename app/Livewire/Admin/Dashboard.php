<?php

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Field;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Admin — FutsalKita')]
class Dashboard extends Component
{
    public function render()
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $todayStartUtc = Carbon::now($tz)->startOfDay()->setTimezone('UTC');
        $todayEndUtc = Carbon::now($tz)->endOfDay()->setTimezone('UTC');

        $totalFields = Field::count();
        $activeFields = Field::active()->count();

        $bookingsToday = Booking::whereBetween('starts_at', [$todayStartUtc, $todayEndUtc])->count();
        $upcomingBookings = Booking::where('status', BookingStatus::Confirmed)->where('starts_at', '>', now())->count();
        $completedBookings = Booking::where('status', BookingStatus::Completed)->count();
        $cancelledBookings = Booking::where('status', BookingStatus::Cancelled)->count();

        $recentBookings = Booking::with(['field', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalFields' => $totalFields,
            'activeFields' => $activeFields,
            'bookingsToday' => $bookingsToday,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'cancelledBookings' => $cancelledBookings,
            'recentBookings' => $recentBookings,
        ]);
    }
}
