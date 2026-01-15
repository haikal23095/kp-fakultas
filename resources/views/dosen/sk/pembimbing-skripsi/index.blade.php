@extends('layouts.dosen')

@section('title', 'SK Pembimbing Skripsi')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.dosen') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.sk.index') }}">SK Dosen</a></li>
            <li class="breadcrumb-item active">SK Pembimbing Skripsi</li>
        </ol>
    </nav>
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

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-book-reader fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">SK Pembimbing Skripsi Saya</h5>
                            <small>Daftar SK pembimbing skripsi yang melibatkan Anda</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if($filteredSK->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Program Studi</th>
                                <th width="15%">Nomor SK</th>
                                <th width="10%">Semester</th>
                                <th width="12%">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="15%">Tanggal SK</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredSK as $index => $sk)
                            @php
                                $pembimbingData = is_string($sk->Data_Pembimbing_Skripsi) 
                                    ? json_decode($sk->Data_Pembimbing_Skripsi, true) 
                                    : $sk->Data_Pembimbing_Skripsi;
                                $jumlahMahasiswa = is_array($pembimbingData) ? count($pembimbingData) : 0;
                                
                                // Filter mahasiswa yang dibimbing oleh dosen ini
                                $myMahasiswa = collect($pembimbingData)->filter(function($mhs) use ($dosen) {
                                    $isPembimbing1 = isset($mhs['pembimbing_1']['nama_dosen']) && 
                                        stripos($mhs['pembimbing_1']['nama_dosen'], $dosen->Nama_Dosen) !== false;
                                    $isPembimbing2 = isset($mhs['pembimbing_2']['nama_dosen']) && 
                                        stripos($mhs['pembimbing_2']['nama_dosen'], $dosen->Nama_Dosen) !== false;
                                    return $isPembimbing1 || $isPembimbing2;
                                })->count();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->accSKPembimbingSkripsi && $sk->accSKPembimbingSkripsi->Nomor_Surat)
                                        <strong class="text-warning">{{ $sk->accSKPembimbingSkripsi->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'info' : 'warning' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $myMahasiswa }} dari {{ $jumlahMahasiswa }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    {{ $sk->accSKPembimbingSkripsi ? \Carbon\Carbon::parse($sk->accSKPembimbingSkripsi->Tanggal_Persetujuan_Dekan)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('dosen.sk.pembimbing-skripsi.download', $sk->No) }}" class="btn btn-sm btn-outline-warning" target="_blank" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada SK Pembimbing Skripsi</h5>
                    <p class="text-muted">Anda belum terdaftar dalam SK pembimbing skripsi yang telah disetujui</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
