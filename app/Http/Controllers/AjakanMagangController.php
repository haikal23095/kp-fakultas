<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMagangInvitation;
use App\Models\SuratMagang;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AjakanMagangController extends Controller
{
    /**
     * Display a listing of invitations for the logged-in student.
     */
    public function index()
    {
        $user = Auth::user();

        // Get mahasiswa ID
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Get all invitations for this mahasiswa with related data
        $invitations = SuratMagangInvitation::where('id_mahasiswa_diundang', $mahasiswa->Id_Mahasiswa)
            ->with([
                'suratMagang',
                'mahasiswaPengundang.user'
            ])
            ->orderBy('invited_at', 'desc')
            ->get();

        return view('mahasiswa.ajakan_magang', compact('invitations'));
    }

    /**
     * Accept an invitation.
     */
    public function accept($id)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $invitation = SuratMagangInvitation::where('id_no', $id)
            ->where('id_mahasiswa_diundang', $mahasiswa->Id_Mahasiswa)
            ->firstOrFail();

        // Check if already responded
        if ($invitation->status !== 'pending') {
            return redirect()->route('mahasiswa.ajakan-magang')
                ->with('warning', 'Undangan ini sudah direspons sebelumnya.');
        }

        // Update invitation status
        $invitation->update([
            'status' => 'accepted',
            'responded_at' => Carbon::now(),
        ]);

        // Send notification to the inviter (mahasiswa pengundang)
        $mahasiswaPengundang = Mahasiswa::find($invitation->id_mahasiswa_pengundang);
        if ($mahasiswaPengundang && $mahasiswaPengundang->Id_User) {
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => $mahasiswa->Nama_Mahasiswa . ' telah menerima undangan magang Anda.',
                'Dest_user' => $mahasiswaPengundang->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode([
                    'invitation_id' => $invitation->id_no,
                    'surat_magang_id' => $invitation->id_surat_magang
                ])
            ]);
        }

        // Check if all invitations for this surat magang are accepted
        $allInvitations = SuratMagangInvitation::where('id_surat_magang', $invitation->id_surat_magang)->get();
        $allAccepted = $allInvitations->every(function ($inv) {
            return $inv->status === 'accepted';
        });

        // If all accepted, update Surat_Magang status to 'Diajukan-ke-koordinator'
        if ($allAccepted) {
            $suratMagang = SuratMagang::find($invitation->id_surat_magang);
            if ($suratMagang) {
                $suratMagang->update([
                    'Status' => 'Diajukan-ke-koordinator'
                ]);
            }
        }

        return redirect()->route('mahasiswa.ajakan-magang')
            ->with('success', 'Undangan magang berhasil diterima!');
    }

    /**
     * Reject an invitation.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $invitation = SuratMagangInvitation::where('id_no', $id)
            ->where('id_mahasiswa_diundang', $mahasiswa->Id_Mahasiswa)
            ->firstOrFail();

        // Check if already responded
        if ($invitation->status !== 'pending') {
            return redirect()->route('mahasiswa.ajakan-magang')
                ->with('warning', 'Undangan ini sudah direspons sebelumnya.');
        }

        // Update invitation status
        $invitation->update([
            'status' => 'rejected',
            'keterangan' => $request->input('keterangan'),
            'responded_at' => Carbon::now(),
        ]);

        // If rejected, update Surat_Magang status to 'Ditolak'
        $suratMagang = SuratMagang::find($invitation->id_surat_magang);
        if ($suratMagang) {
            $suratMagang->update([
                'Status' => 'Ditolak',
                'Komentar' => 'Undangan ditolak oleh ' . ($mahasiswa->Nama_Mahasiswa ?? 'Mahasiswa') . '. Alasan: ' . ($request->input('keterangan') ?? 'Tidak ada alasan')
            ]);
        }

        return redirect()->route('mahasiswa.ajakan-magang')
            ->with('success', 'Undangan magang berhasil ditolak.');
    }
}
