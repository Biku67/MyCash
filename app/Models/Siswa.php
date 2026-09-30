<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'kode_kelas',
        'nama',
        'nis',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function detailTransaksiKas()
    {
        return $this->hasMany(DetailTransaksiKas::class, 'id_siswa');
    }

    public function pengumumanPenerima()
    {
        return $this->hasMany(PengumumanPenerima::class, 'id_siswa');
    }
}
