<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KaprodiController extends Controller
{
    /**
     * Tampilkan halaman kurikulum
     */
    public function kurikulum()
    {
        return view('kaprodi.kurikulum');
    }

    /**
     * Tampilkan halaman jadwal kuliah
     */
    public function jadwalKuliah()
    {
        return view('kaprodi.jadwal_kuliah');
    }
}
