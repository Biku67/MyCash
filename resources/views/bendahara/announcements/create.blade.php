<x-app-layout>
@section('page-title', 'Buat Pengumuman Kas')

<div class="max-w-3xl mx-auto" 
     x-data="{
         targetType: '{{ old('target_type', 'all') }}',
         search: '',
         selectedStudents: {{ json_encode(array_map('strval', old('student_ids', []))) }},
         allStudentIds: {{ json_encode($students->pluck('id')->map(fn($id) => (string)$id)->values()->all()) }},
         selectAll() {
             this.selectedStudents = [...this.allStudentIds];
         },
         deselectAll() {
             this.selectedStudents = [];
         }
     }">
    
    <!-- Back Navigation -->
    <div class="mb-5">
        <a href="{{ route('bendahara.announcements.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Pengumuman
        </a>
    </div>

    <!-- Main Card -->
    <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="mb-6 pb-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Buat Pengumuman Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Kirim pengumuman atau info kas kepada siswa kelas <span class="font-semibold text-slate-700">{{ $kelas ? $kelas->nama_kelas : 'Anda' }}</span>.
                </p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">campaign</span>
            </div>
        </div>

        <form method="POST" action="{{ route('bendahara.announcements.store') }}" class="space-y-6">
            @csrf

            <!-- Opsi Target Penerima -->
            <div id="tour-bendahara-announcement-target">
                <label class="block text-sm font-semibold text-navy mb-2.5">
                    Penerima Pengumuman <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Opsi 1: Satu Kelas Penuh -->
                    <div role="button" tabindex="0" @click="targetType = 'all'"
                         class="cursor-pointer relative block group p-4 rounded-2xl border-2 transition-all duration-200 flex items-start justify-between gap-3 focus:outline-none"
                         :class="targetType === 'all'
                             ? 'border-teal-500 bg-gradient-to-br from-teal-50/90 via-white to-teal-50/40 shadow-sm ring-2 ring-teal-500/20'
                             : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-slate-50/60'">
                        <input type="radio" name="target_type" value="all" :checked="targetType === 'all'" class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 transition-all duration-200"
                                 :class="targetType === 'all' 
                                     ? 'bg-teal-500 text-white shadow-sm scale-105' 
                                     : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600'">
                                <span class="material-symbols-outlined text-xl">groups</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold transition-colors"
                                       :class="targetType === 'all' ? 'text-teal-950' : 'text-gray-700'">
                                        Satu Kelas (Semua Siswa)
                                    </p>
                                    {{-- <span x-show="targetType === 'all'" x-cloak class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                        Aktif
                                    </span> --}}
                                </div>
                                <p class="text-xs mt-0.5 transition-colors leading-relaxed"
                                   :class="targetType === 'all' ? 'text-teal-700 font-medium' : 'text-gray-400'">
                                    Kirim ke seluruh {{ $totalStudents }} siswa di kelas {{ $kelas ? $kelas->nama_kelas : '' }}.
                                </p>
                            </div>
                        </div>

                        <!-- Radio Check Indicator -->
                        <div class="flex-shrink-0 mt-1">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center transition-all shadow-xs"
                                  :class="targetType === 'all' ? 'bg-teal-500 text-white' : 'border-2 border-gray-300 bg-white'">
                                <span class="material-symbols-outlined text-xs font-bold" x-show="targetType === 'all'">check</span>
                            </span>
                        </div>
                    </div>

                    <!-- Opsi 2: Perorangan / Siswa Tertentu -->
                    <div role="button" tabindex="0" @click="targetType = 'selected'"
                         class="cursor-pointer relative block group p-4 rounded-2xl border-2 transition-all duration-200 flex items-start justify-between gap-3 focus:outline-none"
                         :class="targetType === 'selected'
                             ? 'border-navy bg-gradient-to-br from-navy/10 via-white to-navy/5 shadow-sm ring-2 ring-navy/20'
                             : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-slate-50/60'">
                        <input type="radio" name="target_type" value="selected" :checked="targetType === 'selected'" class="sr-only">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 transition-all duration-200"
                                 :class="targetType === 'selected' 
                                     ? 'bg-navy text-white shadow-sm scale-105' 
                                     : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600'">
                                <span class="material-symbols-outlined text-xl">person_pin</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold transition-colors"
                                       :class="targetType === 'selected' ? 'text-navy' : 'text-gray-700'">
                                        Perorangan (Siswa Tertentu)
                                    </p>
                                </div>
                                <p class="text-xs mt-0.5 transition-colors leading-relaxed"
                                   :class="targetType === 'selected' ? 'text-navy-light font-medium' : 'text-gray-400'">
                                    Kirim khusus ke 1 atau beberapa siswa terpilih saja.
                                </p>
                            </div>
                        </div>

                        <!-- Radio Check Indicator -->
                        <div class="flex-shrink-0 mt-1">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center transition-all shadow-xs"
                                  :class="targetType === 'selected' ? 'bg-navy text-white' : 'border-2 border-gray-300 bg-white'">
                                <span class="material-symbols-outlined text-xs font-bold" x-show="targetType === 'selected'">check</span>
                            </span>
                        </div>
                    </div>
                </div>
                @error('target_type')
                    <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span>
                        {{ $message }}
                    </p>
                @enderror

                <!-- Multi-Select Siswa (Ditampilkan jika targetType === 'selected') -->
                <div x-show="targetType === 'selected'" x-transition x-cloak class="p-4 bg-gradient-to-b from-navy/5 to-slate-50 border border-slate-200/80 rounded-2xl space-y-3 mt-3.5 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 pb-2.5 border-b border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-navy">Pilih Siswa Penerima</p>
                            <p class="text-[11px] text-gray-500">
                                <span class="font-bold text-navy" x-text="selectedStudents.length">0</span> dari {{ $totalStudents }} siswa dipilih
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAll()" class="text-[11px] font-semibold text-navy hover:text-navy-dark px-2.5 py-1 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors shadow-2xs">
                                Pilih Semua
                            </button>
                            <button type="button" @click="deselectAll()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 px-2.5 py-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-2xs">
                                Batal Pilih
                            </button>
                        </div>
                    </div>

                    <!-- Search Filter Siswa -->
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-lg">search</span>
                        <input type="text" x-model="search" placeholder="Cari nama atau NIS siswa..." 
                               class="input-clean w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:border-navy focus:ring-1 focus:ring-navy">
                    </div>

                    <!-- List Siswa Scrollable -->
                    <div class="max-h-56 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-100">
                        @foreach($students as $student)
                            <label data-search="{{ strtolower($student->nama . ' ' . ($student->nis ?? '')) }}"
                                   x-show="search === '' || $el.dataset.search.includes(search.toLowerCase().trim())" 
                                   class="flex items-center justify-between p-2.5 rounded-xl cursor-pointer transition-all pt-2 border"
                                   :class="selectedStudents.includes('{{ $student->id }}') 
                                       ? 'bg-navy/10 border-navy/30 shadow-2xs' 
                                       : 'hover:bg-white border-transparent'">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" x-model="selectedStudents"
                                           class="w-4 h-4 text-navy rounded border-gray-300 focus:ring-navy">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors"
                                         :class="selectedStudents.includes('{{ $student->id }}') 
                                             ? 'bg-navy text-white shadow-2xs' 
                                             : 'bg-teal-accent/20 text-teal-accent'">
                                        {{ substr($student->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold"
                                           :class="selectedStudents.includes('{{ $student->id }}') ? 'text-navy font-bold' : 'text-slate-800'">
                                            {{ $student->nama }}
                                        </p>
                                        <p class="text-[10px] font-mono text-gray-400">NIS: {{ $student->nis ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-navy flex items-center gap-1 font-semibold" 
                                      x-show="selectedStudents.includes('{{ $student->id }}')">
                                    <span class="material-symbols-outlined text-base">check_circle</span>
                                    <span class="hidden sm:inline">Terpilih</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('student_ids')
                        <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Judul Pengumuman -->
            <div id="tour-bendahara-announcement-title">
                <label for="judul" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required maxlength="255"
                       class="input-clean w-full px-4 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white rounded-xl border border-slate-200"
                       placeholder="Contoh: Pengingat Iuran Kas Bulan Ini">
                @error('judul')
                    <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Isi Pengumuman -->
            <div id="tour-bendahara-announcement-body">
                <label for="isi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Isi Pengumuman / Pesan <span class="text-rose-500">*</span>
                </label>
                <textarea id="isi" name="isi" rows="6" required
                          class="input-clean w-full px-4 py-3 text-xs sm:text-sm font-medium text-slate-800 bg-white rounded-xl border border-slate-200 leading-relaxed"
                          placeholder="Tulis rincian pesan atau info kas kelas di sini...">{{ old('isi') }}</textarea>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    Sampaikan informasi secara ringkas dan jelas, misalnya batas waktu pembayaran atau keperluan kas kelas.
                </p>
                @error('isi')
                    <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p x-show="targetType === 'selected' && selectedStudents.length === 0" x-cloak 
                       class="text-xs text-amber-600 flex items-center gap-1.5 font-medium">
                        <span class="material-symbols-outlined text-base">warning</span>
                        Pilih minimal 1 siswa penerima untuk melanjutkan.
                    </p>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('bendahara.announcements.index') }}" 
                       class="py-2.5 px-4 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            id="tour-bendahara-announcement-submit"
                            :disabled="targetType === 'selected' && selectedStudents.length === 0"
                            :class="targetType === 'selected' && selectedStudents.length === 0 ? 'opacity-50 cursor-not-allowed' : 'active:scale-95'"
                            class="btn-navy py-2.5 px-5 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-base">send</span>
                        <span>Kirim Pengumuman</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

</x-app-layout>
