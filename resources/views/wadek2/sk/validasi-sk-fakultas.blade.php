@extends('layouts.wadek2')

@section('title', 'Validasi SK Fakultas')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Surat Keputusan Fakultas</h1>
    <p class="text-muted">Validasi dan persetujuan SK tingkat fakultas</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor SK</th>
                        <th>Perihal</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>SK/001/FT/2026</td>
                        <td>Pengangkatan Ketua Program Studi</td>
                        <td>02 Jan 2026</td>
                        <td><span class="badge bg-warning">Menunggu Validasi</span></td>
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
