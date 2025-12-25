@extends('layouts.mahasiswa')

@section('title', 'Pilih Jenis Riwayat Surat')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Riwayat Pengajuan Surat</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Pilih jenis surat untuk melihat riwayat pengajuan</li>
    </ol>

    <div class="row">
        {{-- Card Riwayat Surat Magang --}}
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold">Surat Magang / KP</h4>
                            <p class="small mb-0">Riwayat pengajuan surat pengantar kerja praktik</p>
                        </div>
                        <i class="fas fa-briefcase fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mahasiswa.riwayat', ['type' => 'magang']) }}">
                        Lihat Riwayat
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Card Riwayat Surat Aktif --}}
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success text-white mb-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold">Surat Keterangan Aktif</h4>
                            <p class="small mb-0">Riwayat pengajuan surat keterangan aktif kuliah</p>
                        </div>
                        <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mahasiswa.riwayat', ['type' => 'aktif']) }}">
                        Lihat Riwayat
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Card Riwayat Lainnya (Optional) --}}
        <div class="col-xl-4 col-md-6">
            <div class="card bg-secondary text-white mb-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold">Surat Lainnya</h4>
                            <p class="small mb-0">Riwayat pengajuan jenis surat lainnya</p>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('mahasiswa.riwayat', ['type' => 'lainnya']) }}">
                        Lihat Riwayat
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
