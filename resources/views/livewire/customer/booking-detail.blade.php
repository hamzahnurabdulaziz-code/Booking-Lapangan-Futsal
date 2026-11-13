<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-xs font-medium text-[#58695C]">
        <a href="{{ route('home') }}" class="hover:text-[#2F6B3D]">Beranda</a>
        <span>/</span>
        <a href="{{ route('customer.bookings') }}" class="hover:text-[#2F6B3D]">Booking Saya</a>
        <span>/</span>
        <span class="text-[#17231B] font-semibold">{{ $booking->booking_code }}</span>
    </nav>

    <div class="bg-white rounded-[28px] p-6 sm:p-10 border border-[#E2E7E2] shadow-sm space-y-8">
        <!-- Top Title Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#2F6B3D]">Detail Reservasi</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#17231B] tracking-tight mt-0.5 font-mono">
                    {{ $booking->booking_code }}
                </h1>
                <span class="text-xs text-stone-400 mt-1 block">
                    Dibuat pada {{ $booking->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                </span>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $booking->status->badgeClasses() }}">
                    <span class="w-2 h-2 rounded-full {{ $booking->status === \App\Enums\BookingStatus::Confirmed ? 'bg-emerald-500' : ($booking->status === \App\Enums\BookingStatus::Completed ? 'bg-blue-500' : 'bg-rose-500') }}"></span>
                    <span>Status: {{ $booking->status->label() }}</span>
                </span>
            </div>
        </div>

        <!-- Field & Venue Section -->
        <div class="p-5 rounded-2xl bg-[#F7F7F2] border border-[#E2E7E2] space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#17231B]">{{ $booking->field->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase bg-stone-200 text-stone-700">
                    {{ ucfirst($booking->field->type) }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-[#58695C] pt-2 border-t border-stone-200/70">
                <div>
                    <span>Jadwal Main:</span>
                    <strong class="text-[#17231B] block text-sm font-semibold mt-0.5">
                        {{ $booking->starts_at_local->translatedFormat('l, d F Y') }}
                    </strong>
                    <span class="text-[#2F6B3D] font-bold block mt-0.5">
                        {{ $booking->starts_at_local->format('H:i') }} – {{ $booking->ends_at_local->format('H:i') }} WIB
                    </span>
                </div>
                <div>
                    <span>Lokasi Arena:</span>
                    <strong class="text-[#17231B] block font-medium mt-0.5">{{ config('futsal.venue_address') }}</strong>
                </div>
            </div>
        </div>

        <!-- Team & Notes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl border border-stone-200 bg-stone-50/50">
                <span class="text-[#58695C] block mb-1">Nama Tim:</span>
                <strong class="text-sm text-[#17231B]">{{ $booking->team_name ?: 'Tidak dicantumkan' }}</strong>
            </div>

            <div class="p-4 rounded-2xl border border-stone-200 bg-stone-50/50">
                <span class="text-[#58695C] block mb-1">Catatan Tambahan:</span>
                <p class="text-xs text-[#17231B]">{{ $booking->notes ?: 'Tidak ada catatan khusus' }}</p>
            </div>
        </div>

        @if($booking->status === \App\Enums\BookingStatus::Cancelled)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                <strong class="block font-bold">Informasi Pembatalan:</strong>
                <div>Alasan: {{ $booking->cancellation_reason ?: 'Dibatalkan oleh pelanggan' }}</div>
                @if($booking->cancelled_at)
                    <div class="text-[11px] text-rose-600">Waktu Pembatalan: {{ $booking->cancelled_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</div>
                @endif
            </div>
        @endif

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('customer.bookings') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
                &larr; Kembali ke Riwayat
            </a>

            @if($booking->canBeCancelledBy(auth()->user()))
                <button 
                    type="button" 
                    wire:click="promptCancel"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors focus-ring"
                >
                    Batalkan Reservasi Ini
                </button>
            @endif
        </div>
    </div>

    <!-- Cancellation Confirmation Modal -->
    @if($confirmingCancel)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="$wire.set('confirmingCancel', false)"
        >
            <div class="bg-white rounded-[24px] max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-5">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mx-auto">
                    ⚠️
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold text-[#17231B]">Konfirmasi Pembatalan</h3>
                    <p class="text-xs text-[#58695C] mt-2">
                        Apakah Anda yakin ingin membatalkan jadwal ini? Slot waktu akan langsung terbuka kembali bagi pengguna lain.
                    </p>
                </div>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        wire:click="$set('confirmingCancel', false)"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-bold"
                    >
                        Tutup
                    </button>
                    <button 
                        type="button" 
                        wire:click="cancelBooking"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="cancelBooking">Ya, Batalkan</span>
                        <span wire:loading wire:target="cancelBooking">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
