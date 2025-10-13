<div>
    <!-- The biggest battle is the war against ignorance. - Mustafa Kemal Atatürk -->
</div>
@extends('layouts.mahasiswa')

@section('title', 'Legalisir Online')

@section('content')

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
        <form>
            {{-- Pilihan Jenis Dokumen --}}
            <div class="mb-3">
                <label for="jenisDokumen" class="form-label"><strong>Jenis Dokumen</strong></label>
                <select class="form-select" id="jenisDokumen" required>
                    <option selected disabled value="">-- Pilih dokumen yang akan dilegalisir --</option>
                    <option value="1">Ijazah</option>
                    <option value="2">Transkrip Nilai</option>
                </select>
            </div>

            {{-- Unggah File --}}
            <div class="mb-3">
                <label for="fileDokumen" class="form-label"><strong>Unggah File Dokumen (PDF)</strong></label>
                <input class="form-control" type="file" id="fileDokumen" accept=".pdf" required>
            </div>

            {{-- Jumlah Salinan --}}
            <div class="mb-4">
                <label for="jumlahSalinan" class="form-label"><strong>Jumlah Salinan Legalisir</strong></label>
                <input type="number" class="form-control" id="jumlahSalinan" value="1" min="1" max="10">
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