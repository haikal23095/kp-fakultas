<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\SuratMagang;
use App\Models\JenisSurat;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\SuratMagangInvitation;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SuratPengantarMagangController extends Controller
{
    /**
     * Menyimpan pengajuan Surat Pengantar Magang/KP
     */
    public function store(Request $request)
    {
        // Check if files were uploaded (handle PHP upload limits)
        if (!$request->hasFile('file_pendukung_magang')) {
            return redirect()->back()
                ->with('error', 'File Proposal tidak ditemukan. Pastikan ukuran file tidak melebihi 2MB dan format adalah PDF.')
                ->withInput();
        }

        if (!$request->hasFile('file_tanda_tangan')) {
            return redirect()->back()
                ->with('error', 'File Tanda Tangan tidak ditemukan. Pastikan ukuran file tidak melebihi 1MB dan format adalah JPG/PNG.')
                ->withInput();
        }

        // === 1. VALIDASI DATA ===
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|numeric',
            'data_spesifik.dosen_pembimbing_1' => 'required|string',
            'data_spesifik.nama_instansi' => 'required|string|max:255',
            'data_spesifik.alamat_instansi' => 'required|string|max:500',
            'data_spesifik.judul_penelitian' => 'nullable|string|max:255',
            'data_spesifik.tanggal_mulai' => 'required|date',
            'data_spesifik.tanggal_selesai' => 'required|date|after_or_equal:data_spesifik.tanggal_mulai',
            'file_pendukung_magang' => 'required|file|mimes:pdf|max:2048',
            'file_tanda_tangan' => 'required|file|image|mimes:jpg,jpeg,png|max:1024',
            'mahasiswa' => 'required|array|min:1|max:5',
            'mahasiswa.*.nama' => 'required|string|max:255',
            'mahasiswa.*.nim' => 'required|numeric',
            'mahasiswa.*.jurusan' => 'required|string|max:255',
            'mahasiswa.*.angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ], [
            'data_spesifik.dosen_pembimbing_1.required' => 'Dosen pembimbing wajib dipilih',
            'data_spesifik.nama_instansi.required' => 'Nama instansi/perusahaan wajib diisi',
            'data_spesifik.alamat_instansi.required' => 'Alamat instansi wajib diisi',
            'data_spesifik.tanggal_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.required' => 'Tanggal selesai magang wajib diisi.',
            'data_spesifik.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'file_pendukung_magang.required' => 'Proposal wajib diunggah.',
            'file_pendukung_magang.mimes' => 'Proposal harus berformat PDF.',
            'file_pendukung_magang.max' => 'Ukuran proposal maksimal 2MB.',
            'file_tanda_tangan.required' => 'Foto tanda tangan wajib diunggah.',
            'file_tanda_tangan.image' => 'File tanda tangan harus berupa gambar.',
            'file_tanda_tangan.mimes' => 'Format tanda tangan harus JPG, JPEG, atau PNG.',
            'file_tanda_tangan.max' => 'Ukuran file tanda tangan maksimal 1MB.',
            'mahasiswa.required' => 'Minimal harus ada 1 mahasiswa.',
            'mahasiswa.*.nama.required' => 'Nama mahasiswa wajib diisi.',
            'mahasiswa.*.nim.required' => 'NIM mahasiswa wajib diisi.',
            'mahasiswa.*.jurusan.required' => 'Jurusan mahasiswa wajib diisi.',
            'mahasiswa.*.angkatan.required' => 'Angkatan mahasiswa wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // === 2. INISIALISASI VARIABEL ===
        $mahasiswaId = Auth::id();
        $jenisSuratId = $request->input('Id_Jenis_Surat');

        // === 3. UPLOAD FILE ===
        try {
            $filePendukungPath = $request->file('file_pendukung_magang')->store('file_pendukung_magang', 'public');
            $fileTTDPath = $request->file('file_tanda_tangan')->store('file_tanda_tangan', 'public');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }

        // === 4. CEK APAKAH ADA TEMAN YANG DIAJAK ===
        $mahasiswaPembuat = Mahasiswa::where('Id_User', Auth::id())
            ->with('prodi.fakultas') // Eager load prodi dan fakultas
            ->first();

        if (!$mahasiswaPembuat) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $mahasiswaList = $request->mahasiswa;
        $adaTemanDiajak = count($mahasiswaList) > 1; // Lebih dari 1 = ada teman

        // Ambil Kaprodi yang sesuai dengan prodi mahasiswa
        $kaprodiUser = \App\Models\User::where('Id_Role', 4)
            ->where(function ($query) use ($mahasiswaPembuat) {
                $query->whereHas('dosen', function ($q) use ($mahasiswaPembuat) {
                    $q->where('Id_Prodi', $mahasiswaPembuat->Id_Prodi);
                })
                    ->orWhereHas('pegawai', function ($q) use ($mahasiswaPembuat) {
                        $q->where('Id_Prodi', $mahasiswaPembuat->Id_Prodi);
                    });
            })
            ->with(['dosen', 'pegawai'])
            ->first();

        $kaprodiId = null;
        if ($kaprodiUser) {
            if ($kaprodiUser->dosen) {
                $kaprodiId = $kaprodiUser->dosen->Id_Dosen;
            } elseif ($kaprodiUser->pegawai) {
                // Jika Kaprodi adalah Pegawai, simpan Id_Pegawai ke Nama_Koordinator
                $kaprodiId = $kaprodiUser->pegawai->Id_Pegawai;
            }
        }

        // Ambil Id_Dekan dari Fakultas (Mahasiswa → Prodi → Fakultas → Id_Dekan)
        $idDekan = null;
        if ($mahasiswaPembuat->prodi && $mahasiswaPembuat->prodi->fakultas) {
            $idDekan = $mahasiswaPembuat->prodi->fakultas->Id_Dekan;
        }

        \Log::info('[MAGANG] Dekan info', [
            'mahasiswa_prodi' => $mahasiswaPembuat->Id_Prodi,
            'fakultas_id' => $mahasiswaPembuat->prodi->Id_Fakultas ?? null,
            'id_dekan' => $idDekan
        ]);        // === 5. BUAT TUGAS SURAT DAN SURAT MAGANG ===
        try {
            DB::beginTransaction();

            // Siapkan data mahasiswa JSON
            $dataMahasiswaArray = [];
            foreach ($request->mahasiswa as $index => $mhs) {
                // Get jurusan from mahasiswa's prodi
                $mahasiswaData = Mahasiswa::where('NIM', $mhs['nim'])->with('prodi.jurusan', 'user')->first();
                $namaJurusan = null;
                $noWa = null;

                if ($mahasiswaData && $mahasiswaData->prodi && $mahasiswaData->prodi->jurusan) {
                    $namaJurusan = $mahasiswaData->prodi->jurusan->Nama_Jurusan;
                }

                // Get No WhatsApp from Users table
                if ($mahasiswaData && $mahasiswaData->user) {
                    $noWa = $mahasiswaData->user->No_WA;
                }

                $dataMahasiswaArray[] = [
                    'nama' => $mhs['nama'],
                    'nim' => $mhs['nim'],
                    'program-studi' => $mhs['jurusan'],
                    'jurusan' => $namaJurusan,
                    'angkatan' => $mhs['angkatan'],
                    'no_wa' => $noWa,
                ];
            }

            // Status workflow magang (untuk Surat_Magang)
            $statusMagang = $adaTemanDiajak ? 'Draft' : 'Diajukan-ke-koordinator';

            // Buat Tugas_Surat (semua data spesifik ada di Surat_Magang)
            $tugasSurat = new TugasSurat();
            $tugasSurat->Id_Jenis_Surat = $jenisSuratId;

            // FIX: Mahasiswa adalah PEMBERI tugas (requestor), Kaprodi/Admin adalah PENERIMA
            $tugasSurat->Id_Pemberi_Tugas_Surat = $mahasiswaId;
            $tugasSurat->Id_Penerima_Tugas_Surat = $kaprodiUser ? $kaprodiUser->Id_User : null;

            $tugasSurat->Status = 'baru'; // Status Tugas_Surat selalu baru untuk pengajuan baru
            $tugasSurat->Tanggal_Diberikan_Tugas_Surat = Carbon::now()->format('Y-m-d');
            $tugasSurat->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(5)->format('Y-m-d');

            // Tambahkan Judul Tugas Surat agar muncul di tabel
            $tugasSurat->Judul_Tugas_Surat = "Pengajuan Surat Pengantar Magang ke " . $request->input('data_spesifik.nama_instansi');

            $tugasSurat->save();

            \Log::info('[MAGANG] TugasSurat created', [
                'tugas_surat_id' => $tugasSurat->Id_Tugas_Surat,
                'status_tugas_surat' => 'baru',
                'status_magang' => $statusMagang,
                'ada_teman' => $adaTemanDiajak
            ]);

            // === 6. BUAT SURAT MAGANG ===
            // Siapkan data dosen pembimbing JSON
            $dataDosenPembimbing = [
                'dosen_pembimbing_1' => $request->input('data_spesifik.dosen_pembimbing_1'),
                'dosen_pembimbing_2' => $request->input('data_spesifik.dosen_pembimbing_2'),
            ];

            $suratMagang = new SuratMagang();
            $suratMagang->Id_Tugas_Surat = $tugasSurat->Id_Tugas_Surat;
            $suratMagang->Data_Mahasiswa = json_encode($dataMahasiswaArray);
            $suratMagang->Data_Dosen_pembiming = json_encode($dataDosenPembimbing);
            $suratMagang->Judul_Penelitian = $request->input('data_spesifik.judul_penelitian');
            $suratMagang->Dokumen_Proposal = $filePendukungPath;
            $suratMagang->Nama_Instansi = $request->input('data_spesifik.nama_instansi');
            $suratMagang->Alamat_Instansi = $request->input('data_spesifik.alamat_instansi');
            $suratMagang->Tanggal_Mulai = $request->input('data_spesifik.tanggal_mulai');
            $suratMagang->Tanggal_Selesai = $request->input('data_spesifik.tanggal_selesai');
            $suratMagang->Foto_ttd = $fileTTDPath;
            $suratMagang->Status = $statusMagang;
            $suratMagang->Nama_Koordinator = $kaprodiId;
            $suratMagang->Nama_Dekan = $idDekan; // Simpan Id_Dosen yang menjabat sebagai Dekan
            $suratMagang->save();

            \Log::info('[MAGANG] Surat_Magang created', [
                'surat_magang_id' => $suratMagang->id_no
            ]);

            // === 7. BUAT INVITATIONS DAN KIRIM NOTIFIKASI ===
            if ($adaTemanDiajak) {
                // Ada teman yang diajak - buat invitation dan kirim notifikasi
                foreach ($mahasiswaList as $index => $mhs) {
                    if ($index == 0)
                        continue; // Skip pembuat (index 0)

                    // Cari mahasiswa by NIM
                    $mahasiswaDiundang = Mahasiswa::where('NIM', $mhs['nim'])->first();
                    if (!$mahasiswaDiundang) {
                        \Log::warning('[MAGANG] Mahasiswa tidak ditemukan', ['nim' => $mhs['nim']]);
                        continue;
                    }

                    // Buat invitation
                    $invitation = SuratMagangInvitation::create([
                        'id_surat_magang' => $suratMagang->id_no,
                        'id_mahasiswa_pengundang' => $mahasiswaPembuat->Id_Mahasiswa,
                        'id_mahasiswa_diundang' => $mahasiswaDiundang->Id_Mahasiswa,
                        'status' => 'pending',
                        'keterangan' => null,
                        'invited_at' => now()
                    ]);

                    // Tidak perlu kirim notifikasi - mahasiswa cek di menu Ajakan Magang
                    // Notifikasi::create([
                    //     'Tipe_Notifikasi' => 'Invitation',
                    //     'Pesan' => $mahasiswaPembuat->Nama_Mahasiswa . ' mengundang Anda untuk bergabung dalam pengajuan magang ke ' . $suratMagang->Nama_Instansi,
                    //     'Dest_User' => $mahasiswaDiundang->Id_User,
                    //     'Source_User' => Auth::user()->Id_User,
                    //     'Is_Read' => false,
                    //     'Data_Tambahan' => [
                    //         'invitation_id' => $invitation->id,
                    //         'surat_magang_id' => $suratMagang->id_no
                    //     ]
                    // ]);

                    \Log::info('[MAGANG] Invitation created', [
                        'invitation_id' => $invitation->id,
                        'to_mahasiswa' => $mahasiswaDiundang->Nama_Mahasiswa
                    ]);
                }
            } else {
                // Tidak ada teman - langsung kirim notifikasi ke koordinator
                \Log::info('[MAGANG] No invitations, submitting directly to coordinator');
                // TODO: Implement koordinator notification here
            }

            DB::commit();

            $message = $adaTemanDiajak
                ? 'Pengajuan berhasil dibuat! Menunggu teman-teman menerima undangan.'
                : 'Pengajuan Surat Pengantar Magang/KP berhasil dikirim dan diajukan ke koordinator.';

            return redirect('/mahasiswa/pengajuan-surat/magang')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membuat pengajuan surat magang: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Search mahasiswa untuk autocomplete
     */
    public function searchMahasiswa(Request $request)
    {
        $search = $request->get('q');
        $mahasiswaLogin = Auth::user()->mahasiswa;

        if (!$mahasiswaLogin) {
            return response()->json([]);
        }

        // Get Id_Jurusan dari prodi mahasiswa yang login
        $prodiLogin = $mahasiswaLogin->prodi;
        if (!$prodiLogin || !$prodiLogin->Id_Jurusan) {
            return response()->json([]);
        }

        // Cari mahasiswa dari prodi yang memiliki Id_Jurusan yang sama
        $results = Mahasiswa::whereHas('prodi', function ($query) use ($prodiLogin) {
            $query->where('Id_Jurusan', $prodiLogin->Id_Jurusan);
        })
            ->where('Id_Mahasiswa', '!=', $mahasiswaLogin->Id_Mahasiswa)
            ->where(function ($query) use ($search) {
                $query->where('Nama_Mahasiswa', 'like', '%' . $search . '%')
                    ->orWhere('NIM', 'like', '%' . $search . '%');
            })
            ->with('prodi')
            ->limit(10)
            ->get([
                'Id_Mahasiswa',
                'Nama_Mahasiswa',
                'NIM',
                'Angkatan',
                'Id_Prodi',
                'Status_KP'
            ]);

        return response()->json($results->map(function ($mhs) {
            return [
                'id' => $mhs->Id_Mahasiswa,
                'nama' => $mhs->Nama_Mahasiswa,
                'nim' => $mhs->NIM,
                'angkatan' => $mhs->Angkatan,
                'jurusan' => $mhs->prodi ? $mhs->prodi->Nama_Prodi : '-',
                'status_kp' => $mhs->Status_KP
            ];
        }));
    }

    /**
     * Accept invitation - update status, check if all accepted
     */
    public function acceptInvitation($id)
    {
        try {
            DB::beginTransaction();

            $mahasiswa = Auth::user()->mahasiswa;
            $invitation = SuratMagangInvitation::findOrFail($id);

            // Validasi
            if ($invitation->id_mahasiswa_diundang != $mahasiswa->Id_Mahasiswa) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses');
            }

            if ($invitation->status != 'pending') {
                return redirect()->back()->with('error', 'Undangan sudah diproses');
            }

            // Update status invitation
            $invitation->status = 'accepted';
            $invitation->responded_at = now();
            $invitation->save();

            \Log::info('[INVITATION] Accepted', [
                'invitation_id' => $invitation->id,
                'mahasiswa' => $mahasiswa->Nama_Mahasiswa
            ]);

            // Notify pembuat
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => $mahasiswa->Nama_Mahasiswa . ' menerima undangan magang Anda',
                'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
                'Source_User' => Auth::user()->Id_User,
                'Is_Read' => false
            ]);

            // Cek apakah semua invitation sudah accepted
            $suratMagang = $invitation->suratMagang;
            $allInvitations = SuratMagangInvitation::where('id_surat_magang', $suratMagang->Id_Surat_Magang)->get();
            $allAccepted = $allInvitations->every(function ($inv) {
                return $inv->status === 'accepted';
            });

            if ($allAccepted) {
                // Semua sudah accept - update status Surat_Magang
                $tugasSurat = $suratMagang->tugasSurat;
                $tugasSurat->Status = 'Diajukan-ke-koordinator';
                $tugasSurat->save();

                \Log::info('[INVITATION] All accepted, status updated to Diajukan-ke-koordinator', [
                    'surat_magang_id' => $suratMagang->Id_Surat_Magang,
                    'tugas_surat_id' => $tugasSurat->Id_Tugas_Surat
                ]);

                // Notify pembuat bahwa semua sudah accept
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'Semua teman telah menerima undangan magang. Pengajuan Anda telah dikirim ke koordinator.',
                    'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
                    'Source_User' => Auth::user()->Id_User,
                    'Is_Read' => false
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Undangan berhasil diterima');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject invitation - delete Surat_Magang and TugasSurat
     */
    public function rejectInvitation(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $mahasiswa = Auth::user()->mahasiswa;
            $invitation = SuratMagangInvitation::findOrFail($id);

            // Validasi
            if ($invitation->id_mahasiswa_diundang != $mahasiswa->Id_Mahasiswa) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses');
            }

            if ($invitation->status != 'pending') {
                return redirect()->back()->with('error', 'Undangan sudah diproses');
            }

            // Update status invitation
            $invitation->status = 'rejected';
            $invitation->keterangan = $request->keterangan;
            $invitation->responded_at = now();
            $invitation->save();

            \Log::info('[INVITATION] Rejected', [
                'invitation_id' => $invitation->id,
                'mahasiswa' => $mahasiswa->Nama_Mahasiswa,
                'reason' => $request->keterangan
            ]);

            // HAPUS Surat_Magang karena ada yang reject
            $suratMagang = $invitation->suratMagang;
            $tugasSurat = $suratMagang->tugasSurat;

            // Notify pembuat tentang rejection
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Rejected',
                'Pesan' => $mahasiswa->Nama_Mahasiswa . ' menolak undangan magang: ' . $request->keterangan . '. Pengajuan Anda telah dibatalkan.',
                'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
                'Source_User' => Auth::user()->Id_User,
                'Is_Read' => false
            ]);

            // Hapus semua invitation terkait
            SuratMagangInvitation::where('id_surat_magang', $suratMagang->Id_Surat_Magang)->delete();

            // Hapus Surat_Magang
            $suratMagang->delete();

            // Hapus Tugas_Surat
            $tugasSurat->delete();

            \Log::info('[INVITATION] Surat_Magang and TugasSurat deleted due to rejection', [
                'surat_magang_id' => $suratMagang->Id_Surat_Magang,
                'tugas_surat_id' => $tugasSurat->Id_Tugas_Surat
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Undangan ditolak dan pengajuan dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
