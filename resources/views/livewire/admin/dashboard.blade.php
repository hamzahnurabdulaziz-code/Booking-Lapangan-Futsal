<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-white mb-2">
                <span>🛡️ Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Dashboard Ringkas FutsalKita</h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">Pantau utilisasi lapangan, jadwal reservasi, dan performa booking hari ini</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.fields') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 hover:bg-stone-50 text-xs font-bold text-stone-700 transition-colors focus-ring">
                Kelola Lapangan
            </a>
            <a href="{{ route('admin.bookings') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition-colors focus-ring">
                Kelola Semua Booking
            </a>
        </div>
    </div>

    <!-- Operating Hours Overview Card -->
    <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-[#0A261B] text-white rounded-[24px] p-5 sm:p-6 mb-8 shadow-sm border border-emerald-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-white/10 text-emerald-300 flex items-center justify-center font-bold text-lg shrink-0">
                🕒
            </div>
            <div>
                <span class="text-[11px] font-bold text-emerald-300 uppercase tracking-wider block">Jam Operasional Arena</span>
                <span class="text-base sm:text-lg font-bold">Setiap Hari, 08.00 – 23.00 WIB</span>
            </div>
        </div>
        <div class="flex items-center gap-6 text-xs text-emerald-100/80">
            <div>
                <span class="block font-bold text-white text-sm">{{ $activeFields }} dari {{ $totalFields }}</span>
                <span>Lapangan Aktif</span>
            </div>
            <div class="border-l border-white/20 pl-6">
                <span class="block font-bold text-white text-sm">Rp {{ number_format($totalEstimatedValue, 0, ',', '.') }}</span>
                <span>Nilai Booking Aktif</span>
            </div>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
        <!-- Metric 1: Lapangan -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 border-l-4 border-l-emerald-600 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500 block">Lapangan Aktif</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-stone-900">{{ $activeFields }}</span>
                <span class="text-xs text-stone-400">/ {{ $totalFields }} Total</span>
            </div>
            <a href="{{ route('admin.fields') }}" class="text-[11px] font-bold text-emerald-700 hover:underline mt-3 inline-block">Atur Status & Tarif &rarr;</a>
        </div>

        <!-- Metric 2: Hari Ini -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 border-l-4 border-l-blue-600 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500 block">Booking Hari Ini</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-blue-600">{{ $bookingsToday }}</span>
                <span class="text-xs text-stone-400">Sesi</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Berdasarkan waktu WIB</span>
        </div>

        <!-- Metric 3: Mendatang -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 border-l-4 border-l-amber-500 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500 block">Jadwal Mendatang</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-amber-600">{{ $upcomingBookings }}</span>
                <span class="text-xs text-stone-400">Terkonfirmasi</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Belum dimulai</span>
        </div>

        <!-- Metric 4: Selesai & Batal -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 border-l-4 border-l-rose-500 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-stone-500 block">Selesai / Batal</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-emerald-700">{{ $completedBookings }}</span>
                <span class="text-xs text-rose-600">/ {{ $cancelledBookings }} Batal</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Histori tersimpan</span>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-stone-200 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-stone-900">Aktivitas Reservasi Terbaru</h2>
                <p class="text-xs text-stone-500 mt-0.5">Daftar booking yang baru masuk ke sistem</p>
            </div>
            <a href="{{ route('admin.bookings') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-stone-500 uppercase tracking-wider font-semibold">
                        <th class="pb-3 px-3">Kode</th>
                        <th class="pb-3 px-3">Pemesan</th>
                        <th class="pb-3 px-3">Lapangan</th>
                        <th class="pb-3 px-3">Waktu Main</th>
                        <th class="pb-3 px-3">Total Biaya</th>
                        <th class="pb-3 px-3">Status</th>
                        <th class="pb-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($recentBookings as $b)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-3.5 px-3 font-mono font-bold text-stone-900">{{ $b->booking_code }}</td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-stone-900 block">{{ $b->user->name }}</span>
                                <span class="text-[11px] text-stone-400">{{ $b->user->phone }}</span>
                            </td>
                            <td class="py-3.5 px-3 font-medium">{{ $b->field->name }}</td>
                            <td class="py-3.5 px-3">
                                <span class="font-semibold block text-stone-900">{{ $b->starts_at_local->format('d M Y') }}</span>
                                <span class="text-[11px] text-emerald-700 font-bold">{{ $b->starts_at_local->format('H:i') }} - {{ $b->ends_at_local->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-3 font-bold text-stone-900">
                                {{ $b->formatted_total_price }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $b->status->badgeClasses() }}">
                                    {{ $b->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('admin.bookings', ['search' => $b->booking_code]) }}" class="text-[11px] font-bold text-emerald-700 hover:underline">
                                    Kelola &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">Belum ada aktivitas reservasi di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
