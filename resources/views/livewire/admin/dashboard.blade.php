<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-white mb-2">
                <span>🛡️ Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#17231B] tracking-tight">Dashboard Ringkas FutsalKita</h1>
            <p class="text-xs sm:text-sm text-[#58695C] mt-1">Pantau penggunaan lapangan dan manajemen booking hari ini</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.fields') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 hover:bg-stone-50 text-xs font-bold text-stone-700 transition-colors focus-ring">
                Kelola Lapangan
            </a>
            <a href="{{ route('admin.bookings') }}" class="px-4 py-2.5 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-xs font-bold text-white shadow-sm transition-colors focus-ring">
                Kelola Semua Booking
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
        <!-- Metric 1: Lapangan -->
        <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#E2E7E2] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-[#58695C] block">Lapangan Aktif</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-[#17231B]">{{ $activeFields }}</span>
                <span class="text-xs text-stone-400">/ {{ $totalFields }} Total</span>
            </div>
            <a href="{{ route('admin.fields') }}" class="text-[11px] font-bold text-[#2F6B3D] hover:underline mt-3 inline-block">Atur Status Lapangan &rarr;</a>
        </div>

        <!-- Metric 2: Hari Ini -->
        <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#E2E7E2] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-[#58695C] block">Booking Hari Ini</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-[#2F6B3D]">{{ $bookingsToday }}</span>
                <span class="text-xs text-stone-400">Sesi</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Berdasarkan waktu WIB</span>
        </div>

        <!-- Metric 3: Mendatang -->
        <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#E2E7E2] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-[#58695C] block">Jadwal Mendatang</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-blue-600">{{ $upcomingBookings }}</span>
                <span class="text-xs text-stone-400">Terkonfirmasi</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Belum dimulai</span>
        </div>

        <!-- Metric 4: Selesai & Batal -->
        <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#E2E7E2] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-[#58695C] block">Selesai / Batal</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-stone-800">{{ $completedBookings }}</span>
                <span class="text-xs text-rose-600">/ {{ $cancelledBookings }} Batal</span>
            </div>
            <span class="text-[11px] text-stone-400 mt-3 block">Histori tersimpan</span>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white rounded-[26px] p-6 sm:p-8 border border-[#E2E7E2] shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-[#17231B]">Aktivitas Reservasi Terbaru</h2>
                <p class="text-xs text-[#58695C] mt-0.5">Daftar booking yang baru masuk ke sistem</p>
            </div>
            <a href="{{ route('admin.bookings') }}" class="text-xs font-bold text-[#2F6B3D] hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-[#58695C] uppercase tracking-wider font-semibold">
                        <th class="pb-3 px-3">Kode</th>
                        <th class="pb-3 px-3">Pemesan</th>
                        <th class="pb-3 px-3">Lapangan</th>
                        <th class="pb-3 px-3">Waktu Main</th>
                        <th class="pb-3 px-3">Status</th>
                        <th class="pb-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($recentBookings as $b)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-3.5 px-3 font-mono font-bold text-[#17231B]">{{ $b->booking_code }}</td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-[#17231B] block">{{ $b->user->name }}</span>
                                <span class="text-[11px] text-stone-400">{{ $b->user->phone }}</span>
                            </td>
                            <td class="py-3.5 px-3 font-medium">{{ $b->field->name }}</td>
                            <td class="py-3.5 px-3">
                                <span class="font-semibold block text-[#17231B]">{{ $b->starts_at_local->format('d M Y') }}</span>
                                <span class="text-[11px] text-[#2F6B3D] font-bold">{{ $b->starts_at_local->format('H:i') }} - {{ $b->ends_at_local->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $b->status->badgeClasses() }}">
                                    {{ $b->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('admin.bookings', ['search' => $b->booking_code]) }}" class="text-xs font-bold text-[#2F6B3D] hover:underline">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">Belum ada booking tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
