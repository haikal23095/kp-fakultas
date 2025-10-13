@extends('layouts.dekan')

@section('title', 'Persetujuan Tanda Tangan Elektronik')

@section('content')
    <h1 class="h3 fw-bold mb-4">Persetujuan TTE Surat</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Antrian Surat Menunggu Tanda Tangan Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Jenis Surat</th>
                            <th scope="col">Pemohon</th>
                            <th scope="col">Tanggal Diajukan</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Surat Tugas Dosen</td>
                            <td>Dr. Anisa Rahmawati, M.T.</td>
                            <td>11 Okt 2025</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-1"></i> Lihat Draf
                                </a>
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-signature me-1"></i> Setujui (TTE)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Surat Keterangan Aktif Kuliah</td>
                            <td>Budi Santoso (Mahasiswa)</td>
                            <td>10 Okt 2025</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-1"></i> Lihat Draf
                                </a>
                                <a href="#" class="btn btn-success btn-sm">
                                    <i class="fas fa-signature me-1"></i> Setujui (TTE)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection