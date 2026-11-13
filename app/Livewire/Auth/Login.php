<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Masuk Akun — FutsalKita')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public bool $hasPendingBooking = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectIntendedDestination();
            return;
        }

        $this->hasPendingBooking = session()->has('intended_booking');
    }

    /**
     * Handle the user login request.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        session()->regenerate();

        $this->redirectIntendedDestination();
    }

    private function redirectIntendedDestination(): void
    {
        if (session()->has('intended_booking')) {
            $this->redirectRoute('customer.booking.create', navigate: true);
            return;
        }

        if (Auth::user()?->isAdmin()) {
            $this->redirectRoute('admin.dashboard', navigate: true);
            return;
        }

        $this->redirectRoute('customer.bookings', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
