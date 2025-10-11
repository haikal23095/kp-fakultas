<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersetujuanController extends Controller
{
    public function index()
    {
        // Nanti di sini logika untuk mengambil data surat yang butuh persetujuan
        return view('pages.dekan.persetujuan.index');
    }
}