<x-app-layout>
@section('page-title', 'Edit Data Siswa')

<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('wali-kelas.students.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Daftar Siswa
        </a>
    </div>

    <div class="card p-6 sm:p-8 border border-slate-200/80 shadow-sm rounded-2xl bg-white">
        <div class="mb-6 border-b border-slate-100 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm">
                    {{ substr($student->nama, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Edit Data Siswa</h1>
                    <p class="text-slate-500 text-xs mt-1">Perbarui informasi siswa <span class="font-semibold text-slate-800">{{ $student->nama }}</span> di kelas {{ $kelas->nama_kelas }}.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('wali-kelas.students.update', $student->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $student->nama) }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                    @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIS (Nomor Induk Siswa) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" required class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl font-mono">
                    @error('nis')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Siswa <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="budi@siswa.id">
                    @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp / HP <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $student->no_hp) }}" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="081234567890">
                    @error('phone')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('wali-kelas.students.index') }}" class="w-full sm:w-auto px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-semibold hover:bg-slate-50 transition-colors text-center">Batal</a>
                <button type="submit" class="w-full sm:w-auto btn-navy py-2.5 px-5 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Kredensial & Keamanan Akun Siswa -->
    <div class="card p-6 border border-slate-200/80 shadow-sm rounded-2xl bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-2xl">lock_reset</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Reset Password Akun Siswa</h3>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                        Jika siswa lupa password, Anda dapat meresetnya kembali ke nomor <strong class="text-slate-700">NIS ({{ $student->nis }})</strong> secara langsung.
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1 font-mono">
                        Email login: <span class="text-slate-700 font-medium">{{ $student->user->email ?? '-' }}</span>
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('wali-kelas.students.resetPassword', $student->id) }}" onsubmit="return confirmResetPassword(event, '{{ $student->nama }}', '{{ $student->nis }}')">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition-all inline-flex items-center justify-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-base">lock_reset</span>
                    <span>Reset ke NIS</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmResetPassword(event, name, nis) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        title: 'Reset Password Siswa?',
        html: `Password login untuk siswa <strong>${name}</strong> akan direset ke nomor NIS default: <br><span class="font-mono text-base font-bold text-navy mt-1.5 inline-block bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">${nis}</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1B4F72',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Ya, Reset Password',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
</x-app-layout>
