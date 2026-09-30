<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'tipe',
    ];

    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'id_kategori');
    }
}
