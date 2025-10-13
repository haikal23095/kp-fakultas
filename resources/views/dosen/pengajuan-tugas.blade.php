@extends('layouts.dosen')

@section('title', 'Pengajuan Surat Tugas')

@section('content')
    <h1 class="h3 fw-bold mb-4">Formulir Pengajuan Surat Tugas</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Isi Detail Keperluan Surat</h6>
        </div>
        <div class="card-body">
            <form action="#" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="jenis_surat" class="form-label">Jenis Surat Tugas</label>
                    <select class="form-select" id="jenis_surat" required>
                        <option selected disabled value="">Pilih jenis surat...</option>
                        <option>Surat Tugas Penelitian</option>
                        <option>Surat Tugas Pemateri Seminar</option>
                        <option>Surat Tugas Pengabdian Masyarakat</option>
                        <option>Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keperluan" class="form-label">Deskripsi Keperluan</label>
                    <textarea class="form-control" id="keperluan" rows="4" placeholder="Contoh: Sebagai pemateri dalam seminar nasional 'AI di Dunia Pendidikan' yang diselenggarakan oleh Universitas Gadjah Mada." required></textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
@endsection