<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' — FutsalKita' : 'FutsalKita — Jadwal Main Aman, Tinggal Ajak Tim' }}</title>

    <meta name="description" content="Platform reservasi dan pengecekan jadwal lapangan futsal realtime FutsalKita. Pilih lapangan, tentukan jam main, dan simpan booking tim Anda dengan mudah dan aman.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col bg-[#F8FAF8] text-slate-900 antialiased selection:bg-emerald-600 selection:text-white">

    <!-- Top Sticky Navigation Bar -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 w-full backdrop-blur-md bg-white/95 border-b border-slate-200 shadow-xs transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <div class="flex shrink-0 items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group rounded-xl p-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-xs group-hover:bg-emerald-700 transition-colors">
                            FK
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold tracking-tight text-slate-900 leading-none">Futsal<span class="text-emerald-600">Kita</span></span>
                            <span class="mt-0.5 text-[11px] font-medium text-slate-500 tracking-wide">Arena Booking Futsal</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-700" aria-label="Navigasi Utama">
                    <a href="{{ route('home') }}" class="transition-colors hover:text-emerald-600 {{ request()->routeIs('home') ? 'text-emerald-600 font-bold' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('fields.index') }}" class="transition-colors hover:text-emerald-600 {{ request()->routeIs('fields.*') ? 'text-emerald-600 font-bold' : '' }}">
                        Lapangan
                    </a>
                    <a href="{{ route('home') }}#cara-booking" class="transition-colors hover:text-emerald-600">
                        Cara Booking
                    </a>
                    <a href="{{ route('home') }}#faq" class="transition-colors hover:text-emerald-600">
                        FAQ
                    </a>
                </nav>

                <!-- Auth Buttons / User Menu -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-black transition-colors shadow-xs focus-ring">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @else
                            <a href="{{ route('customer.bookings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition-colors focus-ring border border-emerald-200">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Booking Saya</span>
                            </a>
                        @endif

                        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                            <span class="text-xs font-semibold text-slate-800 max-w-[140px] truncate" title="{{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors focus-ring" title="Keluar dari akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-xs font-bold text-slate-700 hover:text-emerald-700 transition-colors focus-ring rounded-xl">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-xs focus-ring">
                            Daftar Akun
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button 
                        type="button" 
                        @click="mobileOpen = !mobileOpen" 
                        class="p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 focus-ring" 
                        aria-label="Buka menu navigasi" 
                        :aria-expanded="mobileOpen"
                    >
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div 
            x-show="mobileOpen" 
            x-transition.opacity.duration.150ms 
            @keydown.escape.window="mobileOpen = false" 
            class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg"
        >
            <nav class="flex flex-col space-y-1 text-sm font-semibold">
                <a href="{{ route('home') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-800 {{ request()->routeIs('home') ? 'text-emerald-600 font-bold bg-emerald-50/50' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('fields.index') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-800 {{ request()->routeIs('fields.*') ? 'text-emerald-600 font-bold bg-emerald-50/50' : '' }}">
                    Daftar Lapangan
                </a>
                <a href="{{ route('home') }}#cara-booking" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-800">
                    Cara Booking
                </a>
                <a href="{{ route('home') }}#faq" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-800">
                    FAQ
                </a>
            </nav>

            <div class="pt-4 border-t border-slate-200 flex flex-col gap-2">
                @auth
                    <div class="px-3 py-1.5 text-xs text-slate-500">
                        Masuk sebagai <strong class="text-slate-800">{{ auth()->user()->name }}</strong>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white">
                            Panel Admin
                        </a>
                    @else
                        <a href="{{ route('customer.bookings') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Booking Saya
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-xs font-bold border border-slate-300 text-slate-800 hover:bg-slate-50">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Flash Alerts -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-start gap-3 shadow-xs" role="alert">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 flex items-start gap-3 shadow-xs" role="alert">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs sm:text-sm font-semibold">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Professional Footer -->
    <footer class="bg-white border-t border-slate-200 mt-20 pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: About FutsalKita -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-xs">
                            FK
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">Futsal<span class="text-emerald-600">Kita</span></span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-md">
                        Platform reservasi dan pengecekan jadwal lapangan futsal realtime modern. Jadwal main terkonfirmasi instan tanpa risiko jadwal bentrok atau konfirmasi manual yang berbelit.
                    </p>
                    <div class="pt-2 text-xs text-slate-600 flex flex-col gap-1.5">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">📍</span>
                            <span><strong>Lokasi Arena:</strong> {{ config('futsal.venue_address') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">⏰</span>
                            <span><strong>Jam Operasional:</strong> 08.00 – 23.00 WIB (Setiap Hari)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-600 font-bold">🌐</span>
                            <span><strong>Zona Waktu:</strong> Asia/Jakarta (WIB)</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase mb-4">Navigasi Utama</h3>
                    <ul class="space-y-2.5 text-xs text-slate-600">
                        <li><a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('fields.index') }}" class="hover:text-emerald-600 transition-colors">Daftar Lapangan</a></li>
                        <li><a href="{{ route('home') }}#cara-booking" class="hover:text-emerald-600 transition-colors">Cara Pemesanan</a></li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-emerald-600 transition-colors">Pertanyaan Umum (FAQ)</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-600 transition-colors">Masuk Akun</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-emerald-600 transition-colors">Daftar Akun Baru</a></li>
                    </ul>
                </div>

                <!-- Col 3: Ketentuan Reservasi -->
                <div>
                    <h3 class="text-xs font-bold text-slate-900 tracking-wider uppercase mb-4">Ketentuan Pemesanan</h3>
                    <ul class="space-y-2 text-xs text-slate-600 leading-relaxed">
                        <li class="flex items-start gap-1.5"><span class="text-emerald-600 font-bold">•</span> Reservasi dibuka hari ini hingga 30 hari ke depan.</li>
                        <li class="flex items-start gap-1.5"><span class="text-emerald-600 font-bold">•</span> Durasi booking 1 hingga 3 jam per sesi.</li>
                        <li class="flex items-start gap-1.5"><span class="text-emerald-600 font-bold">•</span> Status terkonfirmasi langsung di server.</li>
                        <li class="flex items-start gap-1.5"><span class="text-emerald-600 font-bold">•</span> Pembatalan mandiri sebelum jam mulai bermain.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} FutsalKita Arena. Seluruh hak cipta dilindungi.</p>
                <p class="text-slate-400">Professional Booking Platform • Laravel & Livewire</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
