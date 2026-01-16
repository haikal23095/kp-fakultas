@extends('layouts.wadek2')

@section('title', 'Persetujuan Peminjaman Ruang')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Persetujuan Peminjaman Ruang</h1>
    <p class="text-muted">Kelola persetujuan peminjaman ruang fakultas</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Ruang</th>
                        <th>Keperluan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Himpunan Mahasiswa TI</td>
                        <td>Aula Lt.3</td>
                        <td>Seminar Teknologi</td>
                        <td>10 Jan 2026</td>
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
