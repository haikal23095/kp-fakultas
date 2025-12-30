<?php

namespace App\Http\Controllers\Wadek2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratKeputusanController extends Controller
{
    public function validasiSKFakultas()
    {
        return view('wadek2.sk.validasi-sk-fakultas');
    }
}
