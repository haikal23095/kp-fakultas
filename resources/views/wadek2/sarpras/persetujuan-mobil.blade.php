@extends('layouts.wadek2')

@section('title', 'Persetujuan Peminjaman Mobil')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Persetujuan Peminjaman Mobil</h1>
    <p class="text-muted">Kelola persetujuan peminjaman mobil fakultas</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Dr. Ahmad Fauzi</td>
                        <td>Kunjungan Industri</td>
                        <td>05 Jan 2026</td>
                        <td><span class="badge bg-warning">Menunggu</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Setujui</button>
                            <button class="btn btn-sm btn-danger">Tolak</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
