<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Uang Kas', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Pemasukan Lainnya', 'tipe' => 'pemasukan'],
            ['nama_kategori' => 'Pembelian ATK', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Kegiatan Kelas', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Operasional Kelas', 'tipe' => 'pengeluaran'],
            ['nama_kategori' => 'Pengeluaran Lainnya', 'tipe' => 'pengeluaran'],
        ];

        foreach ($categories as $cat) {
            Kategori::firstOrCreate(['nama_kategori' => $cat['nama_kategori']], $cat);
        }
    }
}
