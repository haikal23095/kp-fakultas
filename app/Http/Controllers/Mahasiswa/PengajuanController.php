<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    // Menampilkan form
    public function create()
    {
        return view('pages.mahasiswa.pengajuan.create');
    }

    // Menyimpan data form
    public function store(Request $request)
    {
        // Nanti logika untuk validasi dan simpan data ada di sini
        return redirect()->route('mahasiswa.riwayat.index')->with('success', 'Pengajuan berhasil dikirim!');
    }
}