@extends('layouts.admin_prodi')

@section('title', 'Manajemen Surat')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Manajemen Surat</h1>
            <p class="text-muted mb-0">Proses surat yang telah disetujui Kaprodi dan tambahkan nomor surat.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.admin_prodi') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Surat</li>
            </ol>
        </nav>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <ul class="nav nav-pills mb-3 bg-light p-2 rounded shadow-sm" id="mainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="magang-tab" data-bs-toggle="pill" data-bs-target="#magang-content" type="button" role="tab" aria-controls="magang-content" aria-selected="true">
                <i class="fas fa-briefcase me-2"></i>KP / Magang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="aktif-tab" data-bs-toggle="pill" data-bs-target="#aktif-content" type="button" role="tab" aria-controls="aktif-content" aria-selected="false">
                <i class="fas fa-user-check me-2"></i>Keterangan Aktif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="mainTabsContent">
        {{-- SECTION: SURAT MAGANG --}}
        <div class="tab-pane fade show active" id="magang-content" role="tabpanel">
            <ul class="nav nav-tabs" id="magangTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-primary fw-bold" id="magang-pending-tab" data-bs-toggle="tab" data-bs-target="#magang-pending" type="button" role="tab">
                        Perlu Nomor <span class="badge bg-danger ms-1">{{ $suratMagangPending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-secondary" id="magang-semua-tab" data-bs-toggle="tab" data-bs-target="#magang-semua" type="button" role="tab">
                        Semua
                    </button>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white shadow-sm rounded-bottom">
                <div class="tab-pane fade show active" id="magang-pending" role="tabpanel">
                    @include('admin_prodi.partials.table_surat', ['suratData' => $suratMagangPending, 'type' => 'magang', 'mode' => 'pending'])
                </div>
                <div class="tab-pane fade" id="magang-semua" role="tabpanel">
                    @include('admin_prodi.partials.table_surat', ['suratData' => $suratMagangSemua, 'type' => 'magang', 'mode' => 'all'])
                </div>
            </div>
        </div>

        {{-- SECTION: SURAT KETERANGAN AKTIF --}}
        <div class="tab-pane fade" id="aktif-content" role="tabpanel">
            <ul class="nav nav-tabs" id="aktifTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-primary fw-bold" id="aktif-pending-tab" data-bs-toggle="tab" data-bs-target="#aktif-pending" type="button" role="tab">
                        Perlu Proses <span class="badge bg-danger ms-1">{{ $suratAktifPending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-secondary" id="aktif-semua-tab" data-bs-toggle="tab" data-bs-target="#aktif-semua" type="button" role="tab">
                        Semua
                    </button>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white shadow-sm rounded-bottom">
                <div class="tab-pane fade show active" id="aktif-pending" role="tabpanel">
                    @include('admin_prodi.partials.table_surat', ['suratData' => $suratAktifPending, 'type' => 'aktif', 'mode' => 'pending'])
                </div>
                <div class="tab-pane fade" id="aktif-semua" role="tabpanel">
                    @include('admin_prodi.partials.table_surat', ['suratData' => $suratAktifSemua, 'type' => 'aktif', 'mode' => 'all'])
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link.active {
        background-color: #4e73df;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #4e73df;
    }
</style>
@endsection



