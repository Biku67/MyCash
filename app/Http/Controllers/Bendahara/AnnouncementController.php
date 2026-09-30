<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Get the authenticated bendahara record.
     */
    private function getBendahara()
    {
        $bendahara = auth()->user()->bendahara;
        if (!$bendahara) {
            abort(403, 'Akses ditolak. Anda belum terdaftar sebagai bendahara kelas.');
        }
        return $bendahara;
    }

    /**
     * Display a listing of announcements for the bendahara's class.
     */
    public function index(Request $request)
    {
        $bendahara = $this->getBendahara();
        $kelas = $bendahara->kelas;

        $type = $request->input('type', 'all'); // 'all', 'pengumuman', 'pembayaran'
        $search = $request->input('search');

        $query = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)
            ->withCount([
                'penerima as total_penerima',
                'penerima as dibaca_penerima' => function ($query) {
                    $query->where('is_read', true);
                }
            ])
            ->with(['penerima.siswa']);

        if ($type === 'pengumuman') {
            $query->where('judul', 'not like', 'Pembayaran Kas%');
        } elseif ($type === 'pembayaran') {
            $query->where('judul', 'like', 'Pembayaran Kas%');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%")
                  ->orWhereHas('penerima.siswa', function ($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        // Counts for tab badges
        $countAll = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)->count();
        $countPengumuman = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)->where('judul', 'not like', 'Pembayaran Kas%')->count();
        $countPembayaran = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)->where('judul', 'like', 'Pembayaran Kas%')->count();

        $totalStudents = Siswa::where('kode_kelas', $bendahara->kode_kelas)->count();

        return view('bendahara.announcements.index', compact(
            'bendahara', 'kelas', 'announcements', 'totalStudents', 'type', 'search',
            'countAll', 'countPengumuman', 'countPembayaran'
        ));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        $bendahara = $this->getBendahara();
        $kelas = $bendahara->kelas;
        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->orderBy('nama', 'asc')->get();
        $totalStudents = $students->count();

        return view('bendahara.announcements.create', compact('bendahara', 'kelas', 'students', 'totalStudents'));
    }

    /**
     * Store a newly created announcement and distribute to all students or selected individual students.
     */
    public function store(Request $request)
    {
        $bendahara = $this->getBendahara();

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'target_type' => 'required|in:all,selected',
            'student_ids' => 'required_if:target_type,selected|array|min:1',
            'student_ids.*' => 'exists:siswa,id',
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 255 karakter.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'target_type.required' => 'Pilih sasaran penerima pengumuman.',
            'student_ids.required_if' => 'Pilih minimal satu siswa penerima jika memilih opsi perorangan.',
            'student_ids.min' => 'Pilih minimal satu siswa penerima jika memilih opsi perorangan.',
        ]);

        $recipientCount = 0;

        DB::transaction(function () use ($bendahara, $request, &$recipientCount) {
            $announcement = Pengumuman::create([
                'kode_kelas' => $bendahara->kode_kelas,
                'id_bendahara' => $bendahara->id,
                'judul' => $request->judul,
                'isi' => $request->isi,
            ]);

            if ($request->target_type === 'selected') {
                $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)
                    ->whereIn('id', $request->student_ids ?? [])
                    ->pluck('id');
            } else {
                $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->pluck('id');
            }

            $recipientCount = $students->count();

            $penerimaData = [];
            $now = now();
            foreach ($students as $studentId) {
                $penerimaData[] = [
                    'id_pengumuman' => $announcement->id,
                    'id_siswa' => $studentId,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($penerimaData)) {
                PengumumanPenerima::insert($penerimaData);
            }
        });

        $message = $request->target_type === 'selected'
            ? "Pengumuman berhasil dikirim ke {$recipientCount} siswa terpilih."
            : "Pengumuman berhasil dikirim ke seluruh {$recipientCount} siswa.";

        return redirect()->route('bendahara.announcements.index')
            ->with('success', $message);
    }

    /**
     * Display the specified announcement with recipient read breakdown.
     */
    public function show($id)
    {
        $bendahara = $this->getBendahara();

        $announcement = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)
            ->where('id', $id)
            ->with(['kelas', 'bendahara'])
            ->firstOrFail();

        $recipients = PengumumanPenerima::where('id_pengumuman', $announcement->id)
            ->with('siswa')
            ->get()
            ->sortBy(fn($item) => $item->siswa->nama ?? '');

        $readCount = $recipients->where('is_read', true)->count();
        $totalRecipients = $recipients->count();
        $totalClassStudents = Siswa::where('kode_kelas', $bendahara->kode_kelas)->count();
        $isAllClass = ($totalRecipients >= $totalClassStudents && $totalClassStudents > 0);

        return view('bendahara.announcements.show', compact('bendahara', 'announcement', 'recipients', 'readCount', 'totalRecipients', 'totalClassStudents', 'isAllClass'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($id)
    {
        $bendahara = $this->getBendahara();

        $announcement = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)
            ->where('id', $id)
            ->firstOrFail();

        if (str_contains($announcement->judul, 'Pembayaran Kas')) {
            return redirect()->route('bendahara.announcements.index')
                ->with('error', 'Notifikasi pembayaran kas dibuat otomatis dan tidak dapat diedit secara manual.');
        }

        $kelas = $bendahara->kelas;

        return view('bendahara.announcements.edit', compact('bendahara', 'announcement', 'kelas'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, $id)
    {
        $bendahara = $this->getBendahara();

        $announcement = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 255 karakter.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
        ]);

        $announcement->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        return redirect()->route('bendahara.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy($id)
    {
        $bendahara = $this->getBendahara();

        $announcement = Pengumuman::where('kode_kelas', $bendahara->kode_kelas)
            ->where('id', $id)
            ->firstOrFail();

        $announcement->delete();

        return redirect()->route('bendahara.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
