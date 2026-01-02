<?php

namespace App\Http\Controllers\Wadek1;

use App\Http\Controllers\Controller;
use App\Models\SuratLegalisir;
use Illuminate\Support\Facades\Auth;

class PersetujuanSuratController extends Controller
{
    /**
     * List legalisir yang menunggu TTD Wadek1
     */
    public function listLegalisir()
    {
        $user = Auth::user();
        
        // Ambil data legalisir langsung dari tabel Surat_Legalisir dengan status menunggu_ttd_pimpinan
        $daftarLegalisir = SuratLegalisir::with([
                'user.mahasiswa.prodi',
                'user.role',
                'tugasSurat.jenisSurat',
                'pejabat'
            ])
            ->where('Status', 'menunggu_ttd_pimpinan')
            ->orderBy('id_no', 'desc')
            ->get();

        return view('wadek1.persetujuan_legalisir', compact('daftarLegalisir'));
    }
}
