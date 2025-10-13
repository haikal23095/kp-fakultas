@extends('layouts.dosen')

@section('title', 'Bimbingan Akademik')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bimbingan Akademik</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i> Generate Laporan
    </a>
</div>

{{-- Kartu Statistik Ringkasan --}}
<div class="row">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Mahasiswa Bimbingan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18 Mahasiswa</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Menunggu Validasi KRS</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">3 Mahasiswa</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Daftar Mahasiswa Wali --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Mahasiswa Wali (Perwalian)</h6>
        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Cari nama atau NIM..." aria-label="Search">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th class="text-center">Semester</th>
                        <th class="text-center">IPK</th>
                        <th class="text-center">Status KRS</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Contoh Data Mahasiswa 1 --}}
                    <tr>
                        <td>1</td>
                        <td>Ahmad Budi Santoso</td>
                        <td>2210511001</td>
                        <td class="text-center">7</td>
                        <td class="text-center">3.75</td>
                        <td class="text-center"><span class="badge bg-success">Sudah Divalidasi</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lihat Detail KHS"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    {{-- Contoh Data Mahasiswa 2 --}}
                    <tr>
                        <td>2</td>
                        <td>Citra Lestari</td>
                        <td>2210511002</td>
                        <td class="text-center">7</td>
                        <td class="text-center">3.68</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Menunggu Validasi</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm me-1" title="Lihat Detail KHS"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-success btn-sm" title="Validasi KRS"><i class="fas fa-check-circle"></i></a>
                        </td>
                    </tr>
                    {{-- Contoh Data Mahasiswa 3 --}}
                    <tr>
                        <td>3</td>
                        <td>Dewi Anggraini</td>
                        <td>2310511015</td>
                        <td class="text-center">5</td>
                        <td class="text-center">3.82</td>
                        <td class="text-center"><span class="badge bg-success">Sudah Divalidasi</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lihat Detail KHS"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    {{-- Contoh Data Mahasiswa 4 --}}
                     <tr>
                        <td>4</td>
                        <td>Eko Prasetyo</td>
                        <td>2410511040</td>
                        <td class="text-center">3</td>
                        <td class="text-center">3.50</td>
                        <td class="text-center"><span class="badge bg-danger">Belum Mengisi</span></td>
                        <td class="text-center">
                             <a href="#" class="btn btn-secondary btn-sm disabled" title="Lihat Detail KHS"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
{{-- Style kustom jika diperlukan, contoh: --}}
<style>
    .card .border-left-primary {
        border-left: .25rem solid #4e73df !important;
    }
    .card .border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }
</style>
@endpush