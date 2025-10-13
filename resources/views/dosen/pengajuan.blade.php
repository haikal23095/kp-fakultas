@extends('layouts.dosen')

@section('title', 'Persetujuan Pengajuan')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Persetujuan Pengajuan Surat</h1>
</div>

{{-- Tabel Daftar Pengajuan Masuk --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pengajuan Masuk (Menunggu Respon)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama Mahasiswa</th>
                        <th>Jenis Surat</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>13 Okt 2025</td>
                        <td>Rina Amelia</td>
                        <td>Surat Rekomendasi Beasiswa</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm me-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-success btn-sm me-1" title="Setujui"><i class="fas fa-check"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Tolak"><i class="fas fa-times"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>12 Okt 2025</td>
                        <td>Joko Susilo</td>
                        <td>Surat Keterangan Aktif Kuliah</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm me-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-success btn-sm me-1" title="Setujui"><i class="fas fa-check"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Tolak"><i class="fas fa-times"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection