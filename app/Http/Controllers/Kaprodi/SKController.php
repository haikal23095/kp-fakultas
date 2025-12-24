<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SKController extends Controller
{
    /**
     * Display the SK main page with 4 cards
     */
    public function index()
    {
        return view('kaprodi.sk.index');
    }

    /**
     * Show the form for creating SK Beban Mengajar
     */
    public function createBebanMengajar()
    {
        // TODO: Implement form for SK Beban Mengajar
        return view('kaprodi.sk.beban-mengajar.create');
    }

    /**
     * Show the form for creating SK Dosen Wali
     */
    public function createDosenWali()
    {
        // TODO: Implement form for SK Dosen Wali
        return view('kaprodi.sk.dosen-wali.create');
    }

    /**
     * Show the form for creating SK Pembimbing Skripsi
     */
    public function createPembimbingSkripsi()
    {
        // TODO: Implement form for SK Pembimbing Skripsi
        return view('kaprodi.sk.pembimbing-skripsi.create');
    }

    /**
     * Show the form for creating SK Penguji Skripsi
     */
    public function createPengujiSkripsi()
    {
        // TODO: Implement form for SK Penguji Skripsi
        return view('kaprodi.sk.penguji-skripsi.create');
    }
}
