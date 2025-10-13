@extends('layouts.kajur')

@section('title', 'Persetujuan Surat Dosen')

@section('content')
    <h1 class="h3 fw-bold mb-4">Persetujuan Surat Tugas Dosen</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Antrian Surat Menunggu Persetujuan Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Dosen Pemohon</th>
                            <th scope="col">Jenis Surat</th>
                            <th scope="col">Tanggal Masuk</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Dr. Anisa Rahmawati, M.T.</td>
                            <td>Surat Tugas Penelitian</td>
                            <td>11 Okt 2025</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i> Setujui
                                </a>
                                <a href="#" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times me-1"></i> Tolak
                                </a>
                            </td>
                        </tr>
                        {{-- Contoh data lain --}}
                        <tr>
                            <th scope="row">2</th>
                            <td>Prof. Budi Hartono, M.Kom.</td>
                            <td>Surat Tugas Pemateri Seminar</td>
                            <td>10 Okt 2025</td>
                             <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i> Setujui
                                </a>
                                <a href="#" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times me-1"></i> Tolak
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection