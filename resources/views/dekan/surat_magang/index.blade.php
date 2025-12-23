@extends('layouts.dekan')

@section('title', 'Persetujuan Surat Magang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Persetujuan Surat Magang</h1>
            <p class="text-muted mb-0">Kelola persetujuan surat pengantar magang mahasiswa.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.dekan') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Surat Magang</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-start-success" role="alert">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-success text-white me-3">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start-danger" role="alert">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-danger text-white me-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list me-2"></i>Daftar Menunggu Persetujuan Dekan
            </h6>
            <span class="badge bg-warning text-dark">{{ count($daftarSurat) }} Permintaan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                        <tr>
                            <th class="ps-4">Nomor Surat</th>
                            <th>Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Instansi Tujuan</th>
                            <th>Periode Magang</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarSurat as $index => $surat)
                        @php
                            $mahasiswa = $surat->tugasSurat?->pemberiTugas?->mahasiswa ?? null;
                            $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                            $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                            $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                            $prodiMahasiswa = $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary">{{ $surat->tugasSurat?->Nomor_Surat ?? $surat->Nomor_Surat ?? '-' }}</div>
                                <small class="text-muted">
                                    @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                        {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($namaMahasiswa, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $namaMahasiswa }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $nimMahasiswa }}</p>
                                        @if(count($dataMahasiswa) > 1)
                                        <span class="badge bg-info text-white" style="font-size: 0.6rem;">
                                            +{{ count($dataMahasiswa) - 1 }} lainnya
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-dark">{{ $prodiMahasiswa }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-sm fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</span>
                                    <span class="text-xs text-muted">{{ Str::limit($surat->Alamat_Instansi ?? '-', 30) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs text-secondary">
                                    @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }}<br>s/d<br>
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark border border-warning">
                                    <i class="fas fa-clock me-1"></i> Menunggu
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('dekan.surat_magang.show', $surat->id_no) }}" 
                                   class="btn btn-primary btn-sm shadow-sm px-3">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="bg-light rounded-circle p-4 mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">Tidak ada permintaan baru</h5>
                                    <p class="text-muted mb-0">Semua surat magang telah diproses.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .border-start-success {
        border-left: 4px solid #198754 !important;
    }
    .border-start-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
