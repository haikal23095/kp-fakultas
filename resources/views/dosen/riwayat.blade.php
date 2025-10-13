@extends('layouts.dosen')

@section('title', 'Riwayat Pengajuan')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Pengajuan Surat</h1>
</div>

{{-- Tabel Riwayat --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Seluruh Riwayat Tindakan</h6>
    </div>
    <div class="card-body">
        {{-- Filter --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="date" class="form-control">
            </div>
            <div class="col-md-4">
                <select class="form-select">
                    <option selected>Semua Status</option>
                    <option value="1">Disetujui</option>
                    <option value="2">Ditolak</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Diproses</th>
                        <th>Nama Mahasiswa</th>
                        <th>Jenis Surat</th>
                        <th class="text-center">Status Akhir</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>11 Okt 2025</td>
                        <td>Budi Santoso</td>
                        <td>Surat Rekomendasi Lomba</td>
                        <td class="text-center"><span class="badge bg-success">Disetujui</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-secondary btn-sm" title="Lihat Detail Arsip"><i class="fas fa-archive"></i></a>
                        </td>
                    </tr>
                     <tr>
                        <td>10 Okt 2025</td>
                        <td>Siti Aminah</td>
                        <td>Surat Izin Penelitian</td>
                        <td class="text-center"><span class="badge bg-danger">Ditolak</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-secondary btn-sm" title="Lihat Detail Arsip"><i class="fas fa-archive"></i></a>
                        </td>
                    </tr>
                     <tr>
                        <td>09 Okt 2025</td>
                        <td>Adi Nugroho</td>
                        <td>Surat Keterangan Aktif Kuliah</td>
                        <td class="text-center"><span class="badge bg-success">Disetujui</span></td>
                        <td class="text-center">
                           <a href="#" class="btn btn-secondary btn-sm" title="Lihat Detail Arsip"><i class="fas fa-archive"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection