<x-app-layout>
@section('page-title', 'Edit Wali Kelas')

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.wali-kelas.index') }}" class="hover:text-slate-900 transition-colors">Kelola Wali Kelas</a>
                <span>/</span>
                <span class="text-slate-700 font-medium">Edit Wali Kelas</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Perbarui Data Wali Kelas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ubah nama guru, NIP, nomor handphone, atau setel ulang kata sandi.</p>
        </div>
        <a href="{{ route('admin.wali-kelas.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="card p-6 sm:p-7 border border-slate-200/80 rounded-2xl shadow-sm">
        <form method="POST" action="{{ route('admin.wali-kelas.update', $waliKelas->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Section 1: Profil & Data Guru -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading mb-3.5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600 text-lg">badge</span>
                    <span>Informasi Pribadi & Kontak</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $waliKelas->nama) }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Contoh: Drs. Fuad Hasan, M.Pd">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" value="{{ old('nip', $waliKelas->nip) }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="19780101xxxxxxxxxx">
                        @error('nip') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $waliKelas->no_hp) }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="08xxxxxxxxxx">
                        @error('no_hp') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 my-4"></div>

            <!-- Section 2: Kredensial Login -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading mb-3.5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600 text-lg">lock</span>
                    <span>Kredensial Akun Login</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $waliKelas->user->email ?? '') }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="walikelas@sekolah.sch.id">
                        @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password Baru (Opsional)</label>
                        <input type="password" name="password" minlength="8" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Kosongkan jika tidak ingin diubah">
                        <p class="text-[11px] text-slate-400 mt-1">Minimal 8 karakter jika diisi.</p>
                        @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" minlength="8" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                <a href="{{ route('admin.wali-kelas.index') }}" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Batal</a>
                <button type="submit" class="btn-navy py-2.5 px-6 text-xs sm:text-sm font-bold rounded-xl shadow-sm inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
