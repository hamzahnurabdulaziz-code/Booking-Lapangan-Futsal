<div class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="bg-white rounded-[24px] p-8 shadow-sm border border-[#E2E7E2]">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-[#2F6B3D] text-white rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-sm">
                FK
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[#17231B]">Daftar Akun Pelanggan</h1>
            <p class="text-sm text-[#58695C] mt-1">Buat akun untuk mulai memesan slot lapangan futsal</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">Nama Lengkap</label>
                <input 
                    wire:model="name" 
                    type="text" 
                    id="name" 
                    required 
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('name') border-rose-500 @enderror"
                >
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">Alamat Email</label>
                <input 
                    wire:model="email" 
                    type="email" 
                    id="email" 
                    required 
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('email') border-rose-500 @enderror"
                >
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">Nomor Telepon / WhatsApp</label>
                <input 
                    wire:model="phone" 
                    type="tel" 
                    id="phone" 
                    required 
                    placeholder="Contoh: 08123456789"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('phone') border-rose-500 @enderror"
                >
                @error('phone')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">Kata Sandi (Min. 8 karakter)</label>
                <input 
                    wire:model="password" 
                    type="password" 
                    id="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50 @error('password') border-rose-500 @enderror"
                >
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">Ulangi Kata Sandi</label>
                <input 
                    wire:model="password_confirmation" 
                    type="password" 
                    id="password_confirmation" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-[#17231B] placeholder-stone-400 bg-stone-50/50"
                >
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-white text-sm font-semibold shadow-sm focus-ring transition-all disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="register">Daftar Akun Baru</span>
                    <span wire:loading wire:target="register" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Mendaftarkan...</span>
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-[#58695C]">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-semibold text-[#2F6B3D] hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
