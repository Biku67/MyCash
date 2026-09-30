<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Pengaturan Profil</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola identitas akun dan keamanan kata sandi Anda.</p>
        </div>

        <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
