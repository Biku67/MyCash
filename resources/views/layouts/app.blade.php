@if(Auth::user()->hasRole('admin'))
    <x-superadmin-layout>
        {{ $slot }}
    </x-superadmin-layout>
@elseif(Auth::user()->hasRole('bendahara'))
    <x-bendahara-layout>
        {{ $slot }}
    </x-bendahara-layout>
@elseif(Auth::user()->hasRole('wali_kelas'))
    <x-wali-kelas-layout>
        {{ $slot }}
    </x-wali-kelas-layout>
@else
    <x-siswa-layout>
        {{ $slot }}
    </x-siswa-layout>
@endif
