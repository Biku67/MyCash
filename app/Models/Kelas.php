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
        'tahun_ajaran',
        'nominal_standar',
        'last_reset_at',
    ];

    protected $casts = [
        'nominal_standar' => 'decimal:2',
        'last_reset_at' => 'datetime',
    ];

    /**
     * Get dynamic list of academic year options
     */
    public static function getTahunAjaranOptions(): array
    {
        $currentYear = (int)date('Y');
        $years = [];
        for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++) {
            $years[] = $y . '/' . ($y + 1);
        }
        return $years;
    }

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
     * Get array of active period items based on class settings and academic year
     * Returns array of ['key' => '2025-07', 'label' => 'Jul 25', 'month' => 'Jul', 'year' => 2025]
     */
    public function getPeriods(?string $tahunAjaran = null): array
    {
        $ta = $tahunAjaran ?: ($this->tahun_ajaran ?: '2025/2026');
        $parts = explode('/', $ta);
        $startYear = (int)($parts[0] ?? date('Y'));
        $endYear = (int)($parts[1] ?? ($startYear + 1));
        $sy2 = substr((string)$startYear, -2);
        $ey2 = substr((string)$endYear, -2);

        if ($this->tipe_periode === 'mingguan') {
            $periods = [];
            for ($i = 1; $i <= 24; $i++) {
                $pad = str_pad($i, 2, '0', STR_PAD_LEFT);
                $periods[] = [
                    'key' => 'M' . $i,
                    'label' => 'M' . $pad . ' ' . $sy2 . '/' . $ey2,
                    'month' => 'M' . $i,
                    'year' => $startYear,
                ];
            }
            return $periods;
        }

        $allMonths = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $monthNums = [
            'Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','Mei'=>'05','Jun'=>'06',
            'Jul'=>'07','Agt'=>'08','Sep'=>'09','Okt'=>'10','Nov'=>'11','Des'=>'12'
        ];

        $start = $this->bulan_mulai ?: 'Jul';
        $end = $this->bulan_selesai ?: 'Jun';

        $startIndex = array_search($start, $allMonths);
        $endIndex = array_search($end, $allMonths);

        if ($startIndex === false) $startIndex = 6;
        if ($endIndex === false) $endIndex = 5;

        $monthList = [];
        if ($startIndex <= $endIndex) {
            $monthList = array_values(array_slice($allMonths, $startIndex, $endIndex - $startIndex + 1));
        } else {
            // Cross-year cycle (e.g. Jul s/d Jun)
            $firstPart = array_slice($allMonths, $startIndex);
            $secondPart = array_slice($allMonths, 0, $endIndex + 1);
            $monthList = array_values(array_merge($firstPart, $secondPart));
        }

        $periods = [];
        foreach ($monthList as $m) {
            $mIdx = array_search($m, $allMonths);
            // In academic year: Jul-Des (index 6-11) is startYear; Jan-Jun (index 0-5) is endYear
            $year = ($mIdx >= 6) ? $startYear : $endYear;
            $y2 = substr((string)$year, -2);
            $key = $year . '-' . $monthNums[$m];
            $label = $m . ' ' . $y2;

            $periods[] = [
                'key' => $key,
                'label' => $label,
                'month' => $m,
                'year' => $year,
            ];
        }

        return $periods;
    }

    /**
     * Get array of only the period keys (e.g. ['2025-07', '2025-08', ...])
     */
    public function getPeriodKeys(?string $tahunAjaran = null): array
    {
        return array_column($this->getPeriods($tahunAjaran), 'key');
    }
}
