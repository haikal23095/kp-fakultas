<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    /**
     * Tampilkan riwayat dosen
     */
    public function riwayat()
    {
        return view('dosen.riwayat');
    }

    /**
     * Tampilkan halaman input nilai
     */
    public function inputNilai()
    {
        return view('dosen.input_nilai');
    }

    /**
     * Tampilkan halaman bimbingan akademik
     */
    public function bimbingan()
    {
        return view('dosen.bimbingan_akademik');
    }
}
