<x-app-layout>
    @section('page-title', 'Pengaturan Profil')

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Pengaturan Profil</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola identitas akun dan perbarui kata sandi Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span>Status: {{ $user->is_active ? 'Akun Aktif' : 'Nonaktif' }}</span>
                </span>
            </div>
        </div>

        <!-- Section 1: Informasi Profil & Database -->
        <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm bg-white">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Section 2: Keamanan Kata Sandi -->
        <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm bg-white">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>
