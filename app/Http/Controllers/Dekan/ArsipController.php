<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index()
    {
        // Nanti di sini logika untuk mengambil data surat yang sudah diarsip
        return view('pages.dekan.arsip.index');
    }
}