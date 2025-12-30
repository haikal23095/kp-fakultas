<?php

namespace App\Http\Controllers\Wadek2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KepegawaianController extends Controller
{
    public function validasiCuti()
    {
        return view('wadek2.sdm.validasi-cuti');
    }

    public function validasiLembur()
    {
        return view('wadek2.sdm.validasi-lembur');
    }
}
