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

    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs mb-3" id="suratMagangTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                <i class="fas fa-clock me-2"></i>Menunggu Persetujuan
                <span class="badge bg-warning text-dark ms-2">{{ count($daftarSurat) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-check-circle me-2"></i>History Disetujui
                <span class="badge bg-success ms-2">{{ count($riwayatSurat) }}</span>
            </button>
        </li>
    </ul>

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

    {{-- Tab Content --}}
    <div class="tab-content" id="suratMagangTabContent">
        {{-- Tab Menunggu Persetujuan --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
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
                            <th class="ps-4">ID</th>
                            <th>Nomor Surat</th>
                            <th>Pemohon</th>
                            <th>Instansi Magang</th>
                            <th>Tanggal Diajukan</th>
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
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary">#SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $surat->Nomor_Surat ?? '-' }}</div>
                                <small class="text-muted">{{ $surat->Nomor_Surat ? 'Sudah diberi nomor' : 'Belum ada nomor' }}</small>
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
                                <div class="d-flex flex-column">
                                    <span class="text-sm fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</span>
                                    <span class="text-xs text-muted">{{ Str::limit($surat->Alamat_Instansi ?? '-', 40) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-secondary">
                                    {{ $surat->created_at ? \Carbon\Carbon::parse($surat->created_at)->format('d M Y, H:i') : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('dekan.surat_magang.show', $surat->id_no) }}" 
                                   class="btn btn-primary btn-sm shadow-sm px-3">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
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

        {{-- Tab History Disetujui --}}
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-check-circle me-2"></i>Riwayat Surat yang Telah Disetujui
                    </h6>
                    <span class="badge bg-success text-white">{{ count($riwayatSurat) }} Surat</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead class="bg-light text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Nomor Surat</th>
                                    <th>Pemohon</th>
                                    <th>Instansi Magang</th>
                                    <th>Tanggal Disetujui</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatSurat as $index => $surat)
                                @php
                                    $mahasiswa = $surat->tugasSurat?->pemberiTugas?->mahasiswa ?? null;
                                    $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                                    $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                                    $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-success">#SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $surat->Nomor_Surat ?? '-' }}</div>
                                        <small class="text-success"><i class="fas fa-check-circle me-1"></i>Lengkap</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-gradient-success rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
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
                                        <div class="d-flex flex-column">
                                            <span class="text-sm fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</span>
                                            <span class="text-xs text-muted">{{ Str::limit($surat->Alamat_Instansi ?? '-', 40) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm text-secondary">
                                            {{ $surat->created_at ? \Carbon\Carbon::parse($surat->created_at)->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-check-double me-1"></i> {{ $surat->Status }}
                                        </span>
                                        @if($surat->Qr_code_dekan)
                                            <br><small class="text-success"><i class="fas fa-qrcode me-1"></i>TTD Digital</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dekan.surat_magang.show', $surat->id_no) }}" 
                                           class="btn btn-info btn-sm shadow-sm px-3">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
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
                                            <h5 class="text-muted fw-bold">Belum ada riwayat</h5>
                                            <p class="text-muted mb-0">Belum ada surat yang disetujui.</p>
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
