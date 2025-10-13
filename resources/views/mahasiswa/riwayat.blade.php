@extends('layouts.mahasiswa')

@section('title', 'Riwayat Pengajuan Surat')

@section('content')
    <h1 class="h3 fw-bold mb-4">Riwayat Pengajuan Surat</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Jenis Surat</th>
                            <th scope="col">Tanggal Pengajuan</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Surat Keterangan Aktif Kuliah</td>
                            <td>10 Okt 2025</td>
                            <td><span class="badge bg-success">Selesai</span></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Unduh</a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Surat Pengantar Kerja Praktik</td>
                            <td>08 Okt 2025</td>
                            <td><span class="badge bg-info">Ditandatangani</span></td>
                            <td class="text-center">-</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Surat Keterangan Tidak Menerima Beasiswa</td>
                            <td>05 Okt 2025</td>
                            <td><span class="badge bg-warning text-dark">Diproses Admin</span></td>
                            <td class="text-center">-</td>
                        </tr>
                        <tr>
                            <th scope="row">4</th>
                            <td>Surat Keterangan Aktif Kuliah</td>
                            <td>01 Okt 2025</td>
                            <td><span class="badge bg-danger">Ditolak</span></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-secondary btn-sm"><i class="fas fa-info-circle"></i> Detail</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection