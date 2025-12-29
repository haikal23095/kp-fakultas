<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenSuratController extends Controller
{
    /**
     * Halaman utama - Card pilihan jenis surat
     */
    public function index()
    {
        // Update status tugas yang terlambat
        TugasSurat::updateStatusTerlambat();
        
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Base query dengan filter fakultas
        $baseQuery = function () use ($fakultasId) {
            return TugasSurat::query()
                ->where(function ($q) use ($fakultasId) {
                    $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    });
                });
        };
        
        // Total semua surat
        $totalSemua = $baseQuery()->count();
        
        // Total semua surat
        $totalSemua = $baseQuery()->count();

        // Hitung untuk Surat Keterangan Aktif - GUNAKAN RELASI
        $countAktif = $baseQuery()->has('suratKetAktif')->count();
        $pendingAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['baru', 'pending', 'Diajukan-ke-koordinator'])
            ->count();
        $prosesAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['proses', 'Dikerjakan-admin'])
            ->count();
        $selesaiAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['selesai', 'Success', 'Disetujui'])
            ->count();

        // Hitung untuk Surat Magang - GUNAKAN RELASI (hanya yang belum ada nomor surat)
        $countMagang = $baseQuery()->has('suratMagang')->where(function($q) {
            $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
        })->count();
        $pendingMagang = $baseQuery()
            ->has('suratMagang')
            ->where(function($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->whereIn('Status', ['baru', 'pending', 'Diajukan-ke-koordinator'])
            ->count();
        $prosesMagang = $baseQuery()
            ->has('suratMagang')
            ->where(function($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->whereIn('Status', ['proses', 'Dikerjakan-admin'])
            ->count();
        $selesaiMagang = 0; // Selesai pindah ke arsip (yang sudah ada nomor)

        // Hitung untuk Legalisir - GUNAKAN RELASI
        $countLegalisir = $baseQuery()->has('suratLegalisir')->count();
        $pendingLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['baru', 'pending'])
            ->count();
        $prosesLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['proses', 'Dikerjakan-admin', 'menunggu_pembayaran', 'pembayaran_lunas', 'proses_stempel_paraf'])
            ->count();
        $selesaiLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['selesai', 'Success', 'siap_diambil'])
            ->count();

        // TODO: Counter untuk jenis surat baru (setelah database dibuat)
        $countMobilDinas = 0;
        $countCuti = 0;
        $countTidakBeasiswa = 0;
        $countDispensasi = 0;
        $countBerkelakuanBaik = 0;
        $countSKFakultas = 0;
        $countPeminjamanGedung = 0;
        $countLembur = 0;

        return view('admin_fakultas.manajemen_surat_index', compact(
            'countAktif', 'pendingAktif', 'prosesAktif', 'selesaiAktif',
            'countMagang', 'pendingMagang', 'prosesMagang', 'selesaiMagang',
            'countLegalisir', 'pendingLegalisir', 'prosesLegalisir', 'selesaiLegalisir',
            'countMobilDinas', 'countCuti', 'countTidakBeasiswa', 'countDispensasi',
            'countBerkelakuanBaik', 'countSKFakultas', 'countPeminjamanGedung', 'countLembur',
            'totalSemua'
        ));
    }

    /**
     * List Surat Keterangan Aktif
     */
    public function listAktif()
    {
        TugasSurat::updateStatusTerlambat();

        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $baseQuery = TugasSurat::query()
            ->has('suratKetAktif') // GUNAKAN RELASI BUKAN Id_Jenis_Surat
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                });
            });

        $daftarTugas = $baseQuery->with([
            'pemberiTugas.role',
            'pemberiTugas.mahasiswa.prodi',
            'jenisSurat',
            'suratKetAktif'
        ])
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
        ->paginate(15);

        return view('admin_fakultas.list_aktif', compact('daftarTugas'));
    }

    /**
     * List Surat Magang
     */
    public function listMagang()
    {
        TugasSurat::updateStatusTerlambat();

        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $baseQuery = TugasSurat::query()
            ->has('suratMagang') // GUNAKAN RELASI BUKAN Id_Jenis_Surat
            ->where(function($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', ''); // Hanya yang belum ada nomor surat
            })
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                });
            });

        $daftarTugas = $baseQuery->with([
            'pemberiTugas.role',
            'pemberiTugas.mahasiswa.prodi',
            'jenisSurat',
            'suratMagang'
        ])
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
        ->paginate(15);

        return view('admin_fakultas.list_magang', compact('daftarTugas'));
    }

    /**
     * Show detail Surat Magang
     */
    public function showMagang($id)
    {
        $surat = \App\Models\SuratMagang::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi.fakultas',
            'tugasSurat.jenisSurat',
            'koordinator'
        ])->findOrFail($id);

        // Decode JSON data
        $dataMahasiswa = is_array($surat->Data_Mahasiswa)
            ? $surat->Data_Mahasiswa
            : json_decode($surat->Data_Mahasiswa, true);

        $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming)
            ? $surat->Data_Dosen_pembiming
            : json_decode($surat->Data_Dosen_pembiming, true);

        return view('admin_fakultas.surat_magang.detail', compact('surat', 'dataMahasiswa', 'dataDosenPembimbing'));
    }

    /**
     * Assign nomor surat magang dan teruskan ke Dekan
     */
    public function assignNomorMagang(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100'
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi',
            'nomor_surat.max' => 'Nomor surat maksimal 100 karakter'
        ]);

        $surat = \App\Models\SuratMagang::findOrFail($id);
        
        // Update status di tabel Surat_Magang - pakai 'Diajukan-ke-dekan' (enum Surat_Magang)
        $surat->Status = 'Diajukan-ke-dekan';
        $surat->save();

        // Update Nomor_Surat dan Status di TugasSurat - pakai 'Diajukan ke Dekan' (enum Tugas_Surat)
        if ($surat->tugasSurat) {
            $surat->tugasSurat->Nomor_Surat = $request->nomor_surat;
            $surat->tugasSurat->Status = 'Diajukan ke Dekan'; // ENUM: dengan spasi, bukan dash
            $surat->tugasSurat->save();
        }

        return redirect()->route('admin_fakultas.surat.magang')
            ->with('success', 'Nomor surat berhasil diberikan dan diteruskan ke Dekan.');
    }

    /**
     * Download dokumen proposal magang
     */
    public function downloadProposalMagang($id)
    {
        $surat = \App\Models\SuratMagang::findOrFail($id);

        if (!$surat->Dokumen_Proposal) {
            return back()->with('error', 'Dokumen proposal tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $surat->Dokumen_Proposal);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Download Surat Pengantar Magang (untuk admin fakultas view arsip)
     */
    public function downloadSuratPengantarMagang($id)
    {
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi',
            'suratMagang.koordinator',
            'suratMagang.dekan'
        ])
            ->where('Id_Tugas_Surat', $id)
            ->firstOrFail();

        // Cek apakah surat magang ada
        if (!$tugasSurat->suratMagang) {
            return back()->with('error', 'Bukan surat magang.');
        }

        // Cek apakah sudah disetujui (koordinator atau dekan)
        if (!$tugasSurat->suratMagang->Acc_Koordinator && !$tugasSurat->suratMagang->Acc_Dekan) {
            return back()->with('error', 'Surat Pengantar belum disetujui.');
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_pengantar', [
            'surat' => $tugasSurat,
            'magang' => $tugasSurat->suratMagang,
            'mahasiswa' => $tugasSurat->pemberiTugas->mahasiswa,
            'koordinator' => $tugasSurat->suratMagang->koordinator,
            'mode' => 'preview'
        ]);
    }

    // TODO: Method placeholder untuk jenis surat baru
    public function listMobilDinas() {
        $daftarTugas = collect(); // Empty collection untuk sementara
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listCuti() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listTidakBeasiswa() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listDispensasi() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listBerkelakuanBaik() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listSKFakultas() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listPeminjamanGedung() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listLembur() {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function archive()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Ambil semua surat yang SUDAH ADA NOMOR SURAT (sudah diproses admin), tidak peduli statusnya
        $arsipTugas = TugasSurat::query()
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                    ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    });
            })
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa.prodi', 'jenisSurat', 'suratKetAktif', 'suratMagang', 'suratLegalisir'])
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->get();

        // Ambil semua jenis surat agar card tetap muncul meskipun kosong
        $allJenisSurat = \App\Models\JenisSurat::all();
        
        // Grouping manual agar semua jenis surat masuk list
        $arsipByJenis = $allJenisSurat->map(function($jenis) use ($arsipTugas) {
            return (object) [
                'jenis' => $jenis,
                'items' => $arsipTugas->where('Id_Jenis_Surat', $jenis->Id_Jenis_Surat)
            ];
        });

        return view('admin_fakultas.arsip_surat', compact('arsipTugas', 'arsipByJenis'));
    }

    public function archiveDetail($id)
    {
        $jenisSurat = \App\Models\JenisSurat::findOrFail($id);
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $arsipTugas = TugasSurat::query()
            ->where('Id_Jenis_Surat', $id)
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                });
            })
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa.prodi', 'jenisSurat', 'suratMagang'])
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->get();

        return view('admin_fakultas.arsip_detail', compact('jenisSurat', 'arsipTugas'));
    }
}
