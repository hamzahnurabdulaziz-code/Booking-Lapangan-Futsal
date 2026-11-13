<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-white mb-2">
                <span>🛡️ Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#17231B] tracking-tight">Manajemen Booking Lapangan</h1>
            <p class="text-xs sm:text-sm text-[#58695C] mt-1">Filter, cari, dan verifikasi status pembatalan atau penyelesaian jadwal</p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-[24px] p-5 border border-[#E2E7E2] shadow-sm mb-8 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- Search -->
            <div class="space-y-1">
                <label for="adminSearch" class="block text-[11px] font-bold uppercase text-[#58695C]">Pencarian</label>
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        id="adminSearch" 
                        placeholder="Kode, pemesan, HP, tim..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-stone-300 text-xs text-[#17231B] bg-stone-50/50 focus-ring"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Field Filter -->
            <div class="space-y-1">
                <label for="adminFieldFilter" class="block text-[11px] font-bold uppercase text-[#58695C]">Filter Lapangan</label>
                <select wire:model.live="fieldId" id="adminFieldFilter" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs text-[#17231B] bg-stone-50/50 focus-ring">
                    <option value="all">Semua Lapangan</option>
                    @foreach($fields as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="space-y-1">
                <label for="adminStatusFilter" class="block text-[11px] font-bold uppercase text-[#58695C]">Filter Status</label>
                <select wire:model.live="status" id="adminStatusFilter" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs text-[#17231B] bg-stone-50/50 focus-ring">
                    <option value="all">Semua Status</option>
                    <option value="confirmed">Terkonfirmasi (Confirmed)</option>
                    <option value="completed">Selesai (Completed)</option>
                    <option value="cancelled">Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="space-y-1">
                <label for="adminDateFilter" class="block text-[11px] font-bold uppercase text-[#58695C]">Filter Tanggal</label>
                <div class="flex gap-2">
                    <input 
                        wire:model.live="date" 
                        type="date" 
                        id="adminDateFilter" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs text-[#17231B] bg-stone-50/50 focus-ring"
                    >
                    @if($date)
                        <button type="button" wire:click="$set('date', '')" class="px-2.5 py-1 text-xs text-stone-500 hover:text-stone-800 bg-stone-100 rounded-lg">✕</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Table Card -->
    <div class="bg-white rounded-[26px] p-6 sm:p-8 border border-[#E2E7E2] shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-[#58695C] uppercase tracking-wider font-semibold">
                        <th class="pb-3 px-3">Kode Booking</th>
                        <th class="pb-3 px-3">Pelanggan</th>
                        <th class="pb-3 px-3">Lapangan</th>
                        <th class="pb-3 px-3">Waktu Bermain</th>
                        <th class="pb-3 px-3">Tim / Catatan</th>
                        <th class="pb-3 px-3">Status</th>
                        <th class="pb-3 px-3 text-right">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($bookings as $b)
                        @php
                            $isBeforeStart = now()->lessThan($b->starts_at);
                            $isAfterEnd = now()->greaterThanOrEqualTo($b->ends_at);
                        @endphp
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-4 px-3 font-mono font-bold text-[#17231B] select-all">
                                {{ $b->booking_code }}
                            </td>
                            <td class="py-4 px-3">
                                <strong class="text-[#17231B] block font-semibold">{{ $b->user->name }}</strong>
                                <span class="text-[11px] text-stone-400 block">{{ $b->user->phone }}</span>
                                <span class="text-[10px] text-stone-400 block">{{ $b->user->email }}</span>
                            </td>
                            <td class="py-4 px-3 font-medium text-stone-800">
                                {{ $b->field->name }}
                            </td>
                            <td class="py-4 px-3">
                                <span class="block font-semibold text-[#17231B]">{{ $b->starts_at_local->format('d M Y') }}</span>
                                <span class="text-[11px] font-bold text-[#2F6B3D] block">
                                    {{ $b->starts_at_local->format('H:i') }} - {{ $b->ends_at_local->format('H:i') }} WIB
                                </span>
                            </td>
                            <td class="py-4 px-3 max-w-[180px]">
                                <span class="font-bold text-[#17231B] block truncate">{{ $b->team_name ?: '—' }}</span>
                                <span class="text-[11px] text-stone-500 block truncate">{{ $b->notes ?: 'Tidak ada catatan' }}</span>
                                @if($b->status === \App\Enums\BookingStatus::Cancelled)
                                    <span class="text-[10px] text-rose-600 block mt-0.5 truncate">Alasan: {{ $b->cancellation_reason }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $b->status->badgeClasses() }}">
                                    {{ $b->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($b->status === \App\Enums\BookingStatus::Confirmed)
                                        @if($isBeforeStart)
                                            <!-- Admin can cancel with reason -->
                                            <button 
                                                type="button" 
                                                wire:click="promptCancel({{ $b->id }})"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors focus-ring"
                                            >
                                                Batalkan
                                            </button>
                                        @endif

                                        @if($isAfterEnd)
                                            <!-- Admin can complete after ends_at -->
                                            <button 
                                                type="button" 
                                                wire:click="completeBooking({{ $b->id }})"
                                                wire:confirm="Tandai booking ini sebagai selesai?"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors focus-ring"
                                            >
                                                Selesaikan
                                            </button>
                                        @elseif(!$isBeforeStart && !$isAfterEnd)
                                            <span class="text-[11px] text-amber-600 font-semibold px-2 py-0.5 bg-amber-50 rounded">
                                                Sedang Berlangsung
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[11px] text-stone-400 italic">Selesai/Batal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">Tidak ada booking yang sesuai dengan kriteria filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- ADMIN CANCELLATION MODAL (REASON MANDATORY) -->
    @if($showCancelModal && $bookingToCancel)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="$wire.closeCancelModal()"
        >
            <div class="bg-white rounded-[26px] max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-5">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl mx-auto">
                    ⚠️
                </div>

                <div class="text-center">
                    <h3 class="text-lg font-bold text-[#17231B]">Batalkan Booking (Admin)</h3>
                    <p class="text-xs text-[#58695C] mt-1.5 leading-relaxed">
                        Anda akan membatalkan booking <strong class="text-[#17231B]">{{ $bookingToCancel->booking_code }}</strong> untuk pelanggan <strong class="text-[#17231B]">{{ $bookingToCancel->user->name }}</strong>.
                    </p>
                </div>

                <form wire:submit="submitCancel" class="space-y-4">
                    <div>
                        <label for="cancelReasonInput" class="block text-xs font-semibold uppercase tracking-wider text-[#17231B] mb-1.5">
                            Alasan Pembatalan <span class="text-rose-600">* Wajib Diisi</span>
                        </label>
                        <textarea 
                            wire:model="cancellationReason" 
                            id="cancelReasonInput" 
                            rows="3" 
                            required 
                            maxlength="500"
                            placeholder="Contoh: Perbaikan darurat lampu lapangan / Maintenance mendadak..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50 @error('cancellationReason') border-rose-500 @enderror"
                        ></textarea>
                        @error('cancellationReason')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closeCancelModal"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-bold"
                        >
                            Tutup
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="submitCancel">Konfirmasi Batalkan</span>
                            <span wire:loading wire:target="submitCancel">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
