@extends('layouts.admin')

@section('title', 'Arsip Surat')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Arsip Surat Fakultas</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pencarian Arsip</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl. Selesai</th>
                        <th>No. Surat</th>
                        <th>Pengaju</th>
                        <th>Jenis Surat</th>
                        <th class="text-center">Status Akhir</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>11 Okt 2025</td>
                        <td>101/UNIV/FAK/X/2025</td>
                        <td>Budi Santoso</td>
                        <td>Surat Rekomendasi Lomba</td>
                        <td class="text-center"><span class="badge bg-success">Disetujui</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-sm" title="Lihat & Unduh Arsip"><i class="fas fa-download"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>10 Okt 2025</td>
                        <td>-</td>
                        <td>Siti Aminah</td>
                        <td>Surat Izin Penelitian</td>
                        <td class="text-center"><span class="badge bg-danger">Ditolak</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lihat Detail Penolakan"><i class="fas fa-info-circle"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection