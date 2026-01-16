@extends('layouts.wadek2')

@section('title', 'Validasi Cuti Pegawai')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Cuti Pegawai</h1>
    <p class="text-muted">Validasi pengajuan cuti pegawai fakultas</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ahmad Hidayat, S.Kom</td>
                        <td>Cuti Tahunan</td>
                        <td>15 Jan 2026</td>
                        <td>20 Jan 2026</td>
                        <td><span class="badge bg-warning">Menunggu</span></td>
                        <td>
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
