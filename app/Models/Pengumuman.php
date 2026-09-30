<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'kode_kelas',
        'id_bendahara',
        'judul',
        'isi',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function bendahara()
    {
        return $this->belongsTo(Bendahara::class, 'id_bendahara');
    }

    public function penerima()
    {
        return $this->hasMany(PengumumanPenerima::class, 'id_pengumuman');
    }
}
