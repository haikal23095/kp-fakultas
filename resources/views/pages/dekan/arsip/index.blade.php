@extends('layouts.app')

@section('title', 'Arsip Surat')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Arsip Surat Fakultas</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Semua Surat Terarsip</h6>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Cari surat..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
            </form>
        </div>
        <div class="card-body">
            <p>Halaman ini akan berisi tabel atau daftar semua surat yang telah ditandatangani dan diarsipkan.</p>
        </div>
    </div>
@endsection