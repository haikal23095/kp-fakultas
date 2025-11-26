@extends('layouts.admin_fakultas')

@section('title', 'Surat Magang - Dikerjakan Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Surat Pengantar KP/Magang</h1>
        <p class="mb-0 text-muted">Daftar surat yang menunggu penomoran</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Surat dengan Status: Dikerjakan-admin</h6>
        <span class="badge bg-primary">{{ $daftarSurat->count() }} Surat</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tgl. Pengajuan</th>
                        <th width="20%">Mahasiswa Pengaju</th>
                        <th width="15%">NIM</th>
                        <th width="20%">Instansi</th>
                        <th width="13%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSurat as $index => $surat)
                    @php
                        $mahasiswa = $surat->tugasSurat?->pemberiTugas?->mahasiswa;
                        $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                        $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                        $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $namaMahasiswa }}</div>
                            <small class="text-muted">{{ $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $nimMahasiswa }}</td>
                        <td>{{ $surat->Nama_Instansi ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary">
                                <i class="fas fa-file-alt me-1"></i> Dikerjakan Admin
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.surat_magang.show', $surat->id_no) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada surat yang menunggu penomoran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
