<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-xs font-semibold text-stone-500" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-700">Beranda</a>
        <span>/</span>
        <a href="{{ route('fields.index') }}" class="hover:text-emerald-700">Daftar Lapangan</a>
        <span>/</span>
        <span class="text-stone-900 font-bold truncate">{{ $field->name }}</span>
    </nav>

    <!-- Top Banner & Details -->
    <div class="bg-white rounded-[28px] overflow-hidden border border-stone-200/90 shadow-sm mb-10">
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
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $field->type === 'indoor' ? 'bg-indigo-700 text-white' : 'bg-emerald-700 text-white' }} shadow-md">
                        {{ ucfirst($field->type) }}
                    </span>
                    @if(!$field->is_active)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-md">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <!-- Price Tag on Image -->
                <div class="absolute bottom-4 right-4 bg-stone-950/85 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10 text-right shadow-lg">
                    <span class="block text-[10px] uppercase font-bold text-emerald-300 tracking-wider">Tarif Sewa</span>
                    <span class="block text-base font-extrabold text-white">{{ $field->formatted_price }} <span class="text-xs font-normal text-stone-300">/ jam</span></span>
                </div>
            </div>

            <!-- Field Meta -->
            <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-between space-y-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        {{ $field->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-600 mt-3 leading-relaxed">
                        {{ $field->description }}
                    </p>
                </div>

                <!-- Facilities -->
                @if(!empty($field->facilities))
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-stone-900 mb-3">Fasilitas Lapangan:</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-stone-700">
                            @foreach($field->facilities as $fac)
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[10px] shrink-0">✓</span>
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
            <div class="bg-white rounded-[26px] p-6 border border-stone-200/90 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-5">
                    <div>
                        <h2 class="text-lg font-bold text-stone-900">Pilih Jadwal Main</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Tentukan tanggal dan durasi untuk memeriksa ketersediaan slot</p>
                    </div>

                    <!-- Duration Chips -->
                    <div class="flex items-center gap-1.5 bg-stone-100 p-1.5 rounded-2xl">
                        <span class="text-xs font-bold text-stone-500 px-2 hidden sm:inline">Durasi:</span>
                        @foreach([1, 2, 3] as $d)
                            <button 
                                type="button" 
                                wire:click="setDuration({{ $d }})"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all focus-ring {{ $duration === $d ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-700 hover:text-stone-950' }}"
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600">Pilih Tanggal (Maks. 30 Hari ke Depan):</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach($quickDates as $qd)
                            <button 
                                type="button" 
                                wire:click="$set('date', '{{ $qd['date'] }}')"
                                class="p-3 rounded-2xl border text-center transition-all focus-ring {{ $date === $qd['date'] ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-bold shadow-xs' : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-semibold' }}"
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
                                class="w-full h-full px-3 py-2 rounded-2xl border border-stone-200 bg-stone-50 text-xs font-bold text-stone-900 focus-ring"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Slot Grid -->
            <div class="bg-white rounded-[26px] p-6 border border-stone-200/90 shadow-sm">
                <div class="flex items-center justify-between mb-4 border-b border-stone-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-stone-900">Daftar Slot Waktu (Durasi {{ $duration }} Jam)</h3>
                        <p class="text-xs text-stone-500">Pilih salah satu slot hijau yang masih tersedia</p>
                    </div>
                    <!-- Legend -->
                    <div class="hidden sm:flex items-center gap-3 text-[11px] font-medium text-stone-600">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600"></span> Tersedia</span>
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
                                    class="p-3.5 rounded-2xl border-2 text-left transition-all focus-ring {{ $isSelected ? 'border-emerald-700 bg-emerald-700 text-white shadow-md scale-[1.02]' : 'border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100 hover:border-emerald-400 text-emerald-950' }}"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold tracking-tight">{{ $slot['label'] }}</span>
                                        @if($isSelected)
                                            <span class="text-xs font-bold text-white">✓ Dipilih</span>
                                        @else
                                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Tersedia</span>
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

        <!-- Right: Sticky Booking Summary Card with Pricing Calculation -->
        <div class="lg:col-span-4 sticky top-28">
            <div class="bg-white rounded-[26px] p-6 border border-stone-200/90 shadow-sm space-y-6">
                <div class="border-b border-stone-100 pb-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Ringkasan Pilihan</span>
                    <h3 class="text-lg font-bold text-stone-900 mt-0.5">Detail Reservasi</h3>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Lapangan:</span>
                        <strong class="text-stone-900 font-semibold text-right">{{ $field->name }}</strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Tipe Arena:</span>
                        <span class="font-bold text-stone-800 uppercase text-[11px]">{{ ucfirst($field->type) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Tarif Lapangan:</span>
                        <strong class="text-stone-900 font-semibold">{{ $field->formatted_price }} / jam</strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Tanggal Main:</span>
                        <strong class="text-stone-900 font-semibold">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                        </strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Durasi Sewa:</span>
                        <strong class="text-stone-900 font-semibold">{{ $duration }} Jam</strong>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-stone-50">
                        <span class="text-stone-500">Jam Mulai – Selesai:</span>
                        @if($hour !== null && $selectedSlotInfo)
                            <strong class="text-emerald-700 font-bold text-sm">
                                {{ $selectedSlotInfo['label'] }} WIB
                            </strong>
                        @else
                            <span class="text-amber-600 font-medium italic">Belum memilih slot</span>
                        @endif
                    </div>
                </div>

                <!-- Pricing Total Box -->
                @php
                    $calculatedTotal = (int) ($field->price_per_hour ?? 150000) * $duration;
                @endphp
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                    <div class="flex items-center justify-between text-xs text-stone-600 mb-1">
                        <span>Estimasi Total Biaya ({{ $duration }} Jam):</span>
                        <span class="text-[11px] text-stone-500">{{ $duration }} × {{ $field->formatted_price }}</span>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-emerald-800">
                        Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                    </div>
                    <span class="block text-[10px] text-emerald-700 mt-1">Langsung terkonfirmasi saat formulir dikirim</span>
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
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-md focus-ring transition-all disabled:opacity-40 disabled:cursor-not-allowed group"
                >
                    @auth
                        <span>Lanjut ke Ringkasan Booking</span>
                    @else
                        <span>Masuk untuk Konfirmasi</span>
                    @endauth
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <p class="text-[11px] text-stone-500 text-center leading-relaxed">
                    Reservasi otomatis dikunci di sistem server kami.
                </p>
            </div>
        </div>
    </div>
</div>
