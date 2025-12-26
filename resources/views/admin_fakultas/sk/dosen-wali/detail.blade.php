@extends('layouts.admin_fakultas')

@section('title', 'Detail SK Dosen Wali')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail SK Dosen Wali #{{ $sk->No }}</h1>
        <p class="mb-0 text-muted">Review dan proses pengajuan SK Dosen Wali</p>
    </div>
    <a href="{{ route('admin_fakultas.sk.dosen-wali') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Status Badge -->
<div class="alert alert-{{ $sk->Status == 'Selesai' ? 'success' : ($sk->Status == 'Ditolak' ? 'danger' : 'info') }} mb-4">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle fa-2x me-3"></i>
        <div>
            <h6 class="mb-1">Status: <strong>{{ $sk->Status }}</strong></h6>
            @php
                $tanggalTenggat = $sk->{'Tanggal-Tenggat'};
                $isOverdue = $tanggalTenggat && $tanggalTenggat->isPast();
            @endphp
            <small>
                Tenggat: {{ $tanggalTenggat ? $tanggalTenggat->format('d M Y H:i') : '-' }}
                @if($isOverdue && $sk->Status != 'Selesai')
                    <span class="text-danger fw-bold">(MELEWATI TENGGAT!)</span>
                @endif
            </small>
        </div>
    </div>
</div>

<!-- Identitas Pengajuan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary bg-opacity-10 border-0">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="fas fa-info-circle me-2"></i>Identitas Pengajuan
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="fw-semibold">Program Studi:</td>
                        <td>{{ $sk->prodi->Nama_Prodi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Semester:</td>
                        <td>
                            <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $sk->Semester }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Tahun Akademik:</td>
                        <td>{{ $sk->Tahun_Akademik }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%" class="fw-semibold">Tanggal Pengajuan:</td>
                        <td>{{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Tanggal Tenggat:</td>
                        <td>{{ $sk->{'Tanggal-Tenggat'} ? $sk->{'Tanggal-Tenggat'}->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Jumlah Dosen:</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ is_array($sk->Data_Dosen_Wali) ? count($sk->Data_Dosen_Wali) : 0 }} Dosen
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Dosen Wali -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-success bg-opacity-10 border-0">
        <h5 class="mb-0 fw-bold text-success">
            <i class="fas fa-users me-2"></i>Daftar Dosen Wali
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="35%">Nama Dosen</th>
                        <th width="25%">NIP</th>
                        <th width="20%" class="text-center">Jumlah Anak Wali</th>
                        <th width="15%" class="text-center">Total Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAnakWali = 0;
                        $dosenData = $sk->Data_Dosen_Wali;
                        
                        // Handle double-encoded JSON (old data)
                        if (is_string($dosenData)) {
                            $dosenData = json_decode($dosenData, true);
                        }
                        
                        // Calculate total
                        if (is_array($dosenData)) {
                            foreach ($dosenData as $d) {
                                $totalAnakWali += $d['jumlah_anak_wali'] ?? 0;
                            }
                        }
                    @endphp
                    @if(is_array($dosenData) && count($dosenData) > 0)
                        @foreach($dosenData as $index => $dosen)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-user-circle text-success me-2"></i>
                                    {{ $dosen['nama_dosen'] ?? 'N/A' }}
                                </td>
                                <td>{{ $dosen['nip'] ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ $dosen['jumlah_anak_wali'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        @if($totalAnakWali > 0)
                                            {{ number_format((($dosen['jumlah_anak_wali'] ?? 0) / $totalAnakWali) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $totalAnakWali }}</span>
                            </td>
                            <td class="text-center">100%</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada data dosen wali
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- History Timeline -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-secondary bg-opacity-10 border-0">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="fas fa-history me-2"></i>Riwayat Proses
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6 class="fw-bold mb-1">Pengajuan Dibuat</h6>
                    <small class="text-muted">
                        {{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d M Y H:i') : '-' }}
                    </small>
                    <p class="mb-0 mt-2">SK Dosen Wali diajukan oleh Kaprodi {{ $sk->prodi->Nama_Prodi ?? '' }}</p>
                </div>
            </div>
            
            @if($sk->Status != 'Dikerjakan admin')
            <div class="timeline-item">
                <div class="timeline-marker bg-{{ $sk->Status == 'Ditolak' ? 'danger' : 'success' }}"></div>
                <div class="timeline-content">
                    <h6 class="fw-bold mb-1">
                        {{ $sk->Status == 'Ditolak' ? 'Ditolak' : 'Diproses Admin Fakultas' }}
                    </h6>
                    <small class="text-muted">-</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-marker {
        position: absolute;
        left: -26px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    
    .timeline-content {
        padding-left: 20px;
    }
</style>
@endpush

@push('scripts')
@endpush

@endsection
