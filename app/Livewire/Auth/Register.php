<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daftar Akun — FutsalKita')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama tidak boleh melebihi 100 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi untuk koordinasi jadwal.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Security rule: Always register as customer, never elevate to admin from request
        $user = User::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'phone' => trim($this->phone),
            'role' => UserRole::Customer,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        if (session()->has('intended_booking')) {
            $this->redirectRoute('customer.booking.create', navigate: true);
            return;
        }

        $this->redirectRoute('customer.bookings', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
