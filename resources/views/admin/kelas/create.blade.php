<x-app-layout>
@section('page-title', 'Tambah Kelas')

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.kelas.index') }}" class="hover:text-slate-900 transition-colors">Kelola Kelas</a>
                <span>/</span>
                <span class="text-slate-700 font-medium">Tambah Baru</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Tambah Kelas Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tambah kelas baru, tentukan wali kelas, dan tentukan nominal iuran kas.</p>
        </div>
        <a href="{{ route('admin.kelas.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="card p-6 sm:p-7 border border-slate-200/80 rounded-2xl shadow-sm" x-data="kelasCreateForm()">
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-5">
            @csrf

            <!-- Section 1: Identitas Kelas -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading mb-3.5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600 text-lg">school</span>
                    <span>Informasi Kelas</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kelas <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="nama_kelas" 
                               x-model="namaKelas"
                               @input="onNamaInput()"
                               required 
                               class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" 
                               placeholder="Contoh: XII Rekayasa Perangkat Lunak 1">
                        <p class="text-[11px] text-slate-400 mt-1">Nama lengkap atau singkatan kelas.</p>
                        @error('nama_kelas') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Kode Kelas</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-navy bg-navy/10 border border-navy/20 px-2 py-0.5 rounded-full">
                                <span class="material-symbols-outlined text-xs">auto_awesome</span>
                                Otomatis
                            </span>
                        </label>
                        <div class="h-[42px] px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl flex items-center justify-between">
                            <span class="font-mono text-xs sm:text-sm font-bold tracking-wider" 
                                  :class="kodeKelas ? 'text-navy' : 'text-slate-400 italic font-normal'" 
                                  x-text="kodeKelas || 'Otomatis dibuat dari nama kelas'">
                                Otomatis dibuat dari nama kelas
                            </span>
                            <span class="material-symbols-outlined text-base text-slate-400">tag</span>
                        </div>
                        <input type="hidden" name="kode_kelas" :value="kodeKelas">
                        <p class="text-[11px] text-slate-400 mt-1">Dibuat otomatis oleh sistem dari nama kelas untuk identifikasi unik.</p>
                        @error('kode_kelas') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Wali Kelas (Opsional)</label>
                        <select name="id_wali_kelas" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                            <option value="">-- Pilih Wali Kelas (Bisa Ditentukan Nanti) --</option>
                            @foreach($waliKelasList as $wali)
                                <option value="{{ $wali->id }}" {{ old('id_wali_kelas') == $wali->id ? 'selected' : '' }}>
                                    {{ $wali->nama }} {{ $wali->nip ? '(NIP: ' . $wali->nip . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_wali_kelas') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 my-4"></div>

            <!-- Section 2: Parameter Iuran Kas -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading mb-3.5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600 text-lg">payments</span>
                    <span>Pengaturan Iuran Kas</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tipe Periode Iuran <span class="text-rose-500">*</span></label>
                        <select name="tipe_periode" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                            <option value="bulanan" {{ old('tipe_periode') === 'bulanan' ? 'selected' : '' }}>Bulanan (Januari - Desember)</option>
                            <option value="mingguan" {{ old('tipe_periode') === 'mingguan' ? 'selected' : '' }}>Mingguan (Minggu 1 - Minggu 24)</option>
                        </select>
                        @error('tipe_periode') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Standar per Periode (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="nominal_standar" value="{{ old('nominal_standar', 20000) }}" required min="0" step="1000" class="input-clean w-full pl-10 pr-3.5 py-2.5 text-xs sm:text-sm font-mono font-semibold text-slate-900 bg-white border border-slate-200 rounded-xl" placeholder="20000">
                        </div>
                        @error('nominal_standar') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                <a href="{{ route('admin.kelas.index') }}" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Batal</a>
                <button type="submit" class="btn-navy py-2.5 px-6 text-xs sm:text-sm font-bold rounded-xl shadow-sm inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Simpan Kelas</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function kelasCreateForm() {
    return {
        namaKelas: {!! json_encode(old('nama_kelas', '')) !!},
        kodeKelas: {!! json_encode(old('kode_kelas', '')) !!},

        generateCode(nama) {
            if (!nama) return '';
            let str = nama.trim().replace(/[^a-zA-Z0-9\s-]/g, '');
            let parts = str.split(/[\s-]+/).filter(Boolean);
            if (parts.length === 0) return '';
            
            if (parts.length > 1 && parts[0].toUpperCase() === 'KELAS') {
                parts.shift();
            }
            
            const stopWords = ['DAN', 'PADA', 'DI', 'UNTUK', 'KELAS'];
            let longWords = parts.filter(p => p.length > 3 && p !== p.toUpperCase());
            
            if (longWords.length >= 2) {
                let result = [];
                for (let i = 0; i < parts.length; i++) {
                    let p = parts[i];
                    let upper = p.toUpperCase();
                    if (stopWords.includes(upper)) continue;
                    
                    if (/^(X{0,3})(IX|IV|V?I{0,3})$/i.test(p) || /^\d+$/.test(p)) {
                        result.push(upper);
                    } else if (p.length <= 4 && p === p.toUpperCase()) {
                        result.push(upper);
                    } else {
                        result.push(upper.charAt(0));
                    }
                }
                let merged = [];
                let acronymBuffer = '';
                for (let item of result) {
                    if (/^(X{0,3})(IX|IV|V?I{0,3})$/i.test(item) || /^\d+$/.test(item)) {
                        if (acronymBuffer) {
                            merged.push(acronymBuffer);
                            acronymBuffer = '';
                        }
                        merged.push(item);
                    } else if (item.length === 1) {
                        acronymBuffer += item;
                    } else {
                        if (acronymBuffer) {
                            merged.push(acronymBuffer);
                            acronymBuffer = '';
                        }
                        merged.push(item);
                    }
                }
                if (acronymBuffer) merged.push(acronymBuffer);
                return merged.join('-').substring(0, 30);
            }
            return parts.map(p => p.toUpperCase()).join('-').substring(0, 30);
        },

        onNamaInput() {
            this.kodeKelas = this.generateCode(this.namaKelas);
        },

        init() {
            this.kodeKelas = this.generateCode(this.namaKelas);
        }
    };
}
</script>
</x-app-layout>
