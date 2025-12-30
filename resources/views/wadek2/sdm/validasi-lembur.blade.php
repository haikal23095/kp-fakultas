@extends('layouts.wadek2')

@section('title', 'Validasi Lembur Pegawai')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Lembur Pegawai</h1>
    <p class="text-muted">Validasi pengajuan lembur pegawai fakultas</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Siti Nurhaliza, S.E.</td>
                        <td>28 Des 2025</td>
                        <td>17:00</td>
                        <td>21:00</td>
                        <td>Persiapan Akreditasi</td>
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
