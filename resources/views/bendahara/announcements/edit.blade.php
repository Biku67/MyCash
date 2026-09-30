<x-app-layout>
@section('page-title', 'Edit Pengumuman Kas')

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Navigation -->
    <div>
        <a href="{{ route('bendahara.announcements.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Pengumuman
        </a>
    </div>

    <!-- Main Card -->
    <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="mb-6 pb-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Edit Pengumuman</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Perbarui judul atau isi pengumuman kelas <span class="font-semibold text-slate-700">{{ $kelas ? $kelas->nama_kelas : 'Anda' }}</span>.
                </p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">edit_note</span>
            </div>
        </div>

        <form method="POST" action="{{ route('bendahara.announcements.update', $announcement->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Judul Pengumuman -->
            <div>
                <label for="judul" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $announcement->judul) }}" required maxlength="255"
                       class="input-clean w-full px-4 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white rounded-xl border border-slate-200">
                @error('judul')
                    <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Isi Pengumuman -->
            <div>
                <label for="isi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Isi Pengumuman / Pesan <span class="text-rose-500">*</span>
                </label>
                <textarea id="isi" name="isi" rows="6" required
                          class="input-clean w-full px-4 py-3 text-xs sm:text-sm font-medium text-slate-800 bg-white rounded-xl border border-slate-200 leading-relaxed">{{ old('isi', $announcement->isi) }}</textarea>
                @error('isi')
                    <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('bendahara.announcements.index') }}" 
                   class="py-2.5 px-4 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="btn-navy py-2.5 px-5 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-2 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>
