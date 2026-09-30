<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\Kelas;
use App\Models\Bendahara;
use App\Models\Siswa;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            KategoriSeeder::class,
        ]);

        // ─── 1. Super Admin ──────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mycash.app'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'is_active'=> true,
            ]
        );
        $adminUser->assignRole('admin');

        // ─── 2. Wali Kelas ───────────────────────────────
        $waliUser = User::firstOrCreate(
            ['email' => 'walikelas@mycash.app'],
            [
                'name'     => 'Pak Ali',
                'password' => Hash::make('password'),
                'role'     => 'wali_kelas',
                'is_active'=> true,
            ]
        );
        $waliUser->assignRole('wali_kelas');

        $waliProfil = WaliKelas::firstOrCreate(
            ['user_id' => $waliUser->id],
            [
                'nama'  => 'Pak Ali',
                'nip'   => '197801012005012001',
                'no_hp' => '081299887766',
            ]
        );

        // ─── 3. Kelas ────────────────────────────────────
        $kelas = Kelas::firstOrCreate(
            ['kode_kelas' => 'XII-RPL-1'],
            [
                'nama_kelas'      => 'XII RPL 1',
                'id_wali_kelas'   => $waliProfil->id,
                'tipe_periode'    => 'bulanan',
                'nominal_standar' => 20000.00,
            ]
        );

        // ─── 4. Bendahara ────────────────────────────────
        $bendaharaUser = User::firstOrCreate(
            ['email' => 'bendahara@mycash.app'],
            [
                'name'     => 'Michael Gunawan',
                'password' => Hash::make('password'),
                'role'     => 'bendahara',
                'is_active'=> true,
            ]
        );
        $bendaharaUser->assignRole('bendahara');

        $bendaharaProfil = Bendahara::firstOrCreate(
            ['user_id' => $bendaharaUser->id],
            [
                'kode_kelas' => $kelas->kode_kelas,
                'nama'       => 'Michael Gunawan',
                'nis'        => '20240001',
                'no_hp'      => '081234567890',
            ]
        );

        // ─── 5. Siswa ────────────────────────────────────
        $students = [
            ['name' => 'Budi Santoso',     'nis' => '20240002', 'email' => 'budi@siswa.app'],
            ['name' => 'Siti Rahayu',      'nis' => '20240003', 'email' => 'siti@siswa.app'],
            ['name' => 'Andi Prasetyo',    'nis' => '20240004', 'email' => 'andi@siswa.app'],
            ['name' => 'Dewi Lestari',     'nis' => '20240005', 'email' => 'dewi@siswa.app'],
            ['name' => 'Rizki Firmansyah', 'nis' => '20240006', 'email' => 'rizki@siswa.app'],
        ];

        foreach ($students as $sData) {
            $sUser = User::firstOrCreate(
                ['email' => $sData['email']],
                [
                    'name'     => $sData['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'is_active'=> true,
                ]
            );
            $sUser->assignRole('siswa');

            Siswa::firstOrCreate(
                ['user_id' => $sUser->id],
                [
                    'kode_kelas' => $kelas->kode_kelas,
                    'nama'       => $sData['name'],
                    'nis'        => $sData['nis'],
                    'no_hp'      => '0812' . rand(10000000, 99999999),
                ]
            );
        }
    }
}
