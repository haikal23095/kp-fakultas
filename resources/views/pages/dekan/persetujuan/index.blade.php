@extends('layouts.app')

@section('title', 'Persetujuan Surat')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Persetujuan Tanda Tangan Elektronik (TTE)</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Surat Menunggu Persetujuan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>No. Surat</th>
                            <th>Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Tgl. Masuk</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Contoh data surat --}}
                        <tr>
                            <td>123/UN37.FAS/KM/2025</td>
                            <td>Ahmad Budiman (Mahasiswa)</td>
                            <td>Surat Aktif Kuliah</td>
                            <td>10 Okt 2025</td>
                            <td><span class="badge bg-warning text-dark">Menunggu TTE</span></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-signature me-1"></i> Lihat & TTE
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>124/UN37.FAS/ST/2025</td>
                            <td>Dr. Siti Aminah, M.Pd.</td>
                            <td>Surat Tugas Dosen</td>
                            <td>09 Okt 2025</td>
                            <td><span class="badge bg-danger">Urgent</span></td>
                            <td class="text-center">
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-signature me-1"></i> Lihat & TTE
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection