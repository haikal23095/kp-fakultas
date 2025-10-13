@extends('layouts.mahasiswa')

@section('title', 'Permohonan Legalisir Dokumen')

@section('content')
    <h1 class="h3 fw-bold mb-4">Permohonan Legalisir Dokumen</h1>

    <div class="alert alert-info border-0 shadow-sm" role="alert">
        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Prosedur Legalisir</h4>
        <p class="mb-0">Silakan isi formulir di bawah ini, kemudian bawa dokumen asli beserta fotokopinya ke bagian administrasi fakultas untuk diproses. Proses legalisir dapat ditunggu.</p>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Formulir Permohonan Legalisir</h6>
        </div>
        <div class="card-body">
            <form action="#" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="jenis_dokumen" class="form-label">Jenis Dokumen yang Akan Dilegalisir</label>
                    <input type="text" class="form-control" id="jenis_dokumen" name="jenis_dokumen" placeholder="Contoh: Ijazah, Transkrip Nilai" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Lembar Fotokopi</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" placeholder="Contoh: 5" required>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check-circle me-2"></i>Ajukan Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection