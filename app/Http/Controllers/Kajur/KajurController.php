<?php

namespace App\Http\Controllers\Kajur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KajurController extends Controller
{
    /**
     * Tampilkan halaman verifikasi RPS
     */
    public function verifikasiRps()
    {
        return view('kajur.verifikasi_rps');
    }

    /**
     * Tampilkan halaman laporan jurusan
     */
    public function laporan()
    {
        return view('kajur.laporan_jurusan');
    }

    /**
     * Tampilkan halaman persetujuan surat
     */
    public function persetujuanSurat()
    {
        return view('kajur.persetujuan-surat');
    }
}
