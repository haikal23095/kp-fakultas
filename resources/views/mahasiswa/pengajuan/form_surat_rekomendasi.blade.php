@extends('layouts.mahasiswa')

@section('title', 'Form Surat Rekomendasi')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Form Surat Rekomendasi</h1>
        <p class="text-muted mb-0">Isi formulir di bawah untuk mengajukan surat rekomendasi</p>
    </div>
    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6>
    </div>
    <div class="card-body">
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Coming Soon!</strong> Form surat rekomendasi sedang dalam tahap pengembangan.
        </div>

        <h5 class="mb-3">Data Mahasiswa</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>NPM/NIM</strong></label>
                <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Program Studi</strong></label>
                <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Prodi Tidak Ditemukan' }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><strong>Jurusan</strong></label>
                <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" readonly>
            </div>
        </div>

        <hr>
        <div class="d-flex justify-content-end">
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Pilihan Surat
            </a>
        </div>
    </div>
</div>

@endsection
