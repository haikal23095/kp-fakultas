@extends('layouts.admin_fakultas')

@section('title', 'Detail Riwayat SK Penguji Skripsi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail Riwayat SK</h1>
        <p class="mb-0 text-muted">Informasi lengkap SK yang telah diproses</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin_fakultas.sk.penguji-skripsi.history') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        @if($acc->Status == 'Selesai' && $acc->QR_Code)
        <a href="{{ route('admin_fakultas.sk.penguji-skripsi.download', $acc->No) }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0"><i class="fas fa-info-circle me-2"></i>Informasi SK</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="40%">Nomor SK</th>
                        <td>: <strong class="text-danger">{{ $acc->Nomor_Surat ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td>: 
                            <span class="badge bg-{{ $acc->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $acc->Semester }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tahun Akademik</th>
                        <td>: {{ $acc->Tahun_Akademik }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @php
                                $badgeClass = 'secondary';
                                switch($acc->Status) {
                                    case 'Menunggu-Persetujuan-Wadek-1': 
                                    case 'Menunggu-Persetujuan-Dekan': 
                                        $badgeClass = 'primary'; 
                                        break;
                                    case 'Selesai': 
                                        $badgeClass = 'success'; 
                                        break;
                                    case 'Ditolak-Wadek1':
                                    case 'Ditolak-Dekan':
                                        $badgeClass = 'danger'; 
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ str_replace('-', ' ', $acc->Status) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0"><i class="fas fa-user-tie me-2"></i>Informasi Pengajuan SK</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="40%">Dekan</th>
                        <td>: {{ $acc->dekan->Nama_Dosen ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>NIP Dekan</th>
                        <td>: {{ $acc->dekan->NIP ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="pt-3">Kaprodi Pengaju</th>
                        <td class="pt-3">: {{ $sk->kaprodi->Nama_Dosen ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($acc->Status == 'Ditolak' && $acc->Alasan_Tolak)
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <h6 class="fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Alasan Penolakan:</h6>
    <p class="mb-0">{{ $acc->Alasan_Tolak }}</p>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger">
            <i class="fas fa-users me-2"></i>Daftar Mahasiswa dan Penguji
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>Judul Skripsi</th>
                        <th>Dosen Penguji</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($dataPenguji) && count($dataPenguji) > 0)
                        @foreach($dataPenguji as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $data['nama_mahasiswa'] ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $data['nim'] ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 300px;" 
                                      title="{{ $data['judul_skripsi'] ?? '-' }}">
                                    {{ $data['judul_skripsi'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @if(isset($data['nama_penguji_1']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_1'] }}</li>
                                    @endif
                                    @if(isset($data['nama_penguji_2']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_2'] }}</li>
                                    @endif
                                    @if(isset($data['nama_penguji_3']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_3'] }}</li>
                                    @endif
                                    @if(!isset($data['nama_penguji_1']) && !isset($data['nama_penguji_2']) && !isset($data['nama_penguji_3']))
                                        <li class="text-muted">-</li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada data.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
