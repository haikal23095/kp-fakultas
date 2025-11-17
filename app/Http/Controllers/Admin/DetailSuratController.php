<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Menggunakan Request standar
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use App\Models\FileArsip;
use App\Models\User;
use App\Models\Dosen; // Diperlukan untuk generateSuratAktif
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DetailSuratController extends Controller
{
    /**
     * Tampilkan detail surat berdasarkan ID.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Eager load relations yang dibutuhkan.
        $with = ['pemberiTugas.role', 'jenisSurat', 'penerimaTugas'];

        if (method_exists(TugasSurat::class, 'fileArsip')) {
            $with[] = 'fileArsip';
        }

        $tugasSurat = TugasSurat::with($with)->find($id);

        // Jika data tidak ditemukan, return 404
        if (!$tugasSurat) {
            abort(404);
        }

        // Coba ambil detail pengaju jika pemberiTugas adalah Mahasiswa
        $detailPengaju = null;
        $pemberi = $tugasSurat->pemberiTugas;
        $roleName = optional($pemberi->role)->Name_Role;

        if ($pemberi && is_string($roleName) && strtolower(trim($roleName)) === 'mahasiswa') {
            // Prefer mengambil via relasi 'mahasiswa' di model User jika ada
            if (method_exists($pemberi, 'mahasiswa')) {
                // Eager load prodi juga untuk data lengkap
                $pemberi->load('mahasiswa.prodi');
                $detailPengaju = $pemberi->mahasiswa;
            } else {
                $detailPengaju = null;
            }
        }

        // <-- Tambahkan pengiriman variabel tanggalHariIni dan data surat/detailPengaju ke view -->
        $tanggalHariIni = Carbon::now()->format('d F Y');
        return view('admin.detail_surat', [
            'activeMenu'    => 'manajemen-surat',
            'surat'         => $tugasSurat,
            'detailPengaju' => $detailPengaju,
            'tanggalHariIni'=> $tanggalHariIni,
        ]);
    }

    /**
     * Download dokumen pendukung yang diupload oleh mahasiswa.
     *
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPendukung($id)
    {
        $tugasSurat = TugasSurat::findOrFail($id);

        // Path disimpan di kolom 'dokumen_pendukung'
        $path = $tugasSurat->dokumen_pendukung;

        if (!$path) {
            return response('Dokumen Pendukung tidak ditemukan.', 404);
        }

        // Gunakan disk 'public' (storage/app/public)
        if (Storage::disk('public')->exists($path)) {
            // Menggunakan Storage::response() akan memungkinkan browser untuk mencoba preview (inline)
            return Storage::disk('public')->response($path);
        }

        return response('Dokumen Pendukung tidak ditemukan.', 404);
    }

    /**
     * Proses upload draft final atau langsung mengajukan ke Dekan.
     * - Jika ada file 'draft_surat' akan disimpan ke tabel File_Arsip.
     * - Jika action == 'proses_ajukan_dekan' tanpa file, hanya ubah status.
     */
    public function processDraft(Request $request, $id) // Menggunakan Request standar
    {
        $user = Auth::user();
        if (!$user || $user->Id_Role != 1) {
            abort(403);
        }

        $tugas = TugasSurat::findOrFail($id);

        // Jika hanya tombol header diklik untuk langsung proses tanpa upload
        if ($request->input('action') === 'proses_ajukan_dekan') {
            // Cari user Dekan
            $dekan = User::whereHas('role', function ($q) {
                $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
            })->first();

            if ($dekan) {
                $tugas->Id_Penerima_Tugas_Surat = $dekan->Id_User;
            }

            $tugas->Status = 'Diajukan ke Dekan';
            $tugas->save();

            return redirect()->route('admin.surat.detail', $tugas->Id_Tugas_Surat)
                ->with('success', 'Tugas telah diajukan ke Dekan.');
        }

        // Validasi upload draft final (wajib)
        $validated = $request->validate([
            'draft_surat' => 'required|file|mimes:pdf|max:5048',
        ]);

        $file = $request->file('draft_surat');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        // Simpan file di storage/app/public/surat_drafts/{Id_Tugas_Surat}/
        $path = $file->store("surat_drafts/{$tugas->Id_Tugas_Surat}", 'public');

        // Perbarui/masukkan pada tabel File_Arsip
        $fileArsip = FileArsip::where('Id_Tugas_Surat', $tugas->Id_Tugas_Surat)->first();
        if (!$fileArsip) {
            $fileArsip = new FileArsip();
            $fileArsip->Id_Tugas_Surat = $tugas->Id_Tugas_Surat;
        }
        $fileArsip->Path_File = $path;
        $fileArsip->Keterangan = 'Draft Final';
        $fileArsip->Id_Pemberi_Tugas_Surat = $user->Id_User;

        // Cari user Dekan dan set sebagai penerima tugas jika ada
        $dekan = User::whereHas('role', function ($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
        })->first();

        if ($dekan) {
            $fileArsip->Id_Penerima_Tugas_Surat = $dekan->Id_User;
            $tugas->Id_Penerima_Tugas_Surat = $dekan->Id_User;
        } else {
            // fallback: tetap gunakan penerima yang ada
            $fileArsip->Id_Penerima_Tugas_Surat = $tugas->Id_Penerima_Tugas_Surat ?? $user->Id_User;
        }

        $fileArsip->save();

        // Juga perbarui kolom dokumen_pendukung di Tugas_Surat jika Anda ingin menyimpan path di situ
        $tugas->dokumen_pendukung = $path;
        $tugas->Status = 'Diajukan ke Dekan';
        $tugas->save();

        return redirect()->route('admin.surat.detail', $tugas->Id_Tugas_Surat)
            ->with('success', 'Draft final berhasil diupload dan diajukan ke Dekan.');
    }

    // ==================================================================
    // METODE UNTUK GENERATE PREVIEW SURAT KETERANGAN AKTIF
    // ==================================================================
    public function generateSuratAktif(\Illuminate\Http\Request $request, $id)
    {
        // Ambil surat + relasi
        $tugasSurat = TugasSurat::with(['pemberiTugas.role', 'jenisSurat', 'penerimaTugas'])->findOrFail($id);

        // Ambil detail pengaju jika pemberi tugas adalah mahasiswa
        $detailPengaju = null;
        $pemberi = $tugasSurat->pemberiTugas;
        $roleName = optional($pemberi->role)->Name_Role;
        if ($pemberi && is_string($roleName) && strtolower(trim($roleName)) === 'mahasiswa') {
            // asumsikan relasi Mahasiswa dapat dicari berdasarkan Id_User
            $detailPengaju = Mahasiswa::with('prodi')->where('Id_User', $pemberi->Id_User)->first();
        }

        // Ambil dekan (sesuaikan kondisi pencarian jika berbeda)
        $dekan = Dosen::where('Id_Pejabat', 1)->first();

        // pastikan data_spesifik didekode jika tersimpan sebagai JSON/text
        $dataSpesifik = [];
        if (!empty($tugasSurat->data_spesifik)) {
            if (is_string($tugasSurat->data_spesifik)) {
                $decoded = json_decode($tugasSurat->data_spesifik, true);
                $dataSpesifik = is_array($decoded) ? $decoded : [];
            } elseif (is_array($tugasSurat->data_spesifik)) {
                $dataSpesifik = $tugasSurat->data_spesifik;
            }
        }

        // Ambil semester & tahun akademik (prioritas: request -> data_spesifik -> default)
        $now = Carbon::now();
        $defaultStart = ($now->month >= 7) ? $now->year : $now->year - 1;
        $defaultAcademic = $defaultStart . '/' . ($defaultStart + 1);

        $semester = $request->input('semester', $dataSpesifik['semester'] ?? '5'); // default semester 5
        $tahunAkademik = $request->input('tahun_akademik', $dataSpesifik['tahun_akademik'] ?? $defaultAcademic);

        // Kirim objek Carbon agar blade bisa memanggil ->format / ->isoFormat
        $tanggalHariIni = Carbon::now();

        return view('admin.generate_surat_aktif', [
            'activeMenu'     => 'manajemen-surat',
            'surat'          => $tugasSurat,
            'detailPengaju'  => $detailPengaju,
            'dekan'          => $dekan,
            'semester'       => $semester,
            'tahun_akademik' => $tahunAkademik,
            'tanggalHariIni' => $tanggalHariIni,
        ]);
    }

    // ==================================================================
    // METODE UNTUK FINALISASI SURAT KETERANGAN AKTIF
    // ==================================================================
    public function finalizeSuratAktif(Request $request, $id) // Menggunakan Request standar
    {
        $tugas = TugasSurat::findOrFail($id);

        $request->validate([
            'nomor_surat'     => 'nullable|string|max:255',
            'semester'        => 'nullable|string|max:20',
            'tahun_akademik'  => 'nullable|string|max:20',
        ]);

        // decode existing data_spesifik (jika tersimpan sebagai JSON/string)
        $dataSpesifik = [];
        if (!empty($tugas->data_spesifik)) {
            if (is_string($tugas->data_spesifik)) {
                $decoded = json_decode($tugas->data_spesifik, true);
                $dataSpesifik = is_array($decoded) ? $decoded : [];
            } elseif (is_array($tugas->data_spesifik)) {
                $dataSpesifik = $tugas->data_spesifik;
            }
        }

        // simpan nomor, semester, tahun ke data_spesifik (jangan buat column baru)
        if ($request->filled('nomor_surat')) {
            $dataSpesifik['nomor_surat'] = $request->input('nomor_surat');
        }
        if ($request->filled('semester')) {
            $dataSpesifik['semester'] = $request->input('semester');
        }
        if ($request->filled('tahun_akademik')) {
            $dataSpesifik['tahun_akademik'] = $request->input('tahun_akademik');
        }

        // simpan metadata penyelesaian ke data_spesifik (karena kolom tanggal tidak ada di DB)
        $dataSpesifik['tanggal_diselesaikan'] = Carbon::now()->toDateTimeString();
        if (auth()->check()) {
            $dataSpesifik['diselesaikan_oleh_id'] = auth()->id();
            $dataSpesifik['diselesaikan_oleh_name'] = auth()->user()->Name_User ?? null;
        }

        // assign kembali ke model: jika model memiliki cast untuk data_spesifik gunakan array, jika tidak gunakan json string
        if (array_key_exists('data_spesifik', $tugas->getCasts())) {
            $tugas->data_spesifik = $dataSpesifik;
        } else {
            $tugas->data_spesifik = json_encode($dataSpesifik);
        }

        // update status dan tanggal selesai (pastikan nilai status sesuai ENUM di DB)
        $tugas->Status = 'Selesai';
        $tugas->Tanggal_Diseselesaikan = Carbon::now();

        // simpan
        $tugas->save();

        return redirect()->route('admin.surat.manage')
            ->with('success', 'Surat Keterangan Aktif #' . $tugas->Id_Tugas_Surat . ' telah diselesaikan dan diarsip.');
    }
}