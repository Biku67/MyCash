<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'log_transaksi_kas';

    protected $fillable = [
        'id_transaksi_kas',
        'kode_kelas',
        'user_id',
        'aksi',
        'alasan',
        'data_sebelumnya',
        'data_sesudahnya',
        'is_read',
    ];

    protected $casts = [
        'data_sebelumnya' => 'array',
        'data_sesudahnya' => 'array',
        'is_read' => 'boolean',
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
        return $this->belongsTo(TransaksiKas::class, 'id_transaksi_kas');
    }
}
