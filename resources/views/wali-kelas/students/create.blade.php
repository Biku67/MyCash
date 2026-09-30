<x-app-layout>
@section('page-title', 'Daftarkan Siswa Baru')

<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('wali-kelas.students.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Daftar Siswa
        </a>
    </div>

    <div class="card p-6 sm:p-8 border border-slate-200/80 shadow-sm rounded-2xl bg-white">
        <div class="mb-6 border-b border-slate-100 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">person_add</span>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Tambah Siswa Baru</h1>
                    <p class="text-slate-500 text-xs mt-1">Siswa akan otomatis dimasukkan ke kelas: <span class="font-semibold text-slate-800">{{ $kelas->nama_kelas }}</span>.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('wali-kelas.students.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Contoh: Budi Santoso">
                    @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIS (Nomor Induk Siswa) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" value="{{ old('nis') }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl font-mono" placeholder="Contoh: 20240001">
                    @error('nis')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Siswa <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="budi@siswa.id">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan untuk membuat otomatis dari NIS.</p>
                    @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp / HP <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="081234567890">
                    @error('phone')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Info Pembuatan Password Otomatis -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-navy/10 text-navy flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-xl">vpn_key</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-bold text-slate-800">Password Akun Dibuat Otomatis</h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-navy/10 text-navy border border-navy/20">
                            Sesuai NIS
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Password awal untuk login akun siswa disetel otomatis sama dengan nomor <strong class="text-slate-700">NIS</strong> siswa. Siswa dapat memperbarui password mereka melalui halaman profil setelah berhasil login.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('wali-kelas.students.index') }}" class="w-full sm:w-auto px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-semibold hover:bg-slate-50 transition-colors text-center">Batal</a>
                <button type="submit" class="w-full sm:w-auto btn-navy py-2.5 px-5 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-base">how_to_reg</span> Daftarkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
