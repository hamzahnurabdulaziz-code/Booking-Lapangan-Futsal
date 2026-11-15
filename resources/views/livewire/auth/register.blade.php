<div class="py-12 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="bg-white rounded-[26px] p-7 sm:p-9 shadow-sm border border-slate-200">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-xs">
                FK
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Akun Pelanggan</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1.5">Buat akun untuk memesan jadwal lapangan futsal tim Anda</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lengkap</label>
                <input 
                    wire:model="name" 
                    type="text" 
                    id="name" 
                    required 
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('name') border-rose-500 @enderror"
                >
                @error('name')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    required 
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('email') border-rose-500 @enderror"
                >
                @error('email')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor WhatsApp / Telepon</label>
                <input 
                    wire:model="phone" 
                    type="tel" 
                    id="phone" 
                    required 
                    placeholder="Contoh: 08123456789"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('phone') border-rose-500 @enderror"
                >
                @error('phone')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password with Show/Hide Toggle -->
            <div x-data="{ showPass: false }">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kata Sandi (Min. 8 karakter)</label>
                <div class="relative">
                    <input 
                        wire:model="password" 
                        :type="showPass ? 'text' : 'password'" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs @error('password') border-rose-500 @enderror"
                    >
                    <button 
                        type="button" 
                        @click="showPass = !showPass" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 rounded-r-xl"
                        :aria-label="showPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    >
                        <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPass" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password with Show/Hide Toggle -->
            <div x-data="{ showConfirm: false }">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Ulangi Kata Sandi</label>
                <div class="relative">
                    <input 
                        wire:model="password_confirmation" 
                        :type="showConfirm ? 'text' : 'password'" 
                        id="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-300 focus-ring text-sm text-slate-900 placeholder-slate-400 bg-white shadow-2xs"
                    >
                    <button 
                        type="button" 
                        @click="showConfirm = !showConfirm" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 rounded-r-xl"
                        :aria-label="showConfirm ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    >
                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showConfirm" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-xs focus-ring transition-all disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="register">Daftar Akun Baru</span>
                    <span wire:loading wire:target="register" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Mendaftarkan Akun...</span>
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-7 text-center text-xs text-slate-600">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
