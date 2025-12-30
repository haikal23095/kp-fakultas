<?php

namespace App\Http\Controllers\Wadek3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KemahasiswaanController extends Controller
{
    public function validasiDispensasi()
    {
        return view('wadek3.kemahasiswaan.validasi-dispensasi');
    }

    public function validasiKelakuanBaik()
    {
        return view('wadek3.kemahasiswaan.validasi-kelakuan-baik');
    }
}
