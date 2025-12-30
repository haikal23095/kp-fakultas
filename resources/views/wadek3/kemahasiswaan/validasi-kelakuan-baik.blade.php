@extends('layouts.wadek3')

@section('title', 'Validasi Surat Kelakuan Baik')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Surat Kelakuan Baik</h1>
    <p class="text-muted">Validasi dan persetujuan surat kelakuan baik mahasiswa</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                        <th>Keperluan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>210102015</td>
                        <td>Dewi Sartika</td>
                        <td>Teknik Informatika</td>
                        <td>Melamar Pekerjaan</td>
                        <td>29 Des 2025</td>
                        <td><span class="badge bg-warning">Menunggu</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary">Detail</button>
                            <button class="btn btn-sm btn-success">Validasi</button>
                            <button class="btn btn-sm btn-danger">Tolak</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
