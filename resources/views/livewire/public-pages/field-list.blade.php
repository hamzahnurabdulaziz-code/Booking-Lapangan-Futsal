<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Header -->
    <div class="mb-10 text-center sm:text-left">
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Pilihan Arena</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight mt-1">Daftar Lapangan Futsal</h1>
        <p class="text-xs sm:text-sm text-stone-600 mt-2 max-w-2xl leading-relaxed">
            Pilih lapangan futsal sesuai preferensi tipe lantai dan kebutuhan bertanding tim Anda. Seluruh lapangan dilengkapi fasilitas pendukung standar match resmi.
        </p>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-[22px] p-4 mb-8 border border-stone-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Type Filter Chips -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span class="text-xs font-bold uppercase text-stone-500 mr-1">Tipe:</span>
            <button 
                type="button" 
                wire:click="$set('type', 'all')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all focus-ring {{ $type === 'all' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}"
            >
                Semua
            </button>
            <button 
                type="button" 
                wire:click="$set('type', 'indoor')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all focus-ring {{ $type === 'indoor' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}"
            >
                Indoor
            </button>
            <button 
                type="button" 
                wire:click="$set('type', 'outdoor')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all focus-ring {{ $type === 'outdoor' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}"
            >
                Outdoor
            </button>
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <span class="text-xs font-bold uppercase text-stone-500">Status:</span>
            <select wire:model.live="status" class="px-3 py-1.5 rounded-xl text-xs font-semibold border border-stone-300 bg-stone-50 text-stone-900 focus-ring">
                <option value="active">Hanya Lapangan Aktif</option>
                <option value="all">Semua (Termasuk Nonaktif)</option>
            </select>
        </div>
    </div>

    <!-- Field Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($fields as $field)
            <div class="bg-white rounded-[26px] overflow-hidden border border-stone-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group {{ !$field->is_active ? 'opacity-75 grayscale-25' : '' }}">
                <!-- Photo with badges & pricing -->
                <div class="relative h-60 bg-stone-100 overflow-hidden">
                    <img 
                        src="{{ asset($field->image_path ?: 'images/placeholder-field.svg') }}" 
                        alt="{{ $field->name }}" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-field.svg') }}';"
                    >
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $field->type === 'indoor' ? 'bg-indigo-700 text-white' : 'bg-emerald-700 text-white' }} shadow-md">
                            {{ ucfirst($field->type) }}
                        </span>
                    </div>

                    @if(!$field->is_active)
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-md">
                                Nonaktif
                            </span>
                        </div>
                    @endif

                    <!-- Pricing Tag -->
                    <div class="absolute bottom-3 right-3 bg-stone-950/85 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-right shadow-lg">
                        <span class="block text-[10px] uppercase font-bold text-emerald-300 tracking-wider">Tarif Sewa</span>
                        <span class="block text-sm font-extrabold text-white">{{ $field->formatted_price }} <span class="text-[11px] font-normal text-stone-300">/ jam</span></span>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                    <div>
                        <h2 class="text-xl font-bold text-stone-900 group-hover:text-emerald-700 transition-colors">
                            {{ $field->name }}
                        </h2>
                        <p class="text-xs text-stone-600 line-clamp-3 mt-2 leading-relaxed">
                            {{ $field->description }}
                        </p>
                    </div>

                    <!-- Facilities -->
                    @if(!empty($field->facilities))
                        <div class="pt-3 border-t border-stone-100">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">Fasilitas Termasuk:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($field->facilities as $fac)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#F8FAF8] text-stone-700 border border-stone-200/60">
                                        ✓ {{ $fac }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action -->
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                        @if($field->is_active)
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Siap Dipesan</span>
                            </div>
                            <a 
                                href="{{ route('fields.show', $field->slug) }}" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm transition-all focus-ring group/btn"
                            >
                                <span>Pilih Jadwal</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <span class="text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg">
                                Dalam Pemeliharaan
                            </span>
                            <span class="text-xs font-medium text-stone-400">
                                Tidak Tersedia
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center bg-white rounded-[24px] border border-dashed border-stone-300">
                <svg class="w-12 h-12 text-stone-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-base font-bold text-stone-900">Tidak ada lapangan yang cocok</h3>
                <p class="text-xs text-stone-500 mt-1">Coba sesuaikan filter tipe atau status lapangan.</p>
                <button type="button" wire:click="$set('type', 'all'); $set('status', 'active')" class="mt-4 px-4 py-2 rounded-xl text-xs font-bold bg-stone-100 text-stone-700 hover:bg-stone-200">
                    Reset Filter
                </button>
            </div>
        @endforelse
    </div>
</div>
