<div class="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-[#082A20] via-[#0D3B2E] to-[#061A16] px-4 py-10">
    <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-20" aria-hidden="true">
        <defs>
            <pattern id="login-texture" width="18" height="18" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1" fill="#A7F3D0" fill-opacity="0.5" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#login-texture)" />
    </svg>
    <div class="pointer-events-none absolute -left-24 top-1/4 h-80 w-80 rounded-full bg-emerald-400/10 blur-[100px]"></div>
    <div class="pointer-events-none absolute -right-24 bottom-1/4 h-80 w-80 rounded-full bg-teal-400/10 blur-[100px]"></div>

    <div class="relative w-full max-w-md rounded-[24px] border border-slate-300/30 bg-slate-900/65 p-6 shadow-[0_24px_80px_rgba(0,0,0,0.45)] backdrop-blur-xl sm:p-8">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-emerald-400 text-[#06271D] rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-[0_0_24px_rgba(16,185,129,0.25)]">
                FK
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Masuk ke FutsalKita</h1>
            <p class="text-sm text-slate-300 mt-1">Lanjutkan untuk melihat dan mengelola booking lapangannmu</p>
        </div>

        @if($hasPendingBooking)
            <div class="mb-6 p-4 rounded-2xl bg-amber-400/10 border border-amber-300/20 text-amber-100 text-xs flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Jadwal Anda Tersimpan:</strong> Masuk sekarang untuk memverifikasi dan mengonfirmasi reservasi slot yang baru saja Anda pilih.</span>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-100 mb-2">Alamat Email</label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    autocomplete="email" 
                    required 
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-white/15 focus-ring text-sm text-white placeholder-slate-400 bg-slate-950/40 @error('email') border-rose-400 @enderror"
                >
                @error('email')
                    <p class="text-xs text-rose-300 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-100">Kata Sandi</label>
                </div>
                <input 
                    wire:model="password" 
                    type="password" 
                    id="password" 
                    autocomplete="current-password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-white/15 focus-ring text-sm text-white placeholder-slate-400 bg-slate-950/40 @error('password') border-rose-400 @enderror"
                >
                @error('password')
                    <p class="text-xs text-rose-300 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded text-[#2F6B3D] border-stone-300 focus:ring-[#2F6B3D]">
                    <span class="text-xs font-medium text-slate-300">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 text-[#052E25] text-sm font-semibold shadow-[0_8px_24px_rgba(16,185,129,0.22)] focus-ring transition-all disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-[#052E25]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Memverifikasi...</span>
                </span>
            </button>
        </form>

        <!-- Quick Demo Credentials helper for Evaluators / Team -->
        <div class="mt-8 pt-6 border-t border-white/10">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Akun Demo Pengujian</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button 
                    type="button" 
                    wire:click="$set('email', 'pelanggan@futsalkita.test'); $set('password', 'password')"
                    class="p-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/15 text-left text-slate-100 transition-colors"
                >
                    <span class="block font-semibold text-emerald-300">👤 Pelanggan</span>
                    <span class="text-[10px] text-slate-400">pelanggan@...</span>
                </button>
                <button 
                    type="button" 
                    wire:click="$set('email', 'admin@futsalkita.test'); $set('password', 'password')"
                    class="p-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/15 text-left text-slate-100 transition-colors"
                >
                    <span class="block font-semibold text-sky-300">🛡️ Admin</span>
                    <span class="text-[10px] text-slate-400">admin@...</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-slate-300">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-semibold text-emerald-300 hover:text-lime-300 hover:underline">Daftar sekarang</a>
        </div>
    </div>
</div>
