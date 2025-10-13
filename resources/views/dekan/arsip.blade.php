@extends('layouts.dekan')

@section('title', 'Arsip Surat Fakultas')

@section('content')
    <h1 class="h3 fw-bold mb-4">Arsip Surat Fakultas</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Pencarian Arsip</h6>
        </div>
        <div class="card-body">
            {{-- Bagian Filter & Pencarian --}}
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-md-5">
                    <input type="text" class="form-control" placeholder="Cari berdasarkan nomor surat atau nama pemohon...">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Semua Jenis Surat</option>
                        <option>Surat Tugas Dosen</option>
                        <option>Surat Aktif Kuliah</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100">Cari</button>
                </div>
            </div>

            {{-- Tabel Hasil Arsip --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nomor Surat</th>
                            <th>Jenis Surat</th>
                            <th>Pemohon</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>123/UN37.1.4/KM/2025</td>
                            <td>Surat Keterangan Aktif Kuliah</td>
                            <td>Agus Setiawan</td>
                            <td>12 Okt 2025</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                         <tr>
                            <td>122/UN37.1.4/TU/2025</td>
                            <td>Surat Tugas Dosen</td>
                            <td>Prof. Budi Hartono, M.Kom.</td>
                            <td>11 Okt 2025</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection