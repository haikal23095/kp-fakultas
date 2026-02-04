<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SKDosenWali;
use App\Models\SuratMagang;
use App\Models\SuratKetAktif;
use App\Models\SuratLegalisir;
use App\Models\SuratPeminjamanMobil;
use App\Models\SuratDispensasi;
use App\Models\SuratKelakuanBaik;
use App\Models\SuratTidakBeasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenSuratController extends Controller
{
    /**
     * Halaman utama - Card pilihan jenis surat
     */
    public function index()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Count total dari semua jenis surat
        $totalSemua = 0;

        // Hitung untuk Surat Keterangan Aktif
        $countAktif = SuratKetAktif::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereNotIn('Status', ['selesai', 'Selesai', 'SELESAI', 'ditolak', 'Ditolak'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();

        $pendingAktif = SuratKetAktif::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereIn('Status', ['baru', 'pending', 'Diajukan'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();

        $prosesAktif = SuratKetAktif::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereIn('Status', ['proses', 'Dikerjakan-admin'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();

        $selesaiAktif = 0;
        $totalSemua += $countAktif;

        // Hitung untuk Surat Magang
        $countMagang = SuratMagang::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereNotIn('Status', ['Success', 'Ditolak-Kaprodi', 'Ditolak-Dekan'])
            ->count();

        $pendingMagang = SuratMagang::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->where('Status', 'Diajukan-ke-koordinator')
            ->count();

        $prosesMagang = SuratMagang::whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereIn('Status', ['Dikerjakan-admin', 'Diajukan-ke-dekan'])
            ->count();

        $selesaiMagang = 0;
        $totalSemua += $countMagang;

        // Hitung untuk Legalisir
        $countLegalisir = SuratLegalisir::whereNotIn('Status', ['selesai', 'ditolak'])
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->count();
        $pendingLegalisir = SuratLegalisir::whereIn('Status', ['menunggu_pembayaran'])
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->count();
        $prosesLegalisir = SuratLegalisir::whereIn('Status', ['pembayaran_lunas', 'menunggu_ttd_pimpinan', 'siap_diambil'])
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->count();
        $selesaiLegalisir = 0;
        $totalSemua += $countLegalisir;

        // Hitung untuk SK Dosen Wali - dari tabel Req_SK_Dosen_Wali
        $countSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->count();

        $pendingSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->whereIn('Status', ['Pending', 'Ditolak-Admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])->count();

        $prosesSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->whereIn('Status', ['Dikerjakan admin', 'Menunggu-Persetujuan-Wadek-1', 'Menunggu-Persetujuan-Dekan'])->count();

        $selesaiSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->where('Status', 'Selesai')->count();

        $totalSemua += $countSKDosen;

        // Hitung untuk Peminjaman Mobil Dinas dengan filter fakultas
        $countMobilDinas = SuratPeminjamanMobil::where('status_pengajuan', 'Diajukan')
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->count();
        $totalSemua += $countMobilDinas;

        // Hitung untuk Surat Dispensasi
        $countDispensasi = SuratDispensasi::whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereNotIn('Status', ['selesai', 'Selesai', 'SELESAI', 'ditolak', 'Ditolak'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();
        $totalSemua += $countDispensasi;

        // Hitung untuk Surat Berkelakuan Baik
        $countBerkelakuanBaik = SuratKelakuanBaik::whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereNotIn('Status', ['selesai', 'Selesai', 'SELESAI', 'ditolak', 'Ditolak'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();
        $totalSemua += $countBerkelakuanBaik;

        // Hitung untuk Surat Tidak Menerima Beasiswa
        $countTidakBeasiswa = SuratTidakBeasiswa::whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })
            ->whereNotIn('Status', ['selesai', 'Selesai', 'SELESAI', 'ditolak', 'Ditolak'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->count();
        $totalSemua += $countTidakBeasiswa;

        // TODO: Counter untuk jenis surat baru (setelah database dibuat)
        $countCuti = 0;
        $countSKFakultas = 0;
        $countPeminjamanGedung = 0;
        $countLembur = 0;

        return view('admin_fakultas.manajemen_surat_index', compact(
            'countAktif',
            'pendingAktif',
            'prosesAktif',
            'selesaiAktif',
            'countMagang',
            'pendingMagang',
            'prosesMagang',
            'selesaiMagang',
            'countLegalisir',
            'pendingLegalisir',
            'prosesLegalisir',
            'selesaiLegalisir',
            'countSKDosen',
            'pendingSKDosen',
            'prosesSKDosen',
            'selesaiSKDosen',
            'countMobilDinas',
            'countCuti',
            'countTidakBeasiswa',
            'countDispensasi',
            'countBerkelakuanBaik',
            'countSKFakultas',
            'countPeminjamanGedung',
            'countLembur',
            'totalSemua'
        ));
    }

    /**
     * List Surat Keterangan Aktif
     */
    public function listAktif()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $daftarTugas = SuratKetAktif::query()
            ->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->whereNotIn('Status', ['selesai', 'Selesai', 'SELESAI', 'ditolak', 'Ditolak'])
            ->where(function ($q) {
                $q->whereNull('Nomor_Surat')->orWhere('Nomor_Surat', '');
            })
            ->with([
                'pemberiTugas.role',
                'pemberiTugas.mahasiswa.prodi'
            ])
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->paginate(15);

        return view('admin_fakultas.list_aktif', compact('daftarTugas'));
    }

    /**
     * List Surat Magang
     */
    public function listMagang()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $daftarTugas = SuratMagang::query()
            ->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->whereNotIn('Status', ['Success', 'Ditolak-Kaprodi', 'Ditolak-Dekan'])
            ->with([
                'pemberiTugas.role',
                'pemberiTugas.mahasiswa.prodi',
                'koordinator'
            ])
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->paginate(15);

        return view('admin_fakultas.list_magang', compact('daftarTugas'));
    }

    /**
     * Show detail Surat Magang
     */
    public function showMagang($id)
    {
        $surat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi.fakultas',
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

        $surat = SuratMagang::findOrFail($id);

        // Update Nomor_Surat dan Status di Surat_Magang
        $surat->Nomor_Surat = $request->nomor_surat;
        $surat->Status = 'Diajukan-ke-dekan';
        $surat->save();

        return redirect()->route('admin_fakultas.surat.magang')
            ->with('success', 'Nomor surat berhasil diberikan dan diteruskan ke Dekan.');
    }

    /**
     * Download dokumen proposal magang
     */
    public function downloadProposalMagang($id)
    {
        $surat = SuratMagang::findOrFail($id);

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
        $surat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi',
            'koordinator',
            'dekan'
        ])
            ->where('Id_Surat_Magang', $id)
            ->firstOrFail();

        // Cek apakah sudah disetujui (koordinator atau dekan)
        if (!$surat->Acc_Koordinator && !$surat->Acc_Dekan) {
            return back()->with('error', 'Surat Pengantar belum disetujui.');
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_pengantar', [
            'surat' => $surat,
            'magang' => $surat,
            'mahasiswa' => $surat->pemberiTugas->mahasiswa,
            'koordinator' => $surat->koordinator,
            'mode' => 'preview'
        ]);
    }

    // TODO: Method placeholder untuk jenis surat baru
    public function listMobilDinas()
    {
        $daftarTugas = collect(); // Empty collection untuk sementara
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listCuti()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listTidakBeasiswa()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listDispensasi()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listBerkelakuanBaik()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listSKFakultas()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listPeminjamanGedung()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listLembur()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function archive()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Query each surat type individually with fakultas filter, only ones with nomor surat (archived)
        $arsipAktif = SuratKetAktif::with(['pemberiTugas.mahasiswa.prodi'])
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->get();

        $arsipMagang = SuratMagang::with(['pemberiTugas.mahasiswa.prodi'])
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->get();

        $arsipLegalisir = SuratLegalisir::with(['user.mahasiswa.prodi'])
            ->where('Status', 'selesai')
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('id_no', 'desc')
            ->get();

        $arsipSKDosen = SKDosenWali::with(['dosen.prodi'])
            ->whereNotNull('Nomor_SK')
            ->where('Nomor_SK', '!=', '')
            ->whereHas('dosen.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $arsipMobilDinas = SuratPeminjamanMobil::with(['user'])
            ->where('status_pengajuan', 'Selesai')
            ->whereNotNull('no_surat')
            ->where('no_surat', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        $arsipBerkelakuanBaik = SuratKelakuanBaik::with(['user.mahasiswa.prodi'])
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $arsipDispensasi = SuratDispensasi::with(['user.mahasiswa.prodi'])
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $arsipTidakBeasiswa = SuratTidakBeasiswa::with(['user.mahasiswa.prodi'])
            ->whereNotNull('Nomor_Surat')
            ->where('Nomor_Surat', '!=', '')
            ->whereHas('user.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                $q->where('Id_Fakultas', $fakultasId);
            })
            ->orderBy('id', 'desc')
            ->get();

        // Count archives per type
        $countArsipAktif = $arsipAktif->count();
        $countArsipMagang = $arsipMagang->count();
        $countArsipLegalisir = $arsipLegalisir->count();
        $countArsipSKDosen = $arsipSKDosen->count();
        $countArsipBerkelakuanBaik = $arsipBerkelakuanBaik->count();
        $countArsipDispensasi = $arsipDispensasi->count();
        $countArsipTidakBeasiswa = $arsipTidakBeasiswa->count();
        $countArsipMobilDinas = $arsipMobilDinas->count();

        // Group by jenis for the view
        $arsipByJenis = collect([
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 1, 'Nama_Surat' => 'Surat Keterangan Aktif'],
                'items' => $arsipAktif
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 2, 'Nama_Surat' => 'Surat Pengantar Magang'],
                'items' => $arsipMagang
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 3, 'Nama_Surat' => 'Surat Legalisir'],
                'items' => $arsipLegalisir
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 12, 'Nama_Surat' => 'SK Dosen Wali'],
                'items' => $arsipSKDosen
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 8, 'Nama_Surat' => 'Surat Kelakuan Baik'],
                'items' => $arsipBerkelakuanBaik
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 7, 'Nama_Surat' => 'Surat Dispensasi'],
                'items' => $arsipDispensasi
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 6, 'Nama_Surat' => 'Surat Tidak Beasiswa'],
                'items' => $arsipTidakBeasiswa
            ],
            (object) [
                'jenis' => (object) ['Id_Jenis_Surat' => 13, 'Nama_Surat' => 'Peminjaman Mobil Dinas'],
                'items' => $arsipMobilDinas
            ]
        ]);

        // For backward compatibility with view
        $arsipTugas = collect();

        return view('admin_fakultas.arsip_surat', compact(
            'arsipTugas',
            'arsipByJenis',
            'countArsipAktif',
            'countArsipMagang',
            'countArsipLegalisir',
            'countArsipSKDosen',
            'countArsipBerkelakuanBaik',
            'countArsipDispensasi',
            'countArsipTidakBeasiswa',
            'countArsipMobilDinas'
        ));
    }

    public function archiveDetail($id)
    {
        // Map ID to jenis surat name and model
        $jenisSuratMap = [
            1 => ['name' => 'Surat Keterangan Aktif', 'model' => SuratKetAktif::class, 'relation' => 'pemberiTugas'],
            2 => ['name' => 'Surat Pengantar Magang', 'model' => SuratMagang::class, 'relation' => 'pemberiTugas'],
            3 => ['name' => 'Surat Legalisir', 'model' => SuratLegalisir::class, 'relation' => 'user'],
            6 => ['name' => 'Surat Tidak Beasiswa', 'model' => SuratTidakBeasiswa::class, 'relation' => 'user'],
            7 => ['name' => 'Surat Dispensasi', 'model' => SuratDispensasi::class, 'relation' => 'user'],
            8 => ['name' => 'Surat Kelakuan Baik', 'model' => SuratKelakuanBaik::class, 'relation' => 'user'],
            12 => ['name' => 'SK Dosen Wali', 'model' => SKDosenWali::class, 'relation' => 'dosen'],
            13 => ['name' => 'Peminjaman Mobil Dinas', 'model' => SuratPeminjamanMobil::class, 'relation' => 'user']
        ];

        if (!isset($jenisSuratMap[$id])) {
            abort(404, 'Jenis surat tidak ditemukan');
        }

        $jenisSurat = (object) [
            'Id_Jenis_Surat' => $id,
            'Nama_Surat' => $jenisSuratMap[$id]['name']
        ];

        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $modelClass = $jenisSuratMap[$id]['model'];
        $relation = $jenisSuratMap[$id]['relation'];

        // Query based on model type
        if ($id == 3) {
            // Legalisir - uses Status = 'selesai'
            $arsipTugas = $modelClass::with([$relation . '.mahasiswa.prodi', $relation . '.role'])
                ->where('Status', 'selesai')
                ->whereHas($relation . '.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->orderBy('id_no', 'desc')
                ->get();
            $arsipLegalisir = $arsipTugas;
            $arsipTugas = collect();
        } elseif ($id == 13) {
            // Peminjaman Mobil Dinas - different status field
            $arsipTugas = $modelClass::with([$relation, 'kendaraan', 'pejabat'])
                ->where('status_pengajuan', 'Selesai')
                ->whereNotNull('no_surat')
                ->where('no_surat', '!=', '')
                ->orderBy('updated_at', 'desc')
                ->get();
            $arsipLegalisir = collect();
        } elseif ($id == 12) {
            // SK Dosen Wali - different structure
            $arsipTugas = $modelClass::with([$relation . '.prodi'])
                ->whereNotNull('Nomor_SK')
                ->where('Nomor_SK', '!=', '')
                ->whereHas($relation . '.prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->orderBy('id', 'desc')
                ->get();
            $arsipLegalisir = collect();
        } else {
            // Other surat types - use Nomor_Surat field
            $arsipTugas = $modelClass::with([$relation . '.mahasiswa.prodi', $relation . '.role'])
                ->whereNotNull('Nomor_Surat')
                ->where('Nomor_Surat', '!=', '')
                ->whereHas($relation . '.mahasiswa.prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->orderBy($id == 1 ? 'Tanggal_Diberikan' : ($id == 2 ? 'Tanggal_Diberikan' : 'id'), 'desc')
                ->get();
            $arsipLegalisir = collect();
        }

        return view('admin_fakultas.arsip_detail', compact('jenisSurat', 'arsipTugas', 'arsipLegalisir'));
    }

    /**
     * Tampilkan halaman pengaturan
     */
    public function settings()
    {
        return view('admin_fakultas.pengaturan');
    }
}
