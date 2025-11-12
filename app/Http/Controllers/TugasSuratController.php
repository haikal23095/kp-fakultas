<?php

namespace App\Http\Controllers;

use App\Models\TugasSurat; // Pastikan Model-nya di-import
use Illuminate\Http\Request;

class TugasSuratController extends Controller
{
    public function index()
    {
        // 1. Ambil data dari database
        // Kita gunakan 'with()' untuk Eager Loading agar lebih efisien
        // 'pemberiTugas' dan 'jenisSurat' adalah NAMA FUNGSI RELASI di Model
        $daftarTugas = TugasSurat::with(['pemberiTugas', 'jenisSurat'])
                                ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
                                ->get();

        // 2. Kirim data ke view 'manajemen-surat.index'
        // (Sesuaikan nama view Anda)
        return view('manajemen-surat.index', [
            'daftarTugas' => $daftarTugas
        ]);
    }
}