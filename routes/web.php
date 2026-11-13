<?php

use App\Livewire\Admin\BookingManagement;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\FieldManagement;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Customer\BookingCreate;
use App\Livewire\Customer\BookingDetail;
use App\Livewire\Customer\BookingIndex;
use App\Livewire\PublicPages\FieldDetail;
use App\Livewire\PublicPages\FieldList;
use App\Livewire\PublicPages\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — FutsalKita
|--------------------------------------------------------------------------
*/

// Public Pages
Route::get('/', Home::class)->name('home');
Route::get('/lapangan', FieldList::class)->name('fields.index');
Route::get('/lapangan/{field:slug}', FieldDetail::class)->name('fields.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Login::class)->name('login');
    Route::get('/daftar', Register::class)->name('register');
});

Route::post('/keluar', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
})->name('logout');

// Customer Protected Routes (Requires Auth)
Route::middleware('auth')->group(function () {
    Route::get('/booking/konfirmasi', BookingCreate::class)->name('customer.booking.create');
    Route::get('/booking-saya', BookingIndex::class)->name('customer.bookings');
    Route::get('/booking-saya/{id}', BookingDetail::class)->name('customer.bookings.show');
});

// Admin Protected Routes (Requires Auth & Admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/lapangan', FieldManagement::class)->name('fields');
    Route::get('/booking', BookingManagement::class)->name('bookings');
});
