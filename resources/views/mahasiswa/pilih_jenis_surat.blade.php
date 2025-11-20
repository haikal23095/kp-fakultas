@extends('layouts.mahasiswa')

@section('title', 'Pilih Jenis Surat')

@push('styles')
<style>
    .surat-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        height: 100%;
    }
    
    .surat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        border-color: #4e73df;
    }
    
    .surat-card .card-body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .surat-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2.5rem;
    }
    
    .surat-card h5 {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #2c3e50;
    }
    
    .surat-card p {
        color: #6c757d;
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
    .card-aktif .surat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .card-rekomendasi .surat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .card-magang .surat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
</style>
@endpush

@section('content')

{{-- Menampilkan pesan sukses setelah submit --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Menampilkan pesan error --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <strong>Terjadi Kesalahan:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Pengajuan Surat Baru</h1>
        <p class="text-muted mb-0">Pilih jenis surat yang ingin Anda ajukan</p>
    </div>
</div>

<div class="row">
    @foreach($jenis_surats as $surat)
        <div class="col-lg-4 col-md-6 mb-4">
            @php
                $cardClass = '';
                $icon = '';
                $route = '';
                $description = '';
                
                if($surat->Nama_Surat == 'Surat Keterangan Aktif Kuliah') {
                    $cardClass = 'card-aktif';
                    $icon = 'fa-user-graduate';
                    $route = route('mahasiswa.pengajuan.aktif.form');
                    $description = 'Surat keterangan bahwa Anda masih terdaftar sebagai mahasiswa aktif';
                } elseif($surat->Nama_Surat == 'Surat Rekomendasi') {
                    $cardClass = 'card-rekomendasi';
                    $icon = 'fa-award';
                    $route = route('mahasiswa.pengajuan.rekomendasi.form');
                    $description = 'Surat rekomendasi untuk keperluan beasiswa, lomba, atau keperluan lainnya';
                } elseif($surat->Nama_Surat == 'Surat Pengantar KP/Magang') {
                    $cardClass = 'card-magang';
                    $icon = 'fa-briefcase';
                    $route = route('mahasiswa.pengajuan.magang.form');
                    $description = 'Surat pengantar untuk keperluan Kerja Praktik atau Magang di perusahaan/instansi';
                }
            @endphp
            
            <a href="{{ $route }}" class="text-decoration-none">
                <div class="card surat-card shadow-sm {{ $cardClass }}">
                    <div class="card-body">
                        <div class="surat-icon">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <h5>{{ $surat->Nama_Surat }}</h5>
                        <p>{{ $description }}</p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-1 text-center mb-3 mb-md-0">
                <i class="fas fa-info-circle fa-3x text-primary"></i>
            </div>
            <div class="col-md-11">
                <h5 class="mb-2">Informasi Penting</h5>
                <ul class="mb-0 text-muted">
                    <li>Pastikan semua data yang Anda masukkan sudah benar sebelum mengirim pengajuan</li>
                    <li>Dokumen pendukung harus dalam format PDF dengan ukuran maksimal 2MB</li>
                    <li>Proses verifikasi membutuhkan waktu 3-5 hari kerja</li>
                    <li>Anda akan mendapat notifikasi melalui sistem ketika status surat berubah</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
