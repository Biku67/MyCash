<section>
    <header class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-heading flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4F72] text-xl">manage_accounts</span>
                <span>Informasi Akun & Data Profil</span>
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Pembaruan data profil disesuaikan dengan skema database pengguna MyCash.
            </p>
        </div>
    </header>

    @php
        $roleName = match($user->role) {
            'admin' => 'Super Admin',
            'wali_kelas' => 'Wali Kelas',
            'bendahara' => 'Bendahara Kelas',
            'siswa' => 'Siswa',
            default => ucfirst(str_replace('_', ' ', $user->role)),
        };

        $roleBadgeColor = match($user->role) {
            'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'wali_kelas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'bendahara' => 'bg-amber-50 text-amber-700 border-amber-200',
            'siswa' => 'bg-sky-50 text-sky-700 border-sky-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };

        $currentPhone = old('no_hp', $user->waliKelas?->no_hp ?? $user->bendahara?->no_hp ?? $user->siswa?->no_hp ?? '');
        $hasRoleEntity = ($user->waliKelas || $user->bendahara || $user->siswa);
    @endphp

    <!-- Database Info Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-5">
        <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Peran (Role)</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold border {{ $roleBadgeColor }}">
                {{ $roleName }}
            </span>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Akun</span>
            <span class="inline-flex items-center gap-1 text-xs font-bold {{ $user->is_active ? 'text-emerald-700' : 'text-rose-700' }}">
                <span class="material-symbols-outlined text-sm">{{ $user->is_active ? 'check_circle' : 'cancel' }}</span>
                <span>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </span>
        </div>

        @if($user->siswa)
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIS</span>
                <span class="text-xs font-mono font-bold text-slate-800">{{ $user->siswa->nis ?: '-' }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kelas</span>
                <span class="text-xs font-bold text-slate-800">{{ $user->siswa->kelas?->nama_kelas ?? $user->siswa->kode_kelas }}</span>
            </div>
        @elseif($user->bendahara)
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIS</span>
                <span class="text-xs font-mono font-bold text-slate-800">{{ $user->bendahara->nis ?: '-' }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kelas Bertugas</span>
                <span class="text-xs font-bold text-slate-800">{{ $user->bendahara->kelas?->nama_kelas ?? $user->bendahara->kode_kelas }}</span>
            </div>
        @elseif($user->waliKelas)
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIP</span>
                <span class="text-xs font-mono font-bold text-slate-800">{{ $user->waliKelas->nip ?: '-' }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kelas Binaan</span>
                <span class="text-xs font-bold text-slate-800">{{ $user->waliKelas->kelas->pluck('nama_kelas')->implode(', ') ?: 'Belum ditugaskan' }}</span>
            </div>
        @else
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Akses Sistem</span>
                <span class="text-xs font-bold text-slate-800">Administrator</span>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Terdaftar Sejak</span>
                <span class="text-xs font-medium text-slate-700">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
            </div>
        @endif
    </div>

    <!-- Edit Form -->
    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nama Lengkap (users.name) -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">person</span>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                        class="input-clean w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                        placeholder="Nama lengkap Anda">
                </div>
                @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Email Akun (users.email) -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">mail</span>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                        class="input-clean w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                        placeholder="nama@email.com">
                </div>
                @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
        </div>

        @if($hasRoleEntity)
            <!-- Nomor Telepon / WA -->
            <div>
                <label for="no_hp" class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">call</span>
                    <input id="no_hp" name="no_hp" type="text" value="{{ $currentPhone }}"
                        class="input-clean w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72]"
                        placeholder="08xxxxxxxxxx">
                </div>
                @error('no_hp')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
        @endif

        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <div>
                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                         class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Profil berhasil diperbarui.</span>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn-navy px-5 py-2.5 text-xs sm:text-sm font-bold rounded-xl shadow-sm inline-flex items-center gap-2 hover:opacity-95 transition-all">
                <span class="material-symbols-outlined text-base">save</span>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</section>
