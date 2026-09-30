<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bendahara extends Model
{
    use HasFactory;

    protected $table = 'bendahara';

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

    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'id_bendahara');
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'id_bendahara');
    }
}
