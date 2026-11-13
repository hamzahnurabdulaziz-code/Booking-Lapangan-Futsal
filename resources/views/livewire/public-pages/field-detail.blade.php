<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-xs font-medium text-[#58695C]" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#2F6B3D]">Beranda</a>
        <span>/</span>
        <a href="{{ route('fields.index') }}" class="hover:text-[#2F6B3D]">Daftar Lapangan</a>
        <span>/</span>
        <span class="text-[#17231B] font-semibold truncate">{{ $field->name }}</span>
    </nav>

    <!-- Top Banner & Details -->
    <div class="bg-white rounded-[28px] overflow-hidden border border-[#E2E7E2] shadow-sm mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            <!-- Field Image -->
            <div class="lg:col-span-5 relative h-64 sm:h-80 lg:h-full min-h-[300px] bg-stone-100">
                <img 
                    src="{{ asset($field->image_path ?: 'images/placeholder-field.svg') }}" 
                    alt="{{ $field->name }}" 
                    class="w-full h-full object-cover"
                    onerror="this.onerror=null; this.src='{{ asset('images/placeholder-field.svg') }}';"
                >
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $field->type === 'indoor' ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white' }} shadow-sm">
                        {{ ucfirst($field->type) }}
                    </span>
                    @if(!$field->is_active)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Field Meta -->
            <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-between space-y-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#17231B] tracking-tight">
                        {{ $field->name }}
                    </h1>
                    <p class="text-sm text-[#58695C] mt-3 leading-relaxed">
                        {{ $field->description }}
                    </p>
                </div>

                <!-- Facilities -->
                @if(!empty($field->facilities))
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#17231B] mb-3">Fasilitas Arena:</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-[#58695C]">
                            @foreach($field->facilities as $fac)
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0">✓</span>
                                    <span>{{ $fac }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-4 border-t border-stone-100 flex flex-wrap items-center justify-between text-xs text-stone-500 gap-2">
                    <div>📍 Lokasi: {{ config('futsal.venue_address') }}</div>
                    <div>🕒 Jam Operasional: 08.00 – 23.00 WIB</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule & Slot Selection Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left: Calendar & Slots -->
        <div class="lg:col-span-8 space-y-8">
            <!-- 1. Date & Duration Controls -->
            <div class="bg-white rounded-[24px] p-6 border border-[#E2E7E2] shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-5">
                    <div>
                        <h2 class="text-lg font-bold text-[#17231B]">Pilih Jadwal Main</h2>
                        <p class="text-xs text-[#58695C] mt-0.5">Tentukan tanggal dan durasi untuk melihat ketersediaan slot</p>
                    </div>

                    <!-- Duration Chips -->
                    <div class="flex items-center gap-1.5 bg-stone-100 p-1 rounded-2xl">
                        <span class="text-xs font-bold text-stone-500 px-2 hidden sm:inline">Durasi:</span>
                        @foreach([1, 2, 3] as $d)
                            <button 
                                type="button" 
                                wire:click="setDuration({{ $d }})"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all focus-ring {{ $duration === $d ? 'bg-[#2F6B3D] text-white shadow-sm' : 'text-stone-700 hover:text-black' }}"
                            >
                                {{ $d }} Jam
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Date Picker & Quick Days -->
                @php
                    $tz = config('futsal.timezone', 'Asia/Jakarta');
                    $today = \Carbon\Carbon::now($tz);
                    $quickDates = [
                        ['label' => 'Hari Ini', 'date' => $today->toDateString(), 'sub' => $today->translatedFormat('d M')],
                        ['label' => 'Besok', 'date' => $today->copy()->addDay()->toDateString(), 'sub' => $today->copy()->addDay()->translatedFormat('d M')],
                        ['label' => 'Lusa', 'date' => $today->copy()->addDays(2)->toDateString(), 'sub' => $today->copy()->addDays(2)->translatedFormat('d M')],
                    ];
                @endphp

                <div class="space-y-3">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#58695C]">Pilih Tanggal (Maks. 30 Hari ke Depan):</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach($quickDates as $qd)
                            <button 
                                type="button" 
                                wire:click="$set('date', '{{ $qd['date'] }}')"
                                class="p-3 rounded-2xl border text-center transition-all focus-ring {{ $date === $qd['date'] ? 'border-[#2F6B3D] bg-[#EBF3ED] text-[#2F6B3D] font-bold shadow-xs' : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-medium' }}"
                            >
                                <span class="block text-xs">{{ $qd['label'] }}</span>
                                <span class="block text-[11px] opacity-75 mt-0.5">{{ $qd['sub'] }}</span>
                            </button>
                        @endforeach

                        <div class="relative">
                            <input 
                                wire:model.live="date" 
                                type="date" 
                                min="{{ $today->toDateString() }}"
                                max="{{ $today->copy()->addDays(30)->toDateString() }}"
                                class="w-full h-full px-3 py-2 rounded-2xl border border-stone-200 bg-stone-50 text-xs font-semibold text-[#17231B] focus-ring"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Slot Grid -->
            <div class="bg-white rounded-[24px] p-6 border border-[#E2E7E2] shadow-sm">
                <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-[#17231B]">Daftar Slot Waktu (Durasi {{ $duration }} Jam)</h3>
                        <p class="text-xs text-[#58695C]">Klik slot waktu hijau yang tersedia untuk memilih jam bermain</p>
                    </div>
                    <!-- Legend -->
                    <div class="hidden sm:flex items-center gap-3 text-[11px] font-medium text-stone-600">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span> Tersedia</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-stone-300"></span> Terisi / Lewat</span>
                    </div>
                </div>

                @if(!$field->is_active)
                    <div class="p-8 text-center bg-rose-50 rounded-2xl border border-rose-200 text-rose-800">
                        <svg class="w-10 h-10 mx-auto mb-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <h4 class="font-bold text-sm">Lapangan Sedang Dinonaktifkan</h4>
                        <p class="text-xs mt-1">Lapangan ini sedang dalam pemeliharaan dan belum dapat menerima reservasi baru.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($slots as $slot)
                            @php
                                $isSelected = ($hour === $slot['start_hour']);
                            @endphp

                            @if($slot['is_available'])
                                <button 
                                    type="button" 
                                    wire:click="selectSlot({{ $slot['start_hour'] }})"
                                    class="p-3.5 rounded-2xl border-2 text-left transition-all focus-ring {{ $isSelected ? 'border-[#2F6B3D] bg-[#2F6B3D] text-white shadow-md scale-[1.02]' : 'border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100 hover:border-emerald-400 text-emerald-950' }}"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold tracking-tight">{{ $slot['label'] }}</span>
                                        @if($isSelected)
                                            <span class="text-xs font-bold text-white">✓ Dipilih</span>
                                        @else
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100/80 px-1.5 py-0.5 rounded">Tersedia</span>
                                        @endif
                                    </div>
                                    <span class="block text-[10px] opacity-80">Waktu Indonesia Barat</span>
                                </button>
                            @else
                                <div class="p-3.5 rounded-2xl border border-stone-200 bg-stone-100/70 text-stone-400 cursor-not-allowed">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold">{{ $slot['label'] }}</span>
                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-stone-200 text-stone-600">
                                            {{ $slot['is_past'] ? 'Lewat' : 'Terisi' }}
                                        </span>
                                    </div>
                                    <span class="block text-[10px] text-stone-400">{{ $slot['reason'] ?? 'Tidak tersedia' }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Sticky Booking Summary Card -->
        <div class="lg:col-span-4 sticky top-28">
            <div class="bg-white rounded-[26px] p-6 border border-[#E2E7E2] shadow-sm space-y-6">
                <div class="border-b border-stone-100 pb-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#2F6B3D]">Ringkasan Pilihan</span>
                    <h3 class="text-lg font-bold text-[#17231B] mt-0.5">Detail Reservasi</h3>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-[#58695C]">Lapangan:</span>
                        <strong class="text-[#17231B] font-semibold text-right">{{ $field->name }}</strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-[#58695C]">Tipe Arena:</span>
                        <span class="font-semibold text-stone-800 uppercase text-[11px]">{{ ucfirst($field->type) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-[#58695C]">Tanggal Main:</span>
                        <strong class="text-[#17231B] font-semibold">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                        </strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-[#58695C]">Durasi Sewa:</span>
                        <strong class="text-[#17231B] font-semibold">{{ $duration }} Jam</strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-[#58695C]">Jam Mulai – Selesai:</span>
                        @if($hour !== null && $selectedSlotInfo)
                            <strong class="text-[#2F6B3D] font-bold text-sm">
                                {{ $selectedSlotInfo['label'] }} WIB
                            </strong>
                        @else
                            <span class="text-amber-600 font-medium italic">Belum memilih slot</span>
                        @endif
                    </div>
                </div>

                @if(!$field->is_active)
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-800 text-xs">
                        Lapangan saat ini dinonaktifkan.
                    </div>
                @elseif($hour === null)
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-800 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Pilih salah satu slot hijau untuk melanjutkan.</span>
                    </div>
                @endif

                <button 
                    type="button" 
                    wire:click="proceedToBooking"
                    @disabled(!$field->is_active || $hour === null)
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-white text-sm font-semibold shadow-md focus-ring transition-all disabled:opacity-40 disabled:cursor-not-allowed group"
                >
                    @auth
                        <span>Lanjut ke Ringkasan Booking</span>
                    @else
                        <span>Masuk untuk Konfirmasi</span>
                    @endauth
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <p class="text-[11px] text-stone-400 text-center leading-relaxed">
                    Reservasi langsung terkonfirmasi di server. Tidak ada biaya atau tarif di sistem MVP ini.
                </p>
            </div>
        </div>
    </div>
</div>
