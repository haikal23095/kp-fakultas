@extends('layouts.kajur')

@section('title', 'Verifikasi RPS')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Verifikasi Rencana Pembelajaran Semester (RPS)</h1>
</div>

{{-- Kartu Statistik --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total RPS Masuk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-import fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Verifikasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sudah Diverifikasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">10</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-check-double fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel RPS --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar RPS Masuk</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Kode MK</th>
                        <th>Dosen Pengampu</th>
                        <th>Tgl. Pengajuan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Algoritma dan Struktur Data</td>
                        <td>INF-101</td>
                        <td>Dr. Rina Amelia, M.Cs.</td>
                        <td>14 Okt 2025</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Menunggu Verifikasi</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm me-1" title="Lihat & Verifikasi"><i class="fas fa-search-plus"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Jaringan Komputer</td>
                        <td>INF-203</td>
                        <td>Bambang Haryanto, M.T.</td>
                        <td>13 Okt 2025</td>
                        <td class="text-center"><span class="badge bg-danger">Revisi Diperlukan</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-secondary btn-sm" title="Lihat Catatan Revisi"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Manajemen Proyek TI</td>
                        <td>INF-305</td>
                        <td>Dr. Siti Aminah, M.Kom.</td>
                        <td>11 Okt 2025</td>
                        <td class="text-center"><span class="badge bg-success">Disetujui</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-secondary btn-sm" title="Lihat Arsip"><i class="fas fa-archive"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection