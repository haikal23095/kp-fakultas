@extends('layouts.wadek3')

@section('title', 'Validasi Dispensasi Mahasiswa')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Dispensasi Mahasiswa</h1>
    <p class="text-muted">Validasi pengajuan dispensasi kegiatan mahasiswa</p>
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
                        <th>Keperluan</th>
                        <th>Tanggal Kegiatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>210101001</td>
                        <td>Rizki Pratama</td>
                        <td>Lomba Nasional Pemrograman</td>
                        <td>08-10 Jan 2026</td>
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
