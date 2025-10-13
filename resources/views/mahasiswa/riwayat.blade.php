<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
</div>
@extends('layouts.mahasiswa')

@section('title', 'Riwayat Pengajuan Surat')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Pengajuan Surat</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Saya</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Surat</th>
                        <th>Keperluan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10 Okt 2025</td>
                        <td>Surat Rekomendasi Beasiswa</td>
                        <td>Pengajuan beasiswa Bank Indonesia...</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Proses Validasi Dosen</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm" title="Lacak Detail"><i class="fas fa-search"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>05 Sep 2025</td>
                        <td>Surat Izin Penelitian</td>
                        <td>Penelitian untuk skripsi di PT...</td>
                        <td class="text-center"><span class="badge bg-success">Selesai & Disetujui</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-sm" title="Unduh Surat"><i class="fas fa-download"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>01 Agu 2025</td>
                        <td>Surat Keterangan Aktif Kuliah</td>
                        <td>Keperluan administrasi orang tua...</td>
                        <td class="text-center"><span class="badge bg-danger">Ditolak</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-secondary btn-sm" title="Lihat Alasan Penolakan"><i class="fas fa-info-circle"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection