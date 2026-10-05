<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode_kelas
 * @property int $id_bendahara
 * @property int $id_kategori
 * @property string $jenis_transaksi
 * @property float $total_nominal
 * @property \Illuminate\Support\Carbon $tanggal_transaksi
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kas';

    protected $fillable = [
        'kode_kelas',
        'id_bendahara',
        'id_kategori',
        'jenis_transaksi',
        'total_nominal',
        'tanggal_transaksi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total_nominal' => 'decimal:2',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function bendahara()
    {
        return $this->belongsTo(Bendahara::class, 'id_bendahara');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksiKas::class, 'id_transaksi_kas');
    }

    public function rincian()
    {
        return $this->hasMany(DetailTransaksiKas::class, 'id_transaksi_kas');
    }
}
