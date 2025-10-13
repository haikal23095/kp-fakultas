@extends('layouts.dosen')

@section('title', 'Riwayat Pengajuan')

@section('content')
    <h1 class="h3 fw-bold mb-4">Riwayat Pengajuan Surat Tugas Anda</h1>

    <div class="card shadow-sm border-0">
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
                            <td>Surat Tugas Pemateri Seminar</td>
                            <td>10 Okt 2025</td>
                            <td><span class="badge bg-success">Selesai</span></td>
                            <td class="text-center"><a href="#" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Unduh</a></td>
                        </tr>
                        <tr>
                            <td>Surat Tugas Penelitian</td>
                            <td>05 Okt 2025</td>
                            <td><span class="badge bg-warning text-dark">Menunggu Persetujuan Jurusan</span></td>
                            <td class="text-center">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection