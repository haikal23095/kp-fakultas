@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Legalisir Online')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Legalisir Online</h1>
    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan Legalisir</h6>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('mahasiswa.pengajuan.legalisir.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                <select class="form-select" id="jenis_dokumen" name="jenis_dokumen" required>
                    <option value="" selected disabled>Pilih Jenis Dokumen</option>
                    <option value="Ijazah">Ijazah</option>
                    <option value="Transkrip">Transkrip Nilai</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="jumlah_salinan" class="form-label">Jumlah Salinan (Lembar)</label>
                <input type="number" class="form-control" id="jumlah_salinan" name="jumlah_salinan" min="1" max="10" required>
                <div class="form-text">Maksimal 10 lembar per pengajuan. Biaya Rp 5.000 per lembar.</div>
            </div>

            <div class="mb-3">
                <label for="file_scan" class="form-label">Upload File Scan Dokumen (PDF) <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="file_scan" name="file_scan" accept="application/pdf" required>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Upload file scan <strong>Ijazah/Transkrip</strong> dalam format PDF. Pastikan scan jelas dan terbaca.
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Estimasi Biaya:</strong> <span id="estimasi_biaya">Rp 0</span>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Ajukan Legalisir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('jumlah_salinan').addEventListener('input', function() {
        let jumlah = this.value;
        let biaya = jumlah * 5000;
        document.getElementById('estimasi_biaya').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(biaya);
    });
</script>
@endpush
