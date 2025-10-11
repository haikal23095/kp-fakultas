<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LegalisirController extends Controller
{
    public function create()
    {
        return view('pages.mahasiswa.legalisir.create');
    }
}