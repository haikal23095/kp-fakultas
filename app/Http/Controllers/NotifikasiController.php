<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan halaman daftar notifikasi
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua notifikasi untuk user yang sedang login
        $notifikasis = Notifikasi::forUser($user->Id_User)
            ->with(['sourceUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notifikasi::forUser($user->Id_User)
            ->unread()
            ->count();

        // Determine the layout based on user role
        $roleId = $user->Id_Role;
        $layout = match ($roleId) {
            1 => 'admin_prodi',
            2 => 'dekan',
            3 => 'kajur',
            4 => 'kaprodi',
            5 => 'dosen',
            6 => 'mahasiswa',
            default => 'mahasiswa',
        };

        return view('notifikasi.index', compact('notifikasis', 'unreadCount', 'layout'));
    }

    /**
     * Menampilkan dropdown notifikasi (AJAX)
     */
    public function getRecent()
    {
        $user = Auth::user();

        // Ambil 5 notifikasi terbaru
        $notifikasis = Notifikasi::forUser($user->Id_User)
            ->with(['sourceUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notifikasi::forUser($user->Id_User)
            ->unread()
            ->count();

        return response()->json([
            'notifikasis' => $notifikasis,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('Id_Notifikasi', $id)
            ->where('Dest_User', $user->Id_User)
            ->firstOrFail();

        $notifikasi->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notifikasi::forUser($user->Id_User)
            ->unread()
            ->update(['Is_Read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('Id_Notifikasi', $id)
            ->where('Dest_User', $user->Id_User)
            ->firstOrFail();

        $notifikasi->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }
}
