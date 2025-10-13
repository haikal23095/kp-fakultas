@extends('layouts.kaprodi')

@section('title', 'Penjadwalan Kuliah')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Penjadwalan Kuliah</h1>
    <a href="#" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Jadwal Baru
    </a>
</div>

{{-- Filter Jadwal --}}
<div class="card shadow mb-4">
    <div class="card-body">
         <form class="row g-3 align-items-center">
            <div class="col-md-5">
                <label for="tahunAkademik" class="form-label">Tahun Akademik</label>
                <select id="tahunAkademik" class="form-select">
                    <option selected>2025/2026</option>
                    <option>2024/2025</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="semester" class="form-label">Semester</label>
                <select id="semester" class="form-select">
                    <option selected>Ganjil</option>
                    <option>Genap</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-info w-100">Terapkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Jadwal Kuliah --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Jadwal Kuliah Semester Ganjil 2025/2026</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen Pengampu</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Ruangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Algoritma dan Struktur Data</td>
                        <td>A</td>
                        <td>Dr. Rina Amelia, M.Cs.</td>
                        <td>Senin</td>
                        <td>08:00 - 10:30</td>
                        <td>Lab Komputer 1</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Ubah Jadwal"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus Jadwal"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Basis Data Lanjutan</td>
                        <td>B</td>
                        <td>Bambang Haryanto, M.T.</td>
                        <td>Selasa</td>
                        <td>13:00 - 15:30</td>
                        <td>Ruang Teori 201</td>
                         <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Ubah Jadwal"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus Jadwal"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Kecerdasan Buatan</td>
                        <td>A</td>
                        <td>Dr. Siti Aminah, M.Kom.</td>
                        <td>Rabu</td>
                        <td>10:00 - 12:30</td>
                        <td>Ruang Teori 305</td>
                         <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm me-1" title="Ubah Jadwal"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus Jadwal"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection