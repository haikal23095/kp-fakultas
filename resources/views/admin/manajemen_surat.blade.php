@extends('layouts.admin')

@section('title', 'Manajemen Surat')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Manajemen Surat Fakultas</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Surat Aktif</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl. Masuk</th>
                        <th>No. Tiket</th>
                        <th>Pengaju</th>
                        <th>Jenis Surat</th>
                        <th>Proses Saat Ini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>14 Okt 2025</td>
                        <td>SK-20251014-001</td>
                        <td>Rina Amelia (Mahasiswa)</td>
                        <td>Surat Rekomendasi Beasiswa</td>
                        <td><span class="badge bg-warning text-dark">Pending Persetujuan Dosen</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lacak Proses"><i class="fas fa-search-location"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>13 Okt 2025</td>
                        <td>SK-20251013-005</td>
                        <td>Dr. Bambang, M.T. (Dosen)</td>
                        <td>Pengajuan Dana Riset</td>
                        <td><span class="badge bg-warning text-dark">Pending Persetujuan Dekan</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lacak Proses"><i class="fas fa-search-location"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection