@extends('layouts.kaprodi')

@section('title', 'Pengelolaan Kurikulum')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengelolaan Kurikulum Program Studi</h1>
    <a href="#" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Mata Kuliah Baru
    </a>
</div>

{{-- Filter Kurikulum --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <label for="kurikulumTahun" class="form-label">Tahun Kurikulum</label>
                <select id="kurikulumTahun" class="form-select">
                    <option selected>Kurikulum 2024</option>
                    <option>Kurikulum 2020</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="semesterFilter" class="form-label">Filter per Semester</label>
                <select id="semesterFilter" class="form-select">
                    <option selected>Tampilkan Semua Semester</option>
                    <option>Semester 1</option>
                    <option>Semester 2</option>
                    <option>Semester 3</option>
                    <option>Semester 4</option>
                    <option>Semester 5</option>
                    <option>Semester 6</option>
                    <option>Semester 7</option>
                    <option>Semester 8</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-info w-100">Filter</button>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Daftar Mata Kuliah --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Mata Kuliah - Kurikulum 2024</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th class="text-center">SKS</th>
                        <th class="text-center">Semester</th>
                        <th>Mata Kuliah Prasyarat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>INF-101</td>
                        <td>Algoritma dan Struktur Data</td>
                        <td class="text-center">4</td>
                        <td class="text-center">1</td>
                        <td>-</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>INF-203</td>
                        <td>Basis Data Lanjutan</td>
                        <td class="text-center">3</td>
                        <td class="text-center">3</td>
                        <td>Basis Data (INF-102)</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>INF-401</td>
                        <td>Kecerdasan Buatan</td>
                        <td class="text-center">3</td>
                        <td class="text-center">5</td>
                        <td>Algoritma dan Struktur Data (INF-101)</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection