@extends('layouts.app')

@section('title', 'Riwayat Surat')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Riwayat & Status Surat</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Pengajuan Surat Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Jenis Surat</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Surat Keterangan Aktif Kuliah</td>
                            <td>10 Okt 2025</td>
                            <td><span class="badge bg-success">Selesai</span></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download me-1"></i> Unduh
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Surat Keterangan Tidak Menerima Beasiswa</td>
                            <td>08 Okt 2025</td>
                            <td><span class="badge bg-info">Ditandatangani</span></td>
                            <td class="text-center">-</td>
                        </tr>
                         <tr>
                            <td>Surat Pengantar Kerja Praktik</td>
                            <td>05 Okt 2025</td>
                            <td><span class="badge bg-warning text-dark">Diproses Admin</span></td>
                             <td class="text-center">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection