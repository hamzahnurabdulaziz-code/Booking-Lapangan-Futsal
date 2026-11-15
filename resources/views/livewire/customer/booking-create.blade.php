<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    @if($confirmedBooking)
        <!-- SUCCESS STATE / HALAMAN BERHASIL -->
        <div class="bg-white rounded-[28px] p-8 sm:p-12 border border-stone-200/90 shadow-sm text-center space-y-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-3xl mx-auto shadow-sm">
                ✓
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    Status: Terkonfirmasi
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mt-3">Booking Berhasil Disimpan!</h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-md mx-auto leading-relaxed">
                    Jadwal lapangan tim Anda telah dikunci di sistem. Tunjukkan kode booking ini kepada petugas saat tiba di FutsalKita Arena.
                </p>
            </div>

            <!-- Booking Code Box -->
            <div class="p-6 rounded-2xl bg-[#F8FAF8] border border-stone-200 max-w-md mx-auto">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-stone-500">Kode Booking Resmi</span>
                <span class="block text-2xl sm:text-3xl font-black text-emerald-800 tracking-widest mt-1 select-all font-mono">
                    {{ $confirmedBooking->booking_code }}
                </span>
            </div>

            <!-- Detail Grid -->
            <div class="max-w-md mx-auto border-t border-b border-stone-100 py-4 space-y-2.5 text-xs text-left">
                <div class="flex justify-between">
                    <span class="text-stone-500">Lapangan:</span>
                    <strong class="text-stone-900">{{ $confirmedBooking->field->name }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Waktu Bermain:</span>
                    <strong class="text-stone-900">
                        {{ $confirmedBooking->starts_at_local->translatedFormat('d M Y') }}, {{ $confirmedBooking->starts_at_local->format('H:i') }} – {{ $confirmedBooking->ends_at_local->format('H:i') }} WIB
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Total Tarif:</span>
                    <strong class="text-emerald-800 font-bold text-sm">{{ $confirmedBooking->formatted_total_price }}</strong>
                </div>
                @if($confirmedBooking->team_name)
                    <div class="flex justify-between">
                        <span class="text-stone-500">Nama Tim:</span>
                        <strong class="text-stone-900">{{ $confirmedBooking->team_name }}</strong>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-stone-500">Pemesan:</span>
                    <strong class="text-stone-900">{{ $confirmedBooking->user->name }} ({{ $confirmedBooking->user->phone }})</strong>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
                <a 
                    href="{{ route('customer.bookings') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition-colors focus-ring"
                >
                    Lihat di Riwayat Booking Saya
                </a>
                <a 
                    href="{{ route('home') }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl border border-stone-300 hover:bg-stone-50 text-stone-700 text-xs font-bold transition-colors focus-ring"
                >
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @else
        <!-- SUMMARY & CONFIRMATION FORM -->
        <div class="bg-white rounded-[28px] p-6 sm:p-10 border border-stone-200/90 shadow-sm space-y-8">
            <div class="border-b border-stone-100 pb-5">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Konfirmasi Terakhir</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight mt-1">Ringkasan Booking Lapangan</h1>
                <p class="text-xs sm:text-sm text-stone-600 mt-1">Periksa kembali detail jadwal sebelum mengonfirmasi reservasi tim Anda.</p>
            </div>

            <!-- Conflict / Error Alert -->
            @error('conflict')
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong class="font-bold block mb-0.5">Jadwal Bertabrakan!</strong>
                        <span>{{ $message }}</span>
                        <div class="mt-2">
                            <a href="{{ route('fields.show', $field->slug) }}" class="underline font-bold text-rose-700">Pilih Slot Waktu Lain &rarr;</a>
                        </div>
                    </div>
                </div>
            @enderror

            @error('field_id')
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs">
                    {{ $message }}
                </div>
            @enderror

            <!-- Schedule & Pricing Summary Card -->
            @if($field)
                @php
                    $calcTotal = (int) ($field->price_per_hour ?? 150000) * $durationHours;
                @endphp
                <div class="p-5 rounded-2xl bg-[#F8FAF8] border border-stone-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-stone-900">{{ $field->name }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-stone-200 text-stone-700">
                            {{ ucfirst($field->type) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-stone-600 pt-3 border-t border-stone-200/70">
                        <div>
                            <span class="text-stone-500">Tanggal:</span>
                            <strong class="text-stone-900 block font-semibold">
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                            </strong>
                        </div>
                        <div>
                            <span class="text-stone-500">Jam & Durasi:</span>
                            <strong class="text-emerald-700 block font-semibold">
                                {{ sprintf('%02d:00 – %02d:00 WIB', $startHour, $endHour) }} ({{ $durationHours }} Jam)
                            </strong>
                        </div>
                        <div>
                            <span class="text-stone-500">Total Tarif:</span>
                            <strong class="text-emerald-800 block font-black text-sm">
                                Rp {{ number_format($calcTotal, 0, ',', '.') }}
                            </strong>
                            <span class="text-[10px] text-stone-400">({{ $durationHours }} × {{ $field->formatted_price }})</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form wire:submit="submitBooking" class="space-y-6">
                <!-- User Profile Meta (Non-editable, server verified) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs">
                    <div>
                        <span class="text-stone-500 block">Nama Pemesan:</span>
                        <strong class="text-stone-900 font-semibold">{{ auth()->user()->name }}</strong>
                    </div>
                    <div>
                        <span class="text-stone-500 block">Email & No. HP:</span>
                        <strong class="text-stone-900 font-semibold">{{ auth()->user()->email }} • {{ auth()->user()->phone }}</strong>
                    </div>
                </div>

                <!-- Team Name Input -->
                <div>
                    <label for="teamName" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">
                        Nama Tim <span class="text-stone-400 font-normal">(Opsional, maks. 100 karakter)</span>
                    </label>
                    <input 
                        wire:model="teamName" 
                        type="text" 
                        id="teamName" 
                        maxlength="100"
                        placeholder="Contoh: Garuda Muda FC"
                        class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-stone-900 placeholder-stone-400 bg-stone-50/50 @error('teamName') border-rose-500 @enderror"
                    >
                    @error('teamName')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes Input -->
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1.5">
                        Catatan Khusus <span class="text-stone-400 font-normal">(Opsional, maks. 500 karakter)</span>
                    </label>
                    <textarea 
                        wire:model="notes" 
                        id="notes" 
                        rows="3"
                        maxlength="500"
                        placeholder="Contoh: Tolong siapkan rompi latihan warna biru atau bola tambahan..."
                        class="w-full px-4 py-3 rounded-xl border border-stone-300 focus-ring text-sm text-stone-900 placeholder-stone-400 bg-stone-50/50 @error('notes') border-rose-500 @enderror"
                    ></textarea>
                    @error('notes')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms reminder -->
                <div class="p-4 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-xs text-emerald-950 space-y-1">
                    <div class="font-bold">Ketentuan Reservasi FutsalKita:</div>
                    <div>• Booking langsung berstatus Terkonfirmasi tanpa persetujuan manual admin.</div>
                    <div>• Jika berhalangan, Anda dapat membatalkan booking sebelum waktu mulai melalui menu riwayat booking.</div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('fields.show', $field->slug ?? '') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
                        &larr; Ubah Tanggal / Jam
                    </a>

                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-md focus-ring transition-all disabled:opacity-60 flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="submitBooking">Konfirmasi & Simpan Booking</span>
                        <span wire:loading wire:target="submitBooking" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Mengunci Jadwal...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
