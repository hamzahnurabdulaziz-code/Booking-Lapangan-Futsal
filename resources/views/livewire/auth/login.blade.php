<div class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="bg-white rounded-[24px] p-8 shadow-sm border border-[#E2E7E2]">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-[#2F6B3D] text-white rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-sm">
                FK
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#17231B]">Masuk ke FutsalKita</h1>
            <p class="text-sm text-[#58695C] mt-1">Lanjutkan untuk melihat dan mengelola booking lapangannmu</p>
        </div>

        @if($hasPendingBooking)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><strong>Jadwal Anda Tersimpan:</strong> Masuk sekarang untuk memverifikasi dan mengonfirmasi reservasi slot yang baru saja Anda pilih.</span>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-2">Alamat Email</label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    autocomplete="email" 
                    required 
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('email') border-rose-500 @enderror"
                >
                @error('email')
                    <p class="text-xs text-rose-600 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B]">Kata Sandi</label>
                </div>
                <input 
                    wire:model="password" 
                    type="password" 
                    id="password" 
                    autocomplete="current-password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('password') border-rose-500 @enderror"
                >
                @error('password')
                    <p class="text-xs text-rose-600 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded text-[#2F6B3D] border-stone-300 focus:ring-[#2F6B3D]">
                    <span class="text-xs font-medium text-[#58695C]">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-white text-sm font-semibold shadow-sm focus-ring transition-all disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Memverifikasi...</span>
                </span>
            </button>
        </form>

        <!-- Quick Demo Credentials helper for Evaluators / Team -->
        <div class="mt-8 pt-6 border-t border-stone-100">
            <p class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider mb-2 text-center">Akun Demo Pengujian</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button 
                    type="button" 
                    wire:click="$set('email', 'pelanggan@futsalkita.test'); $set('password', 'password')"
                    class="p-2 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200 text-left text-stone-700 transition-colors"
                >
                    <span class="block font-semibold text-[#2F6B3D]">👤 Pelanggan</span>
                    <span class="text-[10px] text-stone-500">pelanggan@...</span>
                </button>
                <button 
                    type="button" 
                    wire:click="$set('email', 'admin@futsalkita.test'); $set('password', 'password')"
                    class="p-2 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200 text-left text-stone-700 transition-colors"
                >
                    <span class="block font-semibold text-stone-900">🛡️ Admin</span>
                    <span class="text-[10px] text-stone-500">admin@...</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-[#58695C]">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-semibold text-[#2F6B3D] hover:underline">Daftar sekarang</a>
        </div>
    </div>
</div>
