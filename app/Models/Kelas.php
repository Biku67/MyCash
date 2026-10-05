<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'id_wali_kelas',
        'tipe_periode',
        'bulan_mulai',
        'bulan_selesai',
        'nominal_standar',
        'last_reset_at',
    ];

    protected $casts = [
        'nominal_standar' => 'decimal:2',
        'last_reset_at' => 'datetime',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(WaliKelas::class, 'id_wali_kelas');
    }

    public function bendahara()
    {
        return $this->hasMany(Bendahara::class, 'kode_kelas', 'kode_kelas');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kode_kelas', 'kode_kelas');
    }

    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'kode_kelas', 'kode_kelas');
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'kode_kelas', 'kode_kelas');
    }

    /**
     * Generate class code automatically from class name
     */
    public static function generateKodeKelas(?string $nama): string
    {
        if (empty($nama)) {
            return '';
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s-]/', '', $nama);
        $parts = preg_split('/[\s-]+/', trim($clean));
        $parts = array_values(array_filter($parts));

        if (empty($parts)) {
            return '';
        }

        if (count($parts) > 1 && strtoupper($parts[0]) === 'KELAS') {
            array_shift($parts);
        }

        $stopWords = ['DAN', 'PADA', 'DI', 'UNTUK', 'KELAS'];
        $longWords = array_filter($parts, function ($p) {
            return strlen($p) > 3 && $p !== strtoupper($p);
        });

        if (count($longWords) >= 2) {
            $result = [];
            foreach ($parts as $p) {
                $upper = strtoupper($p);
                if (in_array($upper, $stopWords)) {
                    continue;
                }

                if (preg_match('/^(X{0,3})(IX|IV|V?I{0,3})$/i', $p) || is_numeric($p)) {
                    $result[] = $upper;
                } elseif (strlen($p) <= 4 && $p === strtoupper($p)) {
                    $result[] = $upper;
                } else {
                    $result[] = substr($upper, 0, 1);
                }
            }

            $merged = [];
            $acronymBuffer = '';
            foreach ($result as $item) {
                if (preg_match('/^(X{0,3})(IX|IV|V?I{0,3})$/i', $item) || is_numeric($item)) {
                    if ($acronymBuffer !== '') {
                        $merged[] = $acronymBuffer;
                        $acronymBuffer = '';
                    }
                    $merged[] = $item;
                } elseif (strlen($item) === 1) {
                    $acronymBuffer .= $item;
                } else {
                    if ($acronymBuffer !== '') {
                        $merged[] = $acronymBuffer;
                        $acronymBuffer = '';
                    }
                    $merged[] = $item;
                }
            }
            if ($acronymBuffer !== '') {
                $merged[] = $acronymBuffer;
            }

            return substr(implode('-', $merged), 0, 30);
        }

        return substr(implode('-', array_map('strtoupper', $parts)), 0, 30);
    }

    /**
     * Generate unique class code automatically from class name
     */
    public static function generateUniqueKodeKelas(?string $nama, ?int $exceptId = null): string
    {
        $base = self::generateKodeKelas($nama) ?: 'KELAS-' . strtoupper(\Illuminate\Support\Str::random(4));
        $code = $base;
        $counter = 1;

        while (self::where('kode_kelas', $code)->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $counter++;
            $code = substr($base, 0, 26) . '-' . $counter;
        }

        return $code;
    }

    /**
     * Get list of month abbreviations with full Indonesian names
     */
    public static function getMonthList(): array
    {
        return [
            'Jan' => 'Januari',
            'Feb' => 'Februari',
            'Mar' => 'Maret',
            'Apr' => 'April',
            'Mei' => 'Mei',
            'Jun' => 'Juni',
            'Jul' => 'Juli',
            'Agt' => 'Agustus',
            'Sep' => 'September',
            'Okt' => 'Oktober',
            'Nov' => 'November',
            'Des' => 'Desember',
        ];
    }

    /**
     * Get array of active period strings based on class settings
     */
    public function getPeriods(): array
    {
        if ($this->tipe_periode === 'mingguan') {
            $periods = [];
            for ($i = 1; $i <= 24; $i++) {
                $periods[] = 'M' . $i;
            }
            return $periods;
        }

        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $start = $this->bulan_mulai ?: 'Jan';
        $end = $this->bulan_selesai ?: 'Des';

        $startIndex = array_search($start, $allMonths);
        $endIndex = array_search($end, $allMonths);

        if ($startIndex === false) $startIndex = 0;
        if ($endIndex === false) $endIndex = 11;

        if ($startIndex <= $endIndex) {
            return array_values(array_slice($allMonths, $startIndex, $endIndex - $startIndex + 1));
        }

        // Cross-year cycle (e.g. Jul s/d Jun)
        $firstPart = array_slice($allMonths, $startIndex);
        $secondPart = array_slice($allMonths, 0, $endIndex + 1);

        return array_values(array_merge($firstPart, $secondPart));
    }
}
