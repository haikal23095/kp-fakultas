@extends('layouts.kajur')

@section('title', 'Laporan Kinerja Jurusan')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Kinerja Jurusan</h1>
</div>

{{-- Filter Periode Laporan --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-center">
            <div class="col-md-5">
                <label for="tahunAkademik" class="form-label">Tahun Akademik</label>
                <select id="tahunAkademik" class="form-select">
                    <option selected>2024/2025</option>
                    <option>2023/2024</option>
                    <option>2022/2023</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="semester" class="form-label">Semester</label>
                <select id="semester" class="form-select">
                    <option selected>Ganjil</option>
                    <option>Genap</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
            </div>
        </form>
    </div>
</div>

{{-- Kartu Statistik Utama --}}
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata IPK Jurusan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">3.45</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-graduation-cap fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kelulusan Tepat Waktu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">92%</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
     <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Mahasiswa Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">450</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grafik --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Grafik Tren IPK per Angkatan (Semester Ganjil 2024/2025)</h6>
    </div>
    <div class="card-body">
        <div class="chart-area">
            {{-- Elemen canvas untuk Chart.js atau library grafik lainnya --}}
            <canvas id="myAreaChart"></canvas>
        </div>
        <div class="text-center small mt-4">
            <span class="me-2"><i class="fas fa-circle text-primary"></i> Angkatan 2024</span>
            <span class="me-2"><i class="fas fa-circle text-success"></i> Angkatan 2023</span>
            <span class="me-2"><i class="fas fa-circle text-info"></i> Angkatan 2022</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Tempat untuk meletakkan script inisialisasi grafik (misal: Chart.js) --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
{{-- <script> /* kode javascript untuk grafik */ </script> --}}
@endpush

@push('styles')
<style>
    .card .border-left-info { border-left: .25rem solid #1cc88a !important; }
    .card .border-left-success { border-left: .25rem solid #4e73df !important; }
</style>
@endpush