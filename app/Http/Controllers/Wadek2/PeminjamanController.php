<?php

namespace App\Http\Controllers\Wadek2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function persetujuanMobil()
    {
        return view('wadek2.sarpras.persetujuan-mobil');
    }

    public function persetujuanRuang()
    {
        return view('wadek2.sarpras.persetujuan-ruang');
    }
}
