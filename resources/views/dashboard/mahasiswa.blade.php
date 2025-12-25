@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    
    .card-hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .2s ease-in-out;
    }
</style>
@endpush

@section('content')

{{-- Page Heading --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard</h1>
        <p class="text-muted small mb-0">Selamat datang kembali, {{ auth()->user()->Name_User ?? 'Mahasiswa' }}!</p>
    </div>
    <div class="d-none d-sm-block">
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="far fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
        </span>
    </div>
</div>

{{-- Content Row --}}
<div class="row">

    {{-- Total Pengajuan Card --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 card-hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pengajuan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPengajuan ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Diterima Card --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 card-hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Disetujui</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $diterima ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Ditolak Card --}}
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 card-hover-shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ditolak ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Area Chart / Main Content (History) --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengajuan Terkini</h6>
                <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="ps-4">Jenis Surat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatTerkini ?? [] as $surat)
                            @php
                                // Status logic
                                $statusRaw = optional($surat->suratMagang)->Status ?? '';
                                $status = strtolower(trim($statusRaw));
                                $badgeClass = match($status) {
                                    'success' => 'bg-success',
                                    'ditolak' => 'bg-danger',
                                    'diajukan-ke-koordinator' => 'bg-warning text-dark',
                                    'dikerjakan-admin' => 'bg-info',
                                    'diajukan-ke-dekan' => 'bg-primary',
                                    default => 'bg-secondary'
                                };
                                $statusText = match($status) {
                                    'success' => 'Success',
                                    'ditolak' => 'Ditolak',
                                    'diajukan-ke-koordinator' => 'Diajukan ke Koordinator',
                                    'dikerjakan-admin' => 'Dikerjakan Admin',
                                    'diajukan-ke-dekan' => 'Diajukan ke Dekan',
                                    default => ucfirst($surat->Status ?? '-')
                                };
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $surat->tugasSurat->jenisSurat->Nama_Surat ?? 'Tidak Diketahui' }}</div>
                                    <div class="small text-muted">ID: #{{ $surat->Id_Tugas_Surat }}</div>
                                </td>
                                <td>
                                    <i class="far fa-calendar me-1 text-muted"></i>
                                    {{ $surat->tugasSurat ? \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}
                                </td>
                                <td><span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $statusText }}</span></td>
                                <td class="text-center pe-4">
                                    @if($status === 'success' && $surat->Surat_Pengantar_Magang)
                                        <a href="{{ asset('storage/' . $surat->Surat_Pengantar_Magang) }}" class="btn btn-success btn-sm rounded-circle" download title="Download Surat">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="fas fa-minus"></i></span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <img src="https://img.icons8.com/ios/100/cccccc/empty-box.png" alt="Empty" style="width: 60px; opacity: 0.5;" class="mb-3 d-block mx-auto">
                                    Belum ada riwayat pengajuan surat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Chart / Sidebar (Quick Actions) --}}
    <div class="col-lg-4 mb-4">
        
        {{-- CTA Card --}}
        <div class="card shadow mb-4 bg-gradient-primary text-white" style="background: linear-gradient(45deg, #4e73df, #224abe);">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-paper-plane fa-3x mb-3 text-white-50"></i>
                    <h5 class="fw-bold">Butuh Surat Baru?</h5>
                    <p class="small text-white-50">Ajukan surat keterangan aktif, surat pengantar magang, atau surat rekomendasi dengan mudah dan cepat.</p>
                </div>
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-light text-primary fw-bold w-100 shadow-sm py-2">
                    <i class="fas fa-plus-circle me-2"></i>Buat Pengajuan
                </a>
            </div>
        </div>

        {{-- Information Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-dark">Informasi Penting</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-info-circle text-primary mt-1 me-3"></i>
                        <span class="small text-muted">Pastikan data profil Anda (NIM, Prodi) sudah sesuai sebelum mengajukan surat.</span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-clock text-warning mt-1 me-3"></i>
                        <span class="small text-muted">Proses verifikasi surat biasanya memakan waktu <strong>1-3 hari kerja</strong>.</span>
                    </li>
                    <li class="d-flex">
                        <i class="fas fa-bell text-danger mt-1 me-3"></i>
                        <span class="small text-muted">Cek notifikasi secara berkala untuk mengetahui status pengajuan Anda.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

@endsection