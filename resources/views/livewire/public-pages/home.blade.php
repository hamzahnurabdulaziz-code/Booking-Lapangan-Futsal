<div class="relative overflow-hidden">
    <!-- Hero Section with Deep Futsal Atmosphere -->
    <section class="relative isolate min-h-[580px] lg:min-h-[640px] flex items-center justify-center overflow-hidden bg-gradient-to-b from-[#092B21] via-[#0D3B2E] to-[#061F18] text-white px-4 sm:px-6 lg:px-8 pt-12 pb-28 lg:pb-36">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset('images/placeholder-field.svg') }}" 
                alt="Arena Lapangan Futsal FutsalKita" 
                class="w-full h-full object-cover opacity-20"
                loading="eager"
            >
            <div class="absolute inset-0 bg-gradient-to-b from-[#092B21]/85 via-[#0D3B2E]/80 to-[#061F18]/95"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-500/15 blur-[120px]"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/5 to-transparent opacity-40"></div>
            <svg class="pointer-events-none absolute inset-0 h-full w-full text-white/10" viewBox="0 0 1200 700" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                <defs>
                    <linearGradient id="stadium-beam" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#A7F3D0" stop-opacity=".2" />
                        <stop offset="1" stop-color="#A7F3D0" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path d="M0 0H120L390 700H0Z" fill="url(#stadium-beam)" />
                <path d="M115 0H195L475 700H385Z" fill="url(#stadium-beam)" />
                <path d="M1080 0H1200V700H810Z" fill="url(#stadium-beam)" />
                <path d="M1005 0H1085L815 700H725Z" fill="url(#stadium-beam)" />
                <rect x="90" y="40" width="1020" height="620" rx="28" stroke="currentColor" />
                <line x1="600" y1="40" x2="600" y2="660" stroke="currentColor" />
                <circle cx="600" cy="350" r="110" stroke="currentColor" />
            </svg>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-lg rounded-[28px] border border-emerald-300/45 bg-slate-950/30 px-6 py-8 text-center shadow-[0_24px_80px_rgba(16,185,129,0.14)] backdrop-blur-md lg:max-w-2xl sm:px-10 sm:py-10">
            <div class="space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-semibold text-emerald-200 shadow-[0_0_15px_rgba(16,185,129,0.2)] backdrop-blur-md sm:text-sm">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                    </span>
                    <span>Jadwal Real-Time • Langsung Terkonfirmasi</span>
                </div>

                <!-- Headline -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Jadwal Main Aman, <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-lime-400 bg-clip-text text-transparent drop-shadow-sm">Tinggal Ajak Tim.</span>
                </h1>

                <!-- Description -->
                <p class="max-w-2xl mx-auto text-base sm:text-lg text-emerald-100/80 leading-relaxed font-light">
                    Pilih lapangan dan waktu bermain. Simpan booking timmu dalam beberapa langkah mudah tanpa khawatir jadwal bertabrakan.
                </p>
            </div>
        </div>
    </section>

    <!-- Floating Quick Search Slot Form (Overlapping Hero bottom) -->
    <section class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 lg:-mt-24">
        <div class="bg-white rounded-[24px] p-5 sm:p-7 shadow-xl border border-stone-200/80 backdrop-blur-lg">
            <div class="mb-4 flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2 text-sm font-bold text-[#17231B]">
                    <svg class="w-5 h-5 text-[#2F6B3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Slot Lapangan Tersedia</span>
                </div>
                <span class="text-xs text-[#58695C] font-medium hidden sm:inline">Operasional: 08.00 – 23.00 WIB</span>
            </div>

            <form wire:submit="checkSchedule" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
                <!-- Field Picker -->
                <div class="space-y-1.5">
                    <label for="searchField" class="block text-xs font-semibold uppercase tracking-wider text-[#58695C]">Pilih Lapangan</label>
                    <div class="relative">
                        <select 
                            wire:model="selectedFieldId" 
                            id="searchField" 
                            class="w-full pl-3 pr-8 py-3 rounded-xl border border-stone-300 bg-stone-50/60 text-sm font-medium text-[#17231B] focus-ring appearance-none"
                        >
                            @foreach($allFieldsForSelect as $f)
                                <option value="{{ $f->id }}">
                                    {{ $f->name }} ({{ ucfirst($f->type) }}) {{ !$f->is_active ? '— Nonaktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-stone-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Date Picker -->
                <div class="space-y-1.5">
                    <label for="searchDate" class="block text-xs font-semibold uppercase tracking-wider text-[#58695C]">Tanggal Main</label>
                    <input 
                        wire:model="selectedDate" 
                        type="date" 
                        id="searchDate"
                        min="{{ \Carbon\Carbon::now('Asia/Jakarta')->toDateString() }}"
                        max="{{ \Carbon\Carbon::now('Asia/Jakarta')->addDays(30)->toDateString() }}"
                        class="w-full px-3 py-3 rounded-xl border border-stone-300 bg-stone-50/60 text-sm font-medium text-[#17231B] focus-ring"
                    >
                </div>

                <!-- Start Hour -->
                <div class="space-y-1.5">
                    <label for="searchHour" class="block text-xs font-semibold uppercase tracking-wider text-[#58695C]">Jam Mulai</label>
                    <div class="relative">
                        <select 
                            wire:model="startHour" 
                            id="searchHour" 
                            class="w-full pl-3 pr-8 py-3 rounded-xl border border-stone-300 bg-stone-50/60 text-sm font-medium text-[#17231B] focus-ring appearance-none"
                        >
                            @for($h = 8; $h <= 22; $h++)
                                <option value="{{ $h }}">{{ sprintf('%02d:00 WIB', $h) }}</option>
                            @endfor
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-stone-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Duration -->
                <div class="space-y-1.5">
                    <label for="searchDuration" class="block text-xs font-semibold uppercase tracking-wider text-[#58695C]">Durasi</label>
                    <div class="relative">
                        <select 
                            wire:model="durationHours" 
                            id="searchDuration" 
                            class="w-full pl-3 pr-8 py-3 rounded-xl border border-stone-300 bg-stone-50/60 text-sm font-medium text-[#17231B] focus-ring appearance-none"
                        >
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-stone-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full h-[46px] flex items-center justify-center gap-2 px-4 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-white text-sm font-semibold shadow-md focus-ring transition-all group"
                    >
                        <span>Cek Jadwal</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Featured Fields Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#2F6B3D]">Pilihan Terbaik</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#17231B] tracking-tight mt-1">Lapangan Unggulan FutsalKita</h2>
                <p class="text-sm text-[#58695C] mt-1">Temukan karakter lapangan yang paling pas untuk ritme permainan tim Anda</p>
            </div>
            <a href="{{ route('fields.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#2F6B3D] hover:text-[#245430] group">
                <span>Lihat Semua Lapangan</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($fields as $field)
                <div class="bg-white rounded-[24px] overflow-hidden border border-[#E2E7E2] shadow-sm hover:shadow-md transition-shadow flex flex-col group">
                    <!-- Image with Type Badge -->
                    <div class="relative h-56 bg-stone-100 overflow-hidden">
                        <img 
                            src="{{ asset($field->image_path ?: 'images/placeholder-field.svg') }}" 
                            alt="{{ $field->name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-field.svg') }}';"
                        >
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider {{ $field->type === 'indoor' ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white' }} shadow-sm">
                                {{ ucfirst($field->type) }}
                            </span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-[#17231B] group-hover:text-[#2F6B3D] transition-colors">
                                {{ $field->name }}
                            </h3>
                            <p class="text-xs text-[#58695C] line-clamp-2 mt-1.5 leading-relaxed">
                                {{ $field->description }}
                            </p>
                        </div>

                        <!-- Facilities Chips -->
                        @if(!empty($field->facilities))
                            <div class="flex flex-wrap gap-1.5 pt-2 border-t border-stone-100">
                                @foreach(array_slice($field->facilities, 0, 3) as $fac)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-medium bg-[#F7F7F2] text-[#58695C]">
                                        ✓ {{ $fac }}
                                    </span>
                                @endforeach
                                @if(count($field->facilities) > 3)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-medium bg-stone-100 text-stone-600">
                                        +{{ count($field->facilities) - 3 }} lagi
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                Siap Dipesan
                            </span>
                            <a 
                                href="{{ route('fields.show', $field->slug) }}" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#2F6B3D] hover:bg-[#245430] text-white shadow-sm transition-colors focus-ring"
                            >
                                <span>Pilih Jadwal</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-12 text-center bg-white rounded-[24px] border border-dashed border-stone-300">
                    <p class="text-stone-500 text-sm">Belum ada data lapangan aktif.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- How It Works / Cara Booking (3 Steps) -->
    <section id="cara-booking" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-28">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-bold uppercase tracking-wider text-[#2F6B3D]">Alur Mudah & Transparan</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#17231B] tracking-tight mt-1">3 Langkah Reservasi Lapangan</h2>
            <p class="text-sm text-[#58695C] mt-2">Tanpa menunggu persetujuan manual, jadwal terkonfirmasi secara instan di sistem.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-[24px] p-7 border border-[#E2E7E2] shadow-sm relative">
                <div class="w-12 h-12 rounded-2xl bg-[#EBF3ED] text-[#2F6B3D] flex items-center justify-center font-black text-xl mb-5">
                    1
                </div>
                <h3 class="text-base font-bold text-[#17231B] mb-2">Tentukan Lapangan & Slot</h3>
                <p class="text-xs text-[#58695C] leading-relaxed">
                    Pilih tipe lapangan yang sesuai, tentukan tanggal main (hingga 30 hari ke depan), serta jam mulai dan durasi bermain (1–3 jam).
                </p>
            </div>

            <div class="bg-white rounded-[24px] p-7 border border-[#E2E7E2] shadow-sm relative">
                <div class="w-12 h-12 rounded-2xl bg-[#EBF3ED] text-[#2F6B3D] flex items-center justify-center font-black text-xl mb-5">
                    2
                </div>
                <h3 class="text-base font-bold text-[#17231B] mb-2">Masuk & Konfirmasi Tim</h3>
                <p class="text-xs text-[#58695C] leading-relaxed">
                    Masuk ke akun Anda (pilihan jadwal tetap tersimpan). Lengkapi nama tim dan catatan pendukung untuk petugas lapangan.
                </p>
            </div>

            <div class="bg-white rounded-[24px] p-7 border border-[#E2E7E2] shadow-sm relative">
                <div class="w-12 h-12 rounded-2xl bg-[#EBF3ED] text-[#2F6B3D] flex items-center justify-center font-black text-xl mb-5">
                    3
                </div>
                <h3 class="text-base font-bold text-[#17231B] mb-2">Jadwal Terkonfirmasi</h3>
                <p class="text-xs text-[#58695C] leading-relaxed">
                    Sistem mengunci slot secara otomatis. Anda langsung mendapatkan kode booking unik dan detail reservasi untuk ditunjukkan saat tiba di arena.
                </p>
            </div>
        </div>
    </section>

    <!-- Venue Facilities Highlight -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-28">
        <div class="bg-[#17231B] text-white rounded-[32px] p-8 sm:p-12 lg:p-16 relative overflow-hidden">
            <div class="max-w-2xl relative z-10 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Fasilitas Arena Lengkap</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Kenyamanan Maksimal untuk Tim Anda</h2>
                <p class="text-sm text-stone-300 leading-relaxed">
                    FutsalKita menyediakan arena terintegrasi dengan sarana pendukung bersih, aman, dan siap pakai sebelum maupun sesudah bertanding.
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-6">
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">🚿</div>
                        <h4 class="text-xs font-bold text-white">Ruang Ganti & Shower</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Air bersih dan terawat</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">🅿️</div>
                        <h4 class="text-xs font-bold text-white">Parkir Luas</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Aman untuk motor & mobil</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">💡</div>
                        <h4 class="text-xs font-bold text-white">LED Standard Match</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Terang tanpa bayangan</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">🕌</div>
                        <h4 class="text-xs font-bold text-white">Musholla Bersih</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Tempat ibadah nyaman</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">🥤</div>
                        <h4 class="text-xs font-bold text-white">Kantin Minuman</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Isotonik & snack segar</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <div class="text-xl mb-1">🎽</div>
                        <h4 class="text-xs font-bold text-white">Bola & Rompi</h4>
                        <p class="text-[11px] text-stone-400 mt-0.5">Disediakan di lapangan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-28">
        <div class="text-center mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-[#2F6B3D]">Informasi Bantuan</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#17231B] tracking-tight mt-1">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-sm text-[#58695C] mt-1">Pelajari ketentuan operasional dan mekanisme reservasi FutsalKita</p>
        </div>

        <div class="space-y-4" x-data="{ active: null }">
            <div class="bg-white rounded-[20px] border border-[#E2E7E2] overflow-hidden">
                <button 
                    type="button" 
                    @click="active = (active === 1 ? null : 1)"
                    class="w-full px-6 py-4.5 text-left flex items-center justify-between text-sm font-bold text-[#17231B] hover:text-[#2F6B3D]"
                >
                    <span>Kapan jam operasional FutsalKita?</span>
                    <span class="text-base" x-text="active === 1 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 1" x-collapse class="px-6 pb-5 text-xs text-[#58695C] leading-relaxed border-t border-stone-100 pt-3">
                    Jam operasional awal adalah pukul 08.00 hingga 23.00 WIB setiap hari. Pemesanan slot dimulai pada jam bulat (misal 08:00, 09:00) dan selesai maksimal pukul 23.00 WIB tanpa booking lintas tengah malam.
                </div>
            </div>

            <div class="bg-white rounded-[20px] border border-[#E2E7E2] overflow-hidden">
                <button 
                    type="button" 
                    @click="active = (active === 2 ? null : 2)"
                    class="w-full px-6 py-4.5 text-left flex items-center justify-between text-sm font-bold text-[#17231B] hover:text-[#2F6B3D]"
                >
                    <span>Apakah booking langsung terkonfirmasi atau menunggu persetujuan admin?</span>
                    <span class="text-base" x-text="active === 2 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 2" x-collapse class="px-6 pb-5 text-xs text-[#58695C] leading-relaxed border-t border-stone-100 pt-3">
                    Pada MVP FutsalKita, booking langsung berstatus Terkonfirmasi (confirmed) segera setelah server memvalidasi ketersediaan dan mengunci slot. Tidak ada tahap persetujuan manual atau verifikasi pembayaran di versi ini.
                </div>
            </div>

            <div class="bg-white rounded-[20px] border border-[#E2E7E2] overflow-hidden">
                <button 
                    type="button" 
                    @click="active = (active === 3 ? null : 3)"
                    class="w-full px-6 py-4.5 text-left flex items-center justify-between text-sm font-bold text-[#17231B] hover:text-[#2F6B3D]"
                >
                    <span>Bagaimana jika tim saya ingin membatalkan atau mengubah jadwal?</span>
                    <span class="text-base" x-text="active === 3 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 3" x-collapse class="px-6 pb-5 text-xs text-[#58695C] leading-relaxed border-t border-stone-100 pt-3">
                    Pelanggan dapat membatalkan booking miliknya secara mandiri melalui menu "Booking Saya", dengan syarat pembatalan dilakukan <strong>sebelum jam mulai bermain</strong>. MVP ini tidak menyediakan fitur reschedule langsung; untuk mengganti jadwal, batalkan booking lama lalu buat booking baru.
                </div>
            </div>

            <div class="bg-white rounded-[20px] border border-[#E2E7E2] overflow-hidden">
                <button 
                    type="button" 
                    @click="active = (active === 4 ? null : 4)"
                    class="w-full px-6 py-4.5 text-left flex items-center justify-between text-sm font-bold text-[#17231B] hover:text-[#2F6B3D]"
                >
                    <span>Berapa hari ke depan saya bisa memesan jadwal?</span>
                    <span class="text-base" x-text="active === 4 ? '−' : '+'"></span>
                </button>
                <div x-show="active === 4" x-collapse class="px-6 pb-5 text-xs text-[#58695C] leading-relaxed border-t border-stone-100 pt-3">
                    Anda dapat memesan jadwal mulai hari ini hingga maksimal 30 hari ke depan menurut waktu lokal Indonesia Barat (Asia/Jakarta).
                </div>
            </div>
        </div>
    </section>
</div>
