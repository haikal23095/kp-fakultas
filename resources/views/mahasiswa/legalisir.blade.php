<div>
    <!-- The biggest battle is the war against ignorance. - Mustafa Kemal Atatürk -->
</div>
@extends('layouts.mahasiswa')

@section('title', 'Legalisir Online')

@section('content')

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Legalisir Online</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan Legalisir</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Perhatian!</h5>
            <p>Pastikan file yang Anda unggah adalah hasil scan dokumen asli dengan kualitas yang baik dan jelas terbaca. Ukuran file tidak boleh melebihi 5MB.</p>
        </div>
        <form method="POST" action="{{ route('mahasiswa.pengajuan.legalisir.store') }}" enctype="multipart/form-data">
            @csrf
            {{-- Pilihan Jenis Dokumen --}}
            <div class="mb-3">
                <label for="jenisDokumen" class="form-label"><strong>Jenis Dokumen</strong></label>
                <select class="form-select" id="jenisDokumen" name="jenis_dokumen" required>
                    <option selected disabled value="">-- Pilih dokumen yang akan dilegalisir --</option>
                    <option value="Ijazah">Ijazah</option>
                    <option value="Transkrip">Transkrip Nilai</option>
                </select>
            </div>

            {{-- Jumlah Salinan --}}
            <div class="mb-4">
                <label for="jumlahSalinan" class="form-label"><strong>Jumlah Salinan Legalisir</strong></label>
                <input type="number" class="form-control" id="jumlahSalinan" name="jumlah_salinan" value="1" min="1" max="10" required>
            </div>
            
            {{-- Info: Berkas Fisik Dibawa Langsung --}}
            <div class="alert alert-warning" role="alert">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Pengajuan Tanpa Berkas</h6>
                <p class="mb-0">Anda tidak perlu mengunggah file. Berkas fisik (asli) akan dibawa langsung ke Admin Fakultas untuk diproses.</p>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle me-2"></i>Ajukan Legalisir
                </button>
            </div>
        </form>
    </div>
</div>

@endsection