<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Header -->
    <div class="mb-8">
        <span class="text-xs font-bold uppercase tracking-wider text-[#2F6B3D]">Area Pelanggan</span>
        <h1 class="text-3xl font-extrabold text-[#17231B] tracking-tight mt-1">Riwayat Booking Lapangan</h1>
        <p class="text-xs sm:text-sm text-[#58695C] mt-1">Kelola dan pantau seluruh jadwal bermain tim Anda</p>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-[22px] p-4 border border-[#E2E7E2] shadow-sm mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
            <button 
                type="button" 
                wire:click="$set('tab', 'upcoming')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'upcoming' ? 'bg-[#2F6B3D] text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                Mendatang
            </button>
            <button 
                type="button" 
                wire:click="$set('tab', 'completed')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'completed' ? 'bg-[#2F6B3D] text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                Selesai
            </button>
            <button 
                type="button" 
                wire:click="$set('tab', 'cancelled')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'cancelled' ? 'bg-[#2F6B3D] text-white shadow-sm' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}"
            >
                Dibatalkan
            </button>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full sm:w-72">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari kode booking..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-stone-300 text-xs text-[#17231B] placeholder-stone-400 bg-stone-50/50 focus-ring"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Booking List -->
    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bg-white rounded-[24px] p-6 border border-[#E2E7E2] shadow-sm hover:border-[#2F6B3D]/30 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#EBF3ED] text-[#2F6B3D] flex items-center justify-center font-bold text-sm shrink-0">
                            FK
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-[#58695C] uppercase tracking-wider block">Kode Booking</span>
                            <span class="text-base font-extrabold text-[#17231B] tracking-wide font-mono">{{ $booking->booking_code }}</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $booking->status->badgeClasses() }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $booking->status === \App\Enums\BookingStatus::Confirmed ? 'bg-emerald-500' : ($booking->status === \App\Enums\BookingStatus::Completed ? 'bg-blue-500' : 'bg-rose-500') }}"></span>
                            <span>{{ $booking->status->label() }}</span>
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-[#58695C] block">Lapangan:</span>
                        <strong class="text-[#17231B] text-sm">{{ $booking->field->name }}</strong>
                        <span class="text-[11px] text-stone-400 block mt-0.5">{{ ucfirst($booking->field->type) }}</span>
                    </div>

                    <div>
                        <span class="text-[#58695C] block">Jadwal Bermain:</span>
                        <strong class="text-[#17231B]">
                            {{ $booking->starts_at_local->translatedFormat('l, d M Y') }}
                        </strong>
                        <span class="text-[#2F6B3D] font-bold block mt-0.5">
                            {{ $booking->starts_at_local->format('H:i') }} – {{ $booking->ends_at_local->format('H:i') }} WIB
                        </span>
                    </div>

                    <div>
                        <span class="text-[#58695C] block">Nama Tim & Catatan:</span>
                        <strong class="text-[#17231B] block truncate">
                            {{ $booking->team_name ?: '—' }}
                        </strong>
                        <span class="text-stone-500 block truncate mt-0.5">
                            {{ $booking->notes ?: 'Tidak ada catatan khusus' }}
                        </span>
                    </div>
                </div>

                <!-- Cancellation Info if Cancelled -->
                @if($booking->status === \App\Enums\BookingStatus::Cancelled)
                    <div class="mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                        <strong>Dibatalkan:</strong> {{ $booking->cancellation_reason ?: 'Dibatalkan oleh pemesan' }}
                        @if($booking->cancelled_at)
                            <span class="text-[11px] text-rose-600 block mt-0.5">Waktu batal: {{ $booking->cancelled_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</span>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between">
                    <a 
                        href="{{ route('customer.bookings.show', $booking->id) }}" 
                        class="text-xs font-bold text-[#2F6B3D] hover:text-[#245430] flex items-center gap-1 group"
                    >
                        <span>Lihat Detail Lengkap</span>
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    @if($booking->canBeCancelledBy(auth()->user()))
                        <button 
                            type="button" 
                            wire:click="promptCancel({{ $booking->id }})"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors focus-ring"
                        >
                            Batalkan Booking
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-[24px] border border-dashed border-stone-300">
                <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 class="text-sm font-bold text-[#17231B]">Tidak ada data booking pada tab ini</h3>
                <p class="text-xs text-stone-500 mt-1">Anda belum memiliki jadwal futsal di kategori ini.</p>
                <a href="{{ route('fields.index') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#2F6B3D] text-white hover:bg-[#245430]">
                    Pesan Lapangan Sekarang
                </a>
            </div>
        @endforelse
    </div>

    <!-- ACCESSIBLE CANCELLATION MODAL -->
    @if($confirmingCancel && $bookingToCancel)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
            @keydown.escape.window="$wire.closeCancelModal()"
        >
            <div class="bg-white rounded-[24px] max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-5 animate-in fade-in duration-200">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mx-auto">
                    ⚠️
                </div>

                <div class="text-center">
                    <h3 id="modal-title" class="text-lg font-bold text-[#17231B]">Batalkan Reservasi?</h3>
                    <p class="text-xs text-[#58695C] mt-2 leading-relaxed">
                        Anda akan membatalkan booking <strong class="text-[#17231B]">{{ $bookingToCancel->booking_code }}</strong> untuk lapangan <strong class="text-[#17231B]">{{ $bookingToCancel->field->name }}</strong> pada jadwal {{ $bookingToCancel->starts_at_local->format('d M Y H:i') }} WIB.
                    </p>
                    <p class="text-[11px] text-rose-600 mt-2 font-medium">
                        Slot ini akan dilepas dan dapat segera dipesan oleh tim lain. Tindakan ini tidak dapat diulang.
                    </p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        wire:click="closeCancelModal"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-50 text-xs font-bold focus-ring"
                    >
                        Kembali
                    </button>
                    <button 
                        type="button" 
                        wire:click="cancelBooking"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm focus-ring disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="cancelBooking">Ya, Batalkan Booking</span>
                        <span wire:loading wire:target="cancelBooking">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
