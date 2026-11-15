<div class="py-12 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="bg-white rounded-[26px] p-7 sm:p-9 shadow-sm border border-slate-200">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-xs">
                FK
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke FutsalKita</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1.5">Lanjutkan untuk melihat jadwal dan mengelola booking tim Anda</p>
        </div>

        @if($hasPendingBooking)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs flex items-start gap-2.5 shadow-xs">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Jadwal Anda Tersimpan:</strong> Masuk sekarang untuk memverifikasi dan menyelesaikan reservasi slot yang baru Anda pilih.</span>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alamat Email</label>
                <div class="relative">
                    <input 
                        wire:model="email" 
                        type="email" 
                        id="email" 
                        autocomplete="email" 
                        required 
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('email') border-rose-500 @enderror"
                    >
                </div>
                @error('email')
                    <p class="text-xs text-rose-600 mt-1.5 flex items-center gap-1 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Password Field with Show/Hide Toggle -->
            <div x-data="{ showPassword: false }">
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kata Sandi</label>
                    <a href="#forgot" @click.prevent="alert('Silakan hubungi administrator arena atau petugas FutsalKita untuk mereset kata sandi akun Anda.')" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                        Lupa kata sandi?
                    </a>
                </div>
                <div class="relative">
                    <input 
                        wire:model="password" 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        autocomplete="current-password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('password') border-rose-500 @enderror"
                    >
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 rounded-r-xl"
                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        :aria-pressed="showPassword"
                    >
                        <!-- Eye Icon (Show) -->
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <!-- Eye Off Icon (Hide) -->
                        <svg x-show="showPassword" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-rose-600 mt-1.5 flex items-center gap-1 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-slate-700">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-xs focus-ring transition-all disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Memverifikasi Akun...</span>
                    </span>
                </button>
            </div>
        </form>

        <!-- Development Demo Credentials Helper (Local Environment Only) -->
        @if(app()->environment('local'))
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Akun Pengujian Cepat (Mode Demo)</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button 
                        type="button" 
                        wire:click="$set('email', 'pelanggan@futsalkita.test'); $set('password', 'password')"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 text-left transition-colors"
                    >
                        <span class="block font-bold text-emerald-800">👤 Pelanggan</span>
                        <span class="text-[10px] text-slate-500 truncate block">pelanggan@...</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="$set('email', 'admin@futsalkita.test'); $set('password', 'password')"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 text-left transition-colors"
                    >
                        <span class="block font-bold text-slate-900">🛡️ Admin</span>
                        <span class="text-[10px] text-slate-500 truncate block">admin@...</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="mt-7 text-center text-xs text-slate-600">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Daftar sekarang</a>
        </div>
    </div>
</div>
