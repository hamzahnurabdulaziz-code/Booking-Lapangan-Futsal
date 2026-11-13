<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' — FutsalKita' : 'FutsalKita — Jadwal Main Aman, Tinggal Ajak Tim' }}</title>

    <!-- Meta Description for SEO -->
    <meta name="description" content="Aplikasi pemesanan dan pengecekan jadwal lapangan futsal realtime FutsalKita. Pilih lapangan, tentukan jam main, dan simpan booking timmu dengan mudah.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col bg-[#F7F7F2] text-[#17231B] antialiased selection:bg-[#2F6B3D] selection:text-white">

    <!-- Top Floating Navigation -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 w-full backdrop-blur-md bg-white/90 border-b border-[#E2E7E2] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2F6B3D] rounded-xl px-2 py-1">
                        <div class="w-10 h-10 rounded-xl bg-[#2F6B3D] text-white flex items-center justify-center font-bold text-xl shadow-sm group-hover:bg-[#245430] transition-colors">
                            FK
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold tracking-tight text-[#17231B] leading-none">Futsal<span class="text-[#2F6B3D]">Kita</span></span>
                            <span class="text-[11px] font-medium text-[#58695C] tracking-wide mt-0.5">Booking Arena Futsal</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-[#17231B]" aria-label="Navigasi Utama">
                    <a href="{{ route('home') }}" class="hover:text-[#2F6B3D] transition-colors {{ request()->routeIs('home') ? 'text-[#2F6B3D] font-semibold' : '' }}">Beranda</a>
                    <a href="{{ route('fields.index') }}" class="hover:text-[#2F6B3D] transition-colors {{ request()->routeIs('fields.*') ? 'text-[#2F6B3D] font-semibold' : '' }}">Lapangan</a>
                    <a href="{{ route('home') }}#cara-booking" class="hover:text-[#2F6B3D] transition-colors">Cara Booking</a>
                    <a href="{{ route('home') }}#faq" class="hover:text-[#2F6B3D] transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons / User Menu -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-stone-900 text-white hover:bg-black transition-colors shadow-sm focus-ring">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @else
                            <a href="{{ route('customer.bookings') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-[#EBF3ED] text-[#2F6B3D] hover:bg-[#dcebdd] transition-colors focus-ring">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Booking Saya</span>
                            </a>
                        @endif

                        <div class="flex items-center gap-2 pl-3 border-l border-[#E2E7E2]">
                            <span class="text-sm font-medium text-[#17231B] truncate max-w-[140px]">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-stone-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors focus-ring" title="Keluar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-[#17231B] hover:text-[#2F6B3D] transition-colors focus-ring rounded-xl">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold bg-[#2F6B3D] text-white hover:bg-[#245430] transition-colors shadow-sm focus-ring">
                            Daftar Akun
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileOpen = !mobileOpen" class="p-2.5 rounded-xl text-stone-700 hover:bg-stone-100 focus-ring" aria-label="Buka menu navigasi" :aria-expanded="mobileOpen">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileOpen" x-transition.opacity.duration.200ms @keydown.escape.window="mobileOpen = false" class="md:hidden border-t border-[#E2E7E2] bg-white px-4 pt-3 pb-6 space-y-3">
            <nav class="flex flex-col space-y-2 text-base font-medium">
                <a href="{{ route('home') }}" @click="mobileOpen = false" class="px-3 py-2 rounded-lg hover:bg-stone-50 text-[#17231B]">Beranda</a>
                <a href="{{ route('fields.index') }}" @click="mobileOpen = false" class="px-3 py-2 rounded-lg hover:bg-stone-50 text-[#17231B]">Daftar Lapangan</a>
                <a href="{{ route('home') }}#cara-booking" @click="mobileOpen = false" class="px-3 py-2 rounded-lg hover:bg-stone-50 text-[#17231B]">Cara Booking</a>
                <a href="{{ route('home') }}#faq" @click="mobileOpen = false" class="px-3 py-2 rounded-lg hover:bg-stone-50 text-[#17231B]">FAQ</a>
            </nav>

            <div class="pt-4 border-t border-stone-200 flex flex-col gap-2">
                @auth
                    <div class="px-3 py-2 text-sm text-[#58695C]">
                        Masuk sebagai <strong class="text-[#17231B]">{{ auth()->user()->name }}</strong>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="w-full text-center px-4 py-2.5 rounded-xl font-semibold bg-stone-900 text-white">Panel Admin</a>
                    @else
                        <a href="{{ route('customer.bookings') }}" class="w-full text-center px-4 py-2.5 rounded-xl font-semibold bg-[#EBF3ED] text-[#2F6B3D]">Booking Saya</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 rounded-xl font-semibold border border-stone-300 text-[#17231B]">Masuk</a>
                    <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 rounded-xl font-semibold bg-[#2F6B3D] text-white">Daftar Akun</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alert / Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm" role="alert">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-sm" role="alert">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Professional Footer -->
    <footer class="bg-white border-t border-[#E2E7E2] mt-20 pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: About FutsalKita -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#2F6B3D] text-white flex items-center justify-center font-bold text-lg">
                            FK
                        </div>
                        <span class="text-xl font-bold tracking-tight text-[#17231B]">Futsal<span class="text-[#2F6B3D]">Kita</span></span>
                    </div>
                    <p class="text-sm text-[#58695C] leading-relaxed max-w-md">
                        Platform reservasi dan pengecekan jadwal lapangan futsal modern, cepat, dan terpercaya. Jadwal main aman, langsung terkonfirmasi tanpa ribet konfirmasi manual.
                    </p>
                    <div class="pt-2 text-xs text-[#58695C] flex flex-col gap-1">
                        <div>📍 <strong>Lokasi:</strong> {{ config('futsal.venue_address') }}</div>
                        <div>⏰ <strong>Jam Operasional:</strong> 08.00 – 23.00 WIB (Setiap Hari)</div>
                        <div>🌐 <strong>Zona Waktu:</strong> Asia/Jakarta (WIB)</div>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h3 class="text-sm font-semibold text-[#17231B] tracking-wider uppercase mb-4">Navigasi</h3>
                    <ul class="space-y-2.5 text-sm text-[#58695C]">
                        <li><a href="{{ route('home') }}" class="hover:text-[#2F6B3D] transition-colors">Beranda</a></li>
                        <li><a href="{{ route('fields.index') }}" class="hover:text-[#2F6B3D] transition-colors">Daftar Lapangan</a></li>
                        <li><a href="{{ route('home') }}#cara-booking" class="hover:text-[#2F6B3D] transition-colors">Cara Pemesanan</a></li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-[#2F6B3D] transition-colors">Pertanyaan Umum (FAQ)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Kebijakan & Ketentuan -->
                <div>
                    <h3 class="text-sm font-semibold text-[#17231B] tracking-wider uppercase mb-4">Ketentuan MVP</h3>
                    <ul class="space-y-2 text-xs text-[#58695C] leading-relaxed">
                        <li>• Pemesanan dibuka hari ini s/d 30 hari ke depan.</li>
                        <li>• Durasi sewa 1 hingga 3 jam per booking.</li>
                        <li>• Booking langsung terkonfirmasi di server.</li>
                        <li>• Pembatalan mandiri sebelum waktu bermain dimulai.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-stone-200 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-4">
                <p>&copy; {{ date('Y') }} FutsalKita. Seluruh hak cipta dilindungi.</p>
                <p class="text-stone-400">Final Project Kelompok 3 Orang • Frontend Architecture</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
