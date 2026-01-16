@extends('layouts.kaprodi')

@section('title', 'History Pengajuan KP/Magang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">History Pengajuan KP/Magang</h1>
            <p class="text-muted mb-0">Riwayat pengajuan surat pengantar magang yang telah diproses.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('kaprodi.surat.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Permintaan
            </a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kaprodi.surat.index') }}">Permintaan Surat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">History</li>
                </ol>
            </nav>
        </div>
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

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Riwayat Pengajuan</h6>
            <span class="badge bg-secondary">{{ count($daftarSurat) }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Jenis Surat</th>
                            <th>Instansi Tujuan</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarSurat as $index => $surat)
                        @php
                            $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
                            $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                            $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                            $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                            $prodiMahasiswa = $mahasiswa?->prodi->Nama_Prodi ?? 'N/A';
                            
                            // Tentukan status class dan text
                            if ($surat->Status === 'Ditolak-Kaprodi') {
                                $statusClass = 'danger';
                                $statusIcon = 'times-circle';
                                $statusText = 'Ditolak';
                            } else {
                                $statusClass = 'success';
                                $statusIcon = 'check-circle';
                                $statusText = 'Disetujui';
                            }
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($namaMahasiswa, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $namaMahasiswa }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $nimMahasiswa }}</p>
                                        <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.65rem;">{{ $prodiMahasiswa }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm font-weight-bold text-dark">
                                    {{ $surat->tugasSurat->jenisSurat->Nama_Surat ?? 'Surat Pengantar Magang' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-sm fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</span>
                                    <span class="text-xs text-muted"><i class="far fa-calendar-alt me-1"></i>
                                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary text-xs font-weight-bold">
                                    @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                        {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $statusClass }} border border-{{ $statusClass }}">
                                    <i class="fas fa-{{ $statusIcon }} me-1"></i> {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#detailModal{{ $surat->id_no }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="bg-light rounded-circle p-4 mb-3">
                                        <i class="fas fa-history fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">Belum ada riwayat pengajuan</h5>
                                    <p class="text-muted mb-0">History akan muncul setelah Anda memproses pengajuan surat.</p>
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

{{-- Modals --}}
@foreach($daftarSurat as $index => $surat)
@php
    $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
    $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
    $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
    $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
    
    $dosenPembimbing = is_array($surat->Data_Dosen_pembiming) 
        ? $surat->Data_Dosen_pembiming 
        : json_decode($surat->Data_Dosen_pembiming, true);
    
    // Tentukan status class dan text untuk modal
    if ($surat->Status === 'Ditolak-Kaprodi') {
        $statusClass = 'danger';
        $statusText = 'Ditolak';
    } else {
        $statusClass = 'success';
        $statusText = 'Disetujui';
    }
@endphp

<!-- Modal Detail -->
<div class="modal fade" id="detailModal{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-{{ $statusClass }} text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-alt me-2"></i>Detail Pengajuan - {{ $statusText }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row h-100">
                    <!-- Kolom Kiri: Detail -->
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold text-primary border-bottom">
                                <i class="fas fa-user-graduate me-2"></i>Data Mahasiswa
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small text-muted text-uppercase fw-bold">Nama Lengkap</label>
                                    <div class="fw-bold text-dark">{{ $namaMahasiswa }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted text-uppercase fw-bold">NIM</label>
                                    <div class="text-dark">{{ $nimMahasiswa }}</div>
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted text-uppercase fw-bold">Program Studi</label>
                                    <div class="text-dark">{{ $mahasiswa?->prodi->Nama_Prodi ?? 'N/A' }}</div>
                                </div>
                                
                                @if(count($dataMahasiswa) > 1)
                                <hr class="my-3">
                                <label class="small text-muted text-uppercase fw-bold mb-2">Anggota Kelompok ({{ count($dataMahasiswa) }})</label>
                                <ul class="list-group list-group-flush small">
                                    @foreach($dataMahasiswa as $mhs)
                                        @if(($mhs['nim'] ?? '') !== $nimMahasiswa)
                                        <li class="list-group-item px-0 bg-transparent">
                                            <i class="fas fa-user me-2 text-secondary"></i>{{ $mhs['nama'] ?? '' }}
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold text-primary border-bottom">
                                <i class="fas fa-briefcase me-2"></i>Detail Magang
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small text-muted text-uppercase fw-bold">Instansi Tujuan</label>
                                    <div class="fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted text-uppercase fw-bold">Periode</label>
                                    <div class="text-dark">
                                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d 
                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted text-uppercase fw-bold">Dosen Pembimbing</label>
                                    <div class="text-dark">{{ $dosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</div>
                                    @if(isset($dosenPembimbing['dosen_pembimbing_2']) && $dosenPembimbing['dosen_pembimbing_2'])
                                        <div class="text-dark mt-1">{{ $dosenPembimbing['dosen_pembimbing_2'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-{{ $statusClass }} border-bottom">
                                <i class="fas fa-info-circle me-2"></i>Status Pengajuan
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="small text-muted text-uppercase fw-bold">Status</label>
                                    <div>
                                        <span class="badge bg-{{ $statusClass }} fs-6">
                                            <i class="fas fa-{{ $surat->Status === 'Ditolak-Kaprodi' ? 'times-circle' : 'check-circle' }} me-1"></i>
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted text-uppercase fw-bold">Tanggal Diproses</label>
                                    <div class="text-dark">
                                        @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                            {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                
                                @if($surat->Status === 'Ditolak-Kaprodi' && $surat->Komentar)
                                <hr class="my-3">
                                <div class="alert alert-danger mb-0">
                                    <label class="small text-uppercase fw-bold mb-1">Alasan Penolakan:</label>
                                    <p class="mb-0">{{ $surat->Komentar }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Preview Dokumen -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="previewTab{{ $surat->id_no }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-bold" id="surat-tab{{ $surat->id_no }}" data-bs-toggle="tab" data-bs-target="#surat-preview{{ $surat->id_no }}" type="button" role="tab">
                                            <i class="fas fa-file-alt me-2"></i>Form Pengantar
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-bold" id="proposal-tab{{ $surat->id_no }}" data-bs-toggle="tab" data-bs-target="#proposal-preview{{ $surat->id_no }}" type="button" role="tab">
                                            <i class="fas fa-file-pdf me-2"></i>Proposal Magang
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-0 bg-secondary bg-opacity-10">
                                <div class="tab-content h-100" id="previewTabContent{{ $surat->id_no }}">
                                    <!-- Tab 1: Preview Surat Pengantar -->
                                    <div class="tab-pane fade show active h-100 p-4 overflow-auto" id="surat-preview{{ $surat->id_no }}" role="tabpanel">
                                        <div class="paper-preview mx-auto shadow-sm">
                                            {{-- Header Surat --}}
                                            <div class="text-center mb-4 border-bottom border-dark pb-3">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo" style="height: 80px;" class="me-3">
                                                    <div class="text-center">
                                                        <h6 class="mb-0 fw-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h6>
                                                        <h5 class="mb-0 fw-bold">UNIVERSITAS TRUNODJOYO</h5>
                                                        <h4 class="mb-0 fw-bold">FAKULTAS TEKNIK</h4>
                                                        <small class="d-block">Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5 class="text-center fw-bold text-decoration-underline mb-4">FORM PENGAJUAN SURAT PENGANTAR</h5>

                                            <table class="table table-borderless table-sm mb-4">
                                                <tr>
                                                    <td width="30%">Nama</td>
                                                    <td width="2%">:</td>
                                                    <td>
                                                        @foreach($dataMahasiswa as $idx => $mhs)
                                                            <div class="mb-1">
                                                                <strong>{{ $idx + 1 }}. {{ $mhs['nama'] ?? '' }}</strong> 
                                                                (NIM: {{ $mhs['nim'] ?? '' }})
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Jurusan</td>
                                                    <td>:</td>
                                                    <td>{{ $mahasiswa?->prodi->Nama_Prodi ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Dosen Pembimbing</td>
                                                    <td>:</td>
                                                    <td>{{ $dosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Instansi Tujuan</td>
                                                    <td>:</td>
                                                    <td><strong>{{ $surat->Nama_Instansi ?? '-' }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Periode Magang</td>
                                                    <td>:</td>
                                                    <td>
                                                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d 
                                                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>

                                            <div class="row mt-5">
                                                <div class="col-6 text-center">
                                                    <p class="mb-3">Menyetujui,<br>Koordinator KP/TA</p>
                                                    @if($surat->Status !== 'Ditolak-Kaprodi' && $surat->Qr_code)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $surat->Qr_code) }}" alt="QR Code" style="width: 100px; height: 100px;">
                                                        </div>
                                                    @else
                                                        <div class="mb-5"></div>
                                                    @endif
                                                    <p class="fw-bold mb-0">{{ $surat->koordinator->Nama_Dosen ?? '[Nama Kaprodi]' }}</p>
                                                    <p class="small">NIP. {{ $surat->koordinator->NIP ?? '...' }}</p>
                                                </div>
                                                <div class="col-6 text-center">
                                                    <p class="mb-3">Bangkalan, {{ \Carbon\Carbon::parse($surat->created_at)->format('d M Y') }}<br>Pemohon</p>
                                                    @if($surat->Foto_ttd)
                                                        <img src="{{ asset('storage/' . $surat->Foto_ttd) }}" height="60" class="mb-2">
                                                    @else
                                                        <div class="mb-5"></div>
                                                    @endif
                                                    <p class="fw-bold mb-0">{{ $namaMahasiswa }}</p>
                                                    <p class="small">NIM. {{ $nimMahasiswa }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 2: Preview Proposal -->
                                    <div class="tab-pane fade h-100" id="proposal-preview{{ $surat->id_no }}" role="tabpanel">
                                        @if($surat->Dokumen_Proposal)
                                            <iframe src="{{ asset('storage/' . $surat->Dokumen_Proposal) }}" width="100%" height="600px" class="border-0">
                                                <div class="alert alert-warning m-3">
                                                    Browser Anda tidak mendukung preview PDF. 
                                                    <a href="{{ asset('storage/' . $surat->Dokumen_Proposal) }}" class="btn btn-primary btn-sm" target="_blank">Download Proposal</a>
                                                </div>
                                            </iframe>
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                <i class="fas fa-file-excel fa-4x mb-3"></i>
                                                <h5>Proposal Tidak Ditemukan</h5>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @if($surat->Dokumen_Proposal)
                <a href="{{ route('kaprodi.surat.download', $surat->id_no) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Download Proposal
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .paper-preview {
        background: white;
        width: 100%;
        max-width: 210mm;
        min-height: 297mm;
        padding: 20mm;
        margin: 0 auto;
        border: 1px solid #d3d3d3;
        font-family: 'Times New Roman', Times, serif;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #0d6efd;
    }
    .border-start-success {
        border-left: 4px solid #198754 !important;
    }
</style>
@endsection
