<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-stone-900 text-white mb-2">
                <span>🛡️ Administrator</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#17231B] tracking-tight">Manajemen Lapangan Futsal</h1>
            <p class="text-xs sm:text-sm text-[#58695C] mt-1">Atur nama, tipe, fasilitas, foto, serta ketersediaan status aktif setiap lapangan</p>
        </div>

        <div>
            <button 
                type="button" 
                wire:click="createField"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-xs font-bold text-white shadow-sm transition-colors focus-ring"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Lapangan Baru</span>
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[26px] p-6 sm:p-8 border border-[#E2E7E2] shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-[#58695C] uppercase tracking-wider font-semibold">
                        <th class="pb-3 px-3">Lapangan</th>
                        <th class="pb-3 px-3">Tipe</th>
                        <th class="pb-3 px-3">Fasilitas</th>
                        <th class="pb-3 px-3">Total Booking</th>
                        <th class="pb-3 px-3">Status</th>
                        <th class="pb-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($fields as $f)
                        <tr class="hover:bg-stone-50/60 transition-colors">
                            <td class="py-4 px-3">
                                <div class="flex items-center gap-3">
                                    <img 
                                        src="{{ asset($f->image_path ?: 'images/placeholder-field.svg') }}" 
                                        alt="{{ $f->name }}" 
                                        class="w-12 h-10 object-cover rounded-lg border border-stone-200 bg-stone-100 shrink-0"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-field.svg') }}';"
                                    >
                                    <div>
                                        <strong class="text-sm font-bold text-[#17231B] block">{{ $f->name }}</strong>
                                        <span class="text-[11px] text-stone-400 font-mono">{{ $f->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $f->type === 'indoor' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ ucfirst($f->type) }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="font-medium text-stone-700">{{ is_array($f->facilities) ? count($f->facilities) : 0 }} Fasilitas</span>
                            </td>
                            <td class="py-4 px-3 font-semibold text-stone-800">
                                {{ $f->bookings_count }} Booking
                            </td>
                            <td class="py-4 px-3">
                                @if($f->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        wire:click="toggleStatus({{ $f->id }})"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $f->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }} transition-colors"
                                    >
                                        {{ $f->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="editField({{ $f->id }})"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-stone-100 hover:bg-stone-200 text-stone-700 transition-colors"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">Belum ada lapangan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 rounded-xl bg-stone-50 border border-stone-200 text-[11px] text-[#58695C] leading-relaxed">
            ℹ️ <strong>Catatan Integritas Data:</strong> Lapangan yang telah memiliki riwayat booking tidak dapat di-hard-delete untuk mencegah kerusakan relasi data pemesanan. Bila lapangan sedang tidak dapat digunakan, gunakan tombol <strong>Nonaktifkan</strong>.
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if($showModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
            role="dialog"
            aria-modal="true"
            @keydown.escape.window="$wire.set('showModal', false)"
        >
            <div class="bg-white rounded-[26px] max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-6 my-8">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <h2 class="text-lg font-bold text-[#17231B]">
                        {{ $editingFieldId ? 'Edit Data Lapangan' : 'Tambah Lapangan Baru' }}
                    </h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-stone-400 hover:text-stone-600 text-lg">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label for="fieldName" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Nama Lapangan</label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            id="fieldName" 
                            required 
                            placeholder="Contoh: Lapangan Rumput Sintetis (D)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50"
                        >
                        @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="fieldType" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Tipe Lapangan</label>
                            <select wire:model="type" id="fieldType" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50">
                                <option value="indoor">Indoor</option>
                                <option value="outdoor">Outdoor</option>
                            </select>
                        </div>
                        <div>
                            <label for="fieldStatus" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Status Lapangan</label>
                            <select wire:model="isActive" id="fieldStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50">
                                <option value="1">Aktif (Dapat Dipesan)</option>
                                <option value="0">Nonaktif (Pemeliharaan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="fieldDesc" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Deskripsi Lapangan</label>
                        <textarea 
                            wire:model="description" 
                            id="fieldDesc" 
                            rows="3" 
                            placeholder="Penjelasan keunggulan lantai, jaring, dan spesifikasi lapangan..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50"
                        ></textarea>
                    </div>

                    <div>
                        <label for="fieldFacilities" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Fasilitas (1 per baris)</label>
                        <textarea 
                            wire:model="facilitiesText" 
                            id="fieldFacilities" 
                            rows="3" 
                            placeholder="Lantai Vinyl Taraflex&#10;Lampu LED Standard&#10;Ruang Ganti AC"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50"
                        ></textarea>
                    </div>

                    <div>
                        <label for="fieldImage" class="block text-xs font-semibold uppercase text-[#17231B] mb-1">Path Gambar Aset Lokal</label>
                        <input 
                            wire:model="imagePath" 
                            type="text" 
                            id="fieldImage" 
                            placeholder="images/fields/field-vinyl-a.svg"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus-ring bg-stone-50/50"
                        >
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            wire:click="$set('showModal', false)"
                            class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-bold hover:bg-stone-50"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl bg-[#2F6B3D] hover:bg-[#245430] text-white text-xs font-bold shadow-sm"
                        >
                            <span wire:loading.remove wire:target="save">Simpan Data Lapangan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
