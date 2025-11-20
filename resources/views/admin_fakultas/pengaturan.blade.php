@extends('layouts.admin_fakultas')

@section('title', 'Pengaturan Sistem')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Pengaturan Sistem</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Umum</h6>
    </div>
    <div class="card-body">
        <form>
            <div class="mb-3 row">
                <label for="namaFakultas" class="col-sm-3 col-form-label">Nama Fakultas</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="namaFakultas" value="Fakultas Ilmu Komputer">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="tahunAkademik" class="col-sm-3 col-form-label">Tahun Akademik Aktif</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="tahunAkademik" value="2025/2026 Ganjil">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="emailResmi" class="col-sm-3 col-form-label">Email Resmi Fakultas</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="emailResmi" value="dekanat.filkom@example.ac.id">
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection