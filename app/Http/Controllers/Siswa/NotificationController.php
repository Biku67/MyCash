<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengumumanPenerima;

class NotificationController extends Controller
{
    /**
     * Get the authenticated siswa record.
     */
    private function getSiswa()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda belum terdaftar sebagai siswa.');
        }
        return $siswa;
    }

    /**
     * Display a listing of announcements / notifications received by the student.
     */
    public function index()
    {
        $siswa = $this->getSiswa();

        $notifications = PengumumanPenerima::where('id_siswa', $siswa->id)
            ->with(['pengumuman.bendahara.user', 'pengumuman.kelas'])
            ->latest()
            ->paginate(10);

        $unreadCount = PengumumanPenerima::where('id_siswa', $siswa->id)
            ->where('is_read', false)
            ->count();

        return view('siswa.notifications.index', compact('siswa', 'notifications', 'unreadCount'));
    }

    /**
     * Display the specified announcement and mark it as read.
     */
    public function show($id)
    {
        $siswa = $this->getSiswa();

        $recipient = PengumumanPenerima::where('id_siswa', $siswa->id)
            ->where('id', $id)
            ->with(['pengumuman.bendahara.user', 'pengumuman.kelas'])
            ->firstOrFail();

        if (!$recipient->is_read) {
            $recipient->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return view('siswa.notifications.show', compact('siswa', 'recipient'));
    }

    /**
     * Mark all notifications as read for the student.
     */
    public function markAllAsRead()
    {
        $siswa = $this->getSiswa();

        PengumumanPenerima::where('id_siswa', $siswa->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua pengumuman telah ditandai sudah dibaca.');
    }

    /**
     * Check for new unread notifications in realtime (polling endpoint).
     */
    public function checkUnread(Request $request)
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return response()->json(['success' => false, 'unread_count' => 0, 'has_new' => false]);
        }

        $unreadCount = PengumumanPenerima::where('id_siswa', $siswa->id)
            ->where('is_read', false)
            ->count();

        $hasLastId = $request->has('last_id');
        $lastId = (int)$request->input('last_id', 0);

        // Cari notifikasi baru yang ID-nya lebih besar dari last_id
        $latest = PengumumanPenerima::where('id_siswa', $siswa->id)
            ->where('is_read', false)
            ->when($hasLastId, function ($q) use ($lastId) {
                $q->where('id', '>', $lastId);
            })
            ->with(['pengumuman.bendahara.user'])
            ->latest('id')
            ->first();

        $maxId = (int)(PengumumanPenerima::where('id_siswa', $siswa->id)->max('id') ?? 0);

        if ($latest && $latest->pengumuman && $hasLastId) {
            $ann = $latest->pengumuman;
            $isPayment = str_contains($ann->judul, 'Pembayaran Kas');
            $cardHtml = view('siswa.notifications.partials.item', ['item' => $latest])->render();

            return response()->json([
                'success' => true,
                'has_new' => true,
                'unread_count' => $unreadCount,
                'last_id' => $maxId,
                'card_html' => $cardHtml,
                'notification' => [
                    'id' => $latest->id,
                    'judul' => $ann->judul,
                    'isi' => \Illuminate\Support\Str::limit(strip_tags($ann->isi), 140),
                    'is_payment' => $isPayment,
                    'sender' => $ann->bendahara->user->name ?? 'Bendahara Kelas',
                    'time' => \Carbon\Carbon::parse($ann->created_at)->translatedFormat('H:i') . ' WIB',
                    'url' => route('siswa.notifications.show', $latest->id),
                    'card_html' => $cardHtml,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'has_new' => false,
            'unread_count' => $unreadCount,
            'last_id' => $maxId,
        ]);
    }
}
