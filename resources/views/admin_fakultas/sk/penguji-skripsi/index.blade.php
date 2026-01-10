@extends('layouts.admin_fakultas')

@section('title', 'Request SK Penguji Skripsi')

@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #ffffff;
        color: #000;
        border: 1px solid #000;
        padding: 2cm 2.5cm;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 11pt;
        line-height: 1.5;
        min-height: 500px;
        width: 21cm;
        max-width: 100%;
        margin: 0 auto;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
    }
    .preview-header img {
        width: 65px;
        float: left;
        margin-top: -5px;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
        text-align: center;
    }
    .preview-header .line-1 { font-size: 11pt; font-weight: bold; }
    .preview-header .line-2 { font-size: 13pt; font-weight: bold; }
    .preview-header .line-3 { font-size: 11pt; font-weight: bold; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
        font-weight: normal;
    }
    .preview-title {
        font-weight: bold;
        font-size: 12pt;
        margin: 30px 0 10px 0;
        text-align: center;
    }
    .preview-nomor {
        text-align: center;
        font-size: 12pt;
        margin-bottom: 25px;
    }
    .preview-content {
        text-align: justify;
        margin-bottom: 20px;
    }
    .preview-table-mahasiswa {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
        border: 1px solid #000;
    }
    .preview-table-mahasiswa th,
    .preview-table-mahasiswa td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    .preview-table-mahasiswa thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
        text-transform: capitalize;
    }
    .preview-table-mahasiswa tbody td {
        font-size: 9pt;
        vertical-align: top;
    }
    .preview-table-mahasiswa tbody td:nth-child(1) {
        text-align: center;
        vertical-align: top;
    }
    .preview-signature {
        margin-top: 40px;
        text-align: right;
    }
    .preview-placeholder {
        color: #999;
        font-style: italic;
        background-color: #fffacd;
        padding: 2px 4px;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Request SK Penguji Skripsi</h1>
        <p class="mb-0 text-muted">Kelola pengajuan SK Penguji Skripsi dari Kaprodi</p>
    </div>
    <a href="{{ route('admin_fakultas.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Dikerjakan admin">Dikerjakan Admin</option>
                    <option value="Menunggu-Persetujuan-Wadek-1">Menunggu Wadek 1</option>
                    <option value="Menunggu-Persetujuan-Dekan">Menunggu Dekan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Ditolak-Admin">Ditolak Admin</option>
                    <option value="Ditolak-Wadek1">Ditolak Wadek 1</option>
                    <option value="Ditolak-Dekan">Ditolak Dekan</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterSemester">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SK List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger">Daftar SK Penguji Skripsi</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Jumlah Mahasiswa</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        @php
                            $pengujiData = $sk->Data_Penguji_Skripsi;
                            $jumlahMahasiswa = is_array($pengujiData) ? count($pengujiData) : 0;
                        @endphp
                        <td>{{ $skList->firstItem() + $index }}</td>
                        <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $sk->Semester }}
                            </span>
                        </td>
                        <td>{{ $sk->Tahun_Akademik }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                {{ $jumlahMahasiswa }} Mahasiswa
                            </span>
                        </td>
                        <td>
                            {{ isset($sk->{'Tanggal-Pengajuan'}) ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}
                        </td>
                        <td>
                            @php
                                $badgeClass = 'secondary';
                                $statusText = $sk->Status;
                                
                                switch($sk->Status) {
                                    case 'Dikerjakan admin':
                                        $badgeClass = 'warning';
                                        $statusText = 'Dikerjakan Admin';
                                        break;
                                    case 'Menunggu-Persetujuan-Wadek-1':
                                        $badgeClass = 'info';
                                        $statusText = 'Menunggu Wadek 1';
                                        break;
                                    case 'Menunggu-Persetujuan-Dekan':
                                        $badgeClass = 'primary';
                                        $statusText = 'Menunggu Dekan';
                                        break;
                                    case 'Selesai':
                                        $badgeClass = 'success';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'Ditolak-Admin':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Admin';
                                        break;
                                    case 'Ditolak-Wadek1':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Wadek 1';
                                        break;
                                    case 'Ditolak-Dekan':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Dekan';
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin_fakultas.sk.penguji-skripsi.detail', $sk->No) }}" 
                                   class="btn btn-info"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sk->Status == 'Dikerjakan admin' || $sk->Status == 'Ditolak-Wadek1' || $sk->Status == 'Ditolak-Dekan')
                                <button type="button" 
                                        class="btn btn-warning btn-buatkan-surat" 
                                        data-id="{{ $sk->No }}"
                                        data-prodi="{{ $sk->prodi->Nama_Prodi ?? '-' }}"
                                        data-semester="{{ $sk->Semester }}"
                                        data-tahun="{{ $sk->Tahun_Akademik }}"
                                        data-jumlah="{{ $jumlahMahasiswa }}"
                                        title="Buatkan Surat">
                                    <i class="fas fa-file-signature"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-danger btn-tolak-sk" 
                                        data-id="{{ $sk->No }}"
                                        data-prodi="{{ $sk->prodi->Nama_Prodi ?? '-' }}"
                                        data-semester="{{ $sk->Semester }}"
                                        data-tahun="{{ $sk->Tahun_Akademik }}"
                                        title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada request SK Penguji Skripsi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($skList->hasPages())
    <div class="card-footer bg-white">
        {{ $skList->links() }}
    </div>
    @endif
</div>

<!-- Modal Tolak SK -->
<div class="modal fade" id="modalTolakSK" tabindex="-1" aria-labelledby="modalTolakSKLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakSKLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Anda akan menolak pengajuan SK berikut:
                </div>
                
                <div class="mb-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Program Studi</th>
                            <td>: <span id="reject-prodi">-</span></td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>: <span id="reject-semester">-</span></td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>: <span id="reject-tahun">-</span></td>
                        </tr>
                    </table>
                </div>

                <form id="formTolakSK">
                    <input type="hidden" id="reject-sk-id" name="sk_id">
                    
                    <div class="mb-3">
                        <label for="reject-alasan" class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="reject-alasan" 
                                  name="alasan" 
                                  rows="4" 
                                  placeholder="Masukkan alasan penolakan secara detail..."
                                  required></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Alasan ini akan dikirimkan sebagai notifikasi ke Kaprodi
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-ban me-1"></i>Tolak SK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buatkan Surat -->
<div class="modal fade" id="modalBuatkanSurat" tabindex="-1" aria-labelledby="modalBuatkanSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalBuatkanSuratLabel">
                    <i class="fas fa-file-signature me-2"></i>Buatkan Surat SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: SK Info & Input -->
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informasi SK
                        </h6>
                        
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th width="45%">Program Studi</th>
                                        <td>: <span id="info-prodi">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>Semester</th>
                                        <td>: <span id="info-semester">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Akademik</th>
                                        <td>: <span id="info-tahun">-</span></td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Mahasiswa</th>
                                        <td>: <span id="info-jumlah">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <input type="hidden" id="current-sk-id">
                        
                        <div class="mb-3">
                            <label for="nomorSurat" class="form-label fw-bold">
                                Nomor Surat <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nomorSurat" 
                                   placeholder="Contoh: 4 /10245.3.4 /Hk.04 /2024" required>
                            <div class="form-text">
                                Format: Nomor/Kode/Jenis/Tahun
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tahun-akademik" class="form-label fw-bold">
                                Tahun Akademik <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tahun-akademik" 
                                   placeholder="Contoh: 2023/2024" required>
                            <div class="form-text">
                                Format: YYYY/YYYY
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Document Preview -->
                    <div class="col-md-8 border-start">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-file-alt me-2"></i>Preview Surat
                        </h6>
                        
                        <div class="preview-container bg-secondary bg-opacity-10 p-4 rounded overflow-auto" style="max-height: 750px;">
                            <div class="preview-document" id="documentPreview">
                                <!-- Header with Logo -->
                                <div class="preview-header">
                                    <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
                                    <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
                                    <strong class="line-1">RISET DAN TEKNOLOGI</strong>
                                    <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
                                    <strong class="line-3">FAKULTAS TEKNIK</strong>
                                    <div class="address">
                                        Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                                        Telp: (031) 3011146, Fax. (031) 3011506<br>
                                        Laman: www.trunojoyo.ac.id
                                    </div>
                                </div>
                                
                                <div class="preview-title">
                                    KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNOJOYO MADURA
                                </div>
                                <div class="preview-nomor">
                                    NOMOR: <span id="preview-nomor-surat" class="preview-placeholder">[Nomor Surat]</span>
                                </div>
                                
                                <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
                                    TENTANG<br><br>
                                    PENETAPAN DOSEN PENGUJI UJIAN SKRIPSI<br>
                                    PROGRAM STUDI <span id="preview-prodi-text" style="text-transform: uppercase;"></span> FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNOJOYO MADURA SEMESTER <span id="preview-semester-text" style="text-transform: uppercase;"></span> TAHUN AKADEMIK <span id="preview-tahun-text" class="preview-placeholder"></span>
                                </div>
                                
                                <div style="text-align: center; font-weight: bold; margin-bottom: 30px;">
                                    DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA
                                </div>

                                <!-- Menimbang Section -->
                                <div class="preview-content">
                                    <table style="width: 100%; border: none;">
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none;">Menimbang</td>
                                            <td style="width: 20px; vertical-align: top; border: none;">:</td>
                                            <td style="text-align: justify; border: none;">
                                                <table style="border: none; width: 100%;">
                                                    <tr>
                                                        <td style="width: 20px; vertical-align: top; border: none;">a.</td>
                                                        <td style="text-align: justify; border: none;">Bahwa untuk memperlancar pelaksanaan Ujian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Ujian Skripsi;</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20px; vertical-align: top; border: none; padding-top: 8px;">b.</td>
                                                        <td style="text-align: justify; border: none; padding-top: 8px;">Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Mengingat Section -->
                                <div class="preview-content">
                                    <table style="width: 100%; border: none;">
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none;">Mengingat</td>
                                            <td style="width: 20px; vertical-align: top; border: none;">:</td>
                                            <td style="border: none;">
                                                <ol style="margin: 0; padding-left: 20px;">
                                                    <li style="margin-bottom: 5px; text-align: justify;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Keputusan RI Nomor 85 tahun 2001, tentang Statuta Universitas Trunojoyo Madura;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/ U/ 2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi RI Nomor 79/M/MPK.A/ KP.09.02/ 2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                                                    <li style="margin-bottom: 5px; text-align: justify;">Keputusan Rektor Universitas Trunojoyo Madura Nomor 1357/UNM3/KP/ 2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik, Universitas Trunojoyo Madura;</li>
                                                </ol>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- Memperhatikan section --}}
                                <div class="preview-content">
                                    <table style="width: 100%; border: none;">
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none;">Memperhatikan</td>
                                            <td style="width: 20px; vertical-align: top; border: none;">:</td>
                                            <td style="text-align: justify; border: none;">
                                                <table style="border: none; width: 100%;">
                                                    <tr>
                                                        <td style="width: 20px; vertical-align: top; border: none;">Surat dari Ketua Jurusan <span id="preview-jurusan-text"></span> tentang permohonan SK Dosen Penguji Ujian Skripsi;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Memutuskan Section -->
                                <div style="text-align: center; font-weight: bold; margin: 30px 0 20px 0;">
                                    MEMUTUSKAN
                                </div>

                                <div class="preview-content">
                                    <table style="width: 100%; border: none;">
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none;">Menetapkan</td>
                                            <td style="width: 20px; vertical-align: top; border: none;">:</td>
                                            <td style="text-align: justify; border: none;">
                                                KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA TENTANG PENETAPAN DOSEN PENGUJI UJIAN SKRIPSI PROGRAM STUDI S1 <span id="preview-prodi-menetapkan" style="text-transform: uppercase;"></span> FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER <span id="preview-semester-menetapkan" style="text-transform: uppercase;"></span> TAHUN AKADEMIK <span id="preview-tahun-menetapkan"></span>.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none; padding-top: 10px;">Kesatu</td>
                                            <td style="width: 20px; vertical-align: top; border: none; padding-top: 10px;">:</td>
                                            <td style="text-align: justify; border: none; padding-top: 10px;">
                                                Dosen Penguji Ujian Skripsi Program Studi S1 <span id="preview-prodi-kesatu" style="text-transform: capitalize;"></span> Fakultas Teknik Universitas Trunojoyo Madura semester <span id="preview-semester-kesatu"></span> Tahun Akademik <span id="preview-tahun-kesatu"></span> sebagaimana tercantum dalam lampiran Keputusan ini;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none; padding-top: 10px;">Kedua</td>
                                            <td style="width: 20px; vertical-align: top; border: none; padding-top: 10px;">:</td>
                                            <td style="text-align: justify; border: none; padding-top: 10px;">
                                                Keputusan ini berlaku sejak tanggal ditetapkan.
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="preview-signature">
                                    <div style="display: inline-block; text-align: center; margin-top: 30px;">
                                        <div>Ditetapkan di : Bangkalan</div>
                                        <div>Pada tanggal : {{ now()->translatedFormat('d F Y') }}</div>
                                        <div style="margin-top: 10px; font-weight: bold;">Dekan,</div>
                                        <div style="margin-top: 80px; font-weight: bold;">
                                            {{ $dekanName }}
                                        </div>
                                        <div>
                                            NIP. {{ $dekanNip }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Container untuk halaman lampiran -->
                                <div id="lampiran-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="submitToWadek()" id="btnSubmitWadek">
                    <i class="fas fa-paper-plane me-1"></i>Ajukan SK ke Wadek1
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentSKData = null;

    $(document).ready(function() {
        // Event delegation for "Buatkan Surat" button
        $(document).on('click', '.btn-buatkan-surat', function() {
            const btn = $(this);
            const id = btn.data('id');
            const prodi = btn.data('prodi');
            const semester = btn.data('semester');
            const tahun = btn.data('tahun');
            const jumlah = btn.data('jumlah');

            showBuatkanSuratModal(id, prodi, semester, tahun, jumlah);
        });

        // Event delegation for "Tolak" button
        $(document).on('click', '.btn-tolak-sk', function() {
            const btn = $(this);
            const id = btn.data('id');
            const prodi = btn.data('prodi');
            const semester = btn.data('semester');
            const tahun = btn.data('tahun');

            showRejectModal(id, prodi, semester, tahun);
        });

        // Event listener for nomor surat input
        $('#nomorSurat').on('input', function() {
            const val = $(this).val();
            
            // Update Page 1
            if (val) {
                $('#preview-nomor-surat').text(val).removeClass('preview-placeholder');
                $('.preview-nomor-surat-lampiran').text(val);
            } else {
                $('#preview-nomor-surat').text('[Nomor Surat]').addClass('preview-placeholder');
                $('.preview-nomor-surat-lampiran').html('<span class="preview-placeholder">[Nomor Surat]</span>');
            }
        });

        // Event listener for tahun akademik input
        $('#tahun-akademik').on('input', function() {
            const val = $(this).val();
            const tahunText2 = document.getElementById('preview-tahun-text2');
            const tahunPlaceholders = document.querySelectorAll('.preview-tahun-akademik-lampiran');
            const mainTahunPlaceholders = ['#preview-tahun-text', '#preview-tahun-menetapkan', '#preview-tahun-kesatu'];

            if (val) {
                mainTahunPlaceholders.forEach(selector => {
                    $(selector).text(val).removeClass('preview-placeholder');
                });
                if(tahunText2) $(tahunText2).text(val);
                
                tahunPlaceholders.forEach(el => {
                    el.textContent = val;
                });
                $('#preview-tahun-text2').text(val);
            } else {
                const defaultVal = currentSKData ? currentSKData.tahun : '..../....';
                mainTahunPlaceholders.forEach(selector => {
                    $(selector).text(defaultVal);
                });
                $('#preview-tahun-text2').text(defaultVal);
                if(tahunText2) $(tahunText2).text(defaultVal);

                tahunPlaceholders.forEach(el => {
                    el.textContent = defaultVal;
                });
            }
        });
    });

    function applyFilters() {
        const status = $('#filterStatus').val();
        const semester = $('#filterSemester').val();
        
        let url = new URL(window.location.href);
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        
        window.location.href = url.toString();
    }

    function showRejectModal(id, prodi, semester, tahun) {
        $('#reject-sk-id').val(id);
        $('#reject-prodi').text(prodi);
        $('#reject-semester').text(semester);
        $('#reject-tahun').text(tahun);
        $('#reject-alasan').val('');
        
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }

    function submitRejection() {
        const sk_id = $('#reject-sk-id').val();
        const alasan = $('#reject-alasan').val().trim();
        
        if (!alasan) {
            Swal.fire('Error', 'Alasan penolakan harus diisi', 'error');
            return;
        }

        if (alasan.length < 10) {
            Swal.fire('Error', 'Alasan penolakan minimal 10 karakter', 'error');
            return;
        }

        // Disable button to prevent double click
        const btnTolak = $('.btn-danger[onclick="submitRejection()"]');
        btnTolak.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Memproses...');

        $.ajax({
            url: '{{ route("admin_fakultas.sk.penguji-skripsi.reject") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sk_id: sk_id,
                alasan: alasan
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message || 'SK berhasil ditolak', 'success').then(() => {
                        $('#modalTolakSK').modal('hide');
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                    btnTolak.prop('disabled', false).html('<i class="fas fa-ban me-1"></i>Tolak SK');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat memproses data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const err = JSON.parse(xhr.responseText);
                        errorMsg = err.message || errorMsg;
                    } catch (e) {
                        console.error('Parse error:', e);
                    }
                }
                Swal.fire('Error', errorMsg, 'error');
                btnTolak.prop('disabled', false).html('<i class="fas fa-ban me-1"></i>Tolak SK');
            }
        });
    }

    function showBuatkanSuratModal(id, prodi, semester, tahun, jumlah) {
        // Init Global Data
        currentSKData = {
            id: id,
            prodi: prodi,
            semester: semester,
            tahun: tahun
        };

        $('#current-sk-id').val(id);
        $('#info-prodi').text(prodi);
        $('#info-semester').text(semester);
        $('#info-tahun').text(tahun);
        $('#info-jumlah').text(jumlah + ' Mahasiswa');
        
        // Reset Inputs
        $('#nomorSurat').val('');
        $('#tahun-akademik').val(tahun);

        // Preview text
        const prodiUpper = prodi.toUpperCase();
        const semesterUpper = semester.toUpperCase();
        const semesterCap = semester.charAt(0).toUpperCase() + semester.slice(1).toLowerCase();

        // Jurusan/Prodi
        $('#preview-prodi-text').text(prodiUpper);
        $('#preview-jurusan-text').text(prodi); 
        $('#preview-prodi-menetapkan').text(prodiUpper);
        $('#preview-prodi-kesatu').text(prodi);
        
        // Semester
        $('#preview-semester-text').text(semesterUpper);
        $('#preview-semester-text2').text(semesterCap);
        $('#preview-semester-menetapkan').text(semesterUpper);
        $('#preview-semester-kesatu').text(semesterCap);

        // Tahun
        $('#preview-tahun-text').text(tahun);
        $('#preview-tahun-text2').text(tahun);
        $('#preview-tahun-menetapkan').text(tahun);
        $('#preview-tahun-kesatu').text(tahun);
        
        // Reset Lampiran
        const lampiranContainer = document.getElementById('lampiran-container');
        if (lampiranContainer) {
            lampiranContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Memuat data lampiran...</div></div>';
        }
        
        $.ajax({
            url: '{{ route("admin_fakultas.sk.penguji-skripsi.get-details") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sk_id: id
            },
            success: function(response) {
                if (response.success) {
                    if (response.data_penguji && response.data_penguji.length > 0) {
                        updatePreviewTable(response.data_penguji);
                    } else {
                        $('#lampiran-container').html('<div class="text-center text-danger py-5">Tidak ada data mahasiswa ditemukan.</div>');
                    }
                } else {
                    $('#lampiran-container').html('<div class="text-center text-danger py-5">Gagal memuat data lampiran.</div>');
                }
            },
            error: function() {
                 $('#lampiran-container').html('<div class="text-center text-danger py-5">Terjadi kesalahan sistem.</div>');
            }
        });

        const modal = new bootstrap.Modal(document.getElementById('modalBuatkanSurat'));
        modal.show();
    }

    function updatePreviewTable(dataMahasiswa) {
        const lampiranContainer = document.getElementById('lampiran-container');
        if (!lampiranContainer) return;

        const nomorSuratInput = document.getElementById('nomorSurat');
        const nomorSuratValue = nomorSuratInput && nomorSuratInput.value.trim();
        const nomorSuratHtml = nomorSuratValue
            ? nomorSuratValue
            : '<span class="preview-placeholder">[Nomor Surat]</span>';

        // Gunakan info dari currentSKData
        const prodiName = currentSKData.prodi || '-';
        const semesterUpper = (currentSKData.semester || '-').toUpperCase();
        
        // Get tahun from input or data
        const tahunInput = $('#tahun-akademik').val();
        const tahunAkademikText = tahunInput || currentSKData.tahun || '-';
        
        // Generate Table Rows
        let tableRows = '';
        dataMahasiswa.forEach((item, index) => {
             let pengujiContent = '<ul style="list-style:none; padding-left:0; margin:0;">';
             
             // Ketua
             if(item.nama_penguji_1) {
                pengujiContent += `<li style="margin-bottom:4px;">1. ${item.nama_penguji_1} (Ketua)</li>`;
             }
             // Anggota 1
             if(item.nama_penguji_2) {
                pengujiContent += `<li style="margin-bottom:4px;">2. ${item.nama_penguji_2} (Anggota)</li>`;
             }
             // Anggota 2
             if(item.nama_penguji_3) {
                pengujiContent += `<li>3. ${item.nama_penguji_3} (Anggota)</li>`;
             }
             pengujiContent += '</ul>';

             tableRows += `
                <tr>
                    <td style="text-align: center;">${index + 1}</td>
                    <td>
                        <strong>${item.nama_mahasiswa || '-'}</strong><br>
                        NIM. ${item.nim || '-'}
                    </td>
                    <td>${item.judul_skripsi || '-'}</td>
                    <td>${pengujiContent}</td>
                </tr>
             `;
        });

        let lampiranHtml = `
            <div class="lampiran-prodi" style="margin-top: 60px; page-break-before: always; border-top: 2px dashed #ccc; padding-top: 40px;">
                <div style="font-size: 11pt; text-align: left; margin-bottom: 20px;">
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR <span class="preview-nomor-surat-lampiran">${nomorSuratHtml}</span></p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PENGUJI UJIAN SKRIPSI PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK <span class="preview-tahun-akademik-lampiran">${tahunAkademikText}</span></p>

                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PENGUJI UJIAN SKRIPSI</p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK</p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNOJOYO MADURA</p>
                    <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK <span class="preview-tahun-akademik-lampiran">${tahunAkademikText}</span></p>
                </div>

                <table class="preview-table-mahasiswa">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Mahasiswa / NIM</th>
                            <th width="30%">Judul Skripsi</th>
                            <th width="40%">Dosen Penguji</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
                <div style="margin-top: 40px; font-size: 11pt;">
                    <div style="text-align: right;">
                        Bangkalan, {{ now()->translatedFormat('d F Y') }}<br>
                        Dekan,<br>
                        <div style="height: 60px;"></div>
                        <strong>{{ $dekanName }}</strong><br>
                        NIP. {{ $dekanNip }}
                    </div>
                </div>
            </div>
        `;

        lampiranContainer.innerHTML = lampiranHtml;
    }

    function submitToWadek() {
        const sk_id = $('#current-sk-id').val();
        const nomor_surat = $('#nomorSurat').val();
        const tahun_akademik = $('#tahun-akademik').val();
        
        if (!nomor_surat) {
            Swal.fire('Error', 'Nomor surat harus diisi', 'error');
            return;
        }

        if (!tahun_akademik) {
            Swal.fire('Error', 'Tahun Akademik harus diisi', 'error');
            return;
        }

        $('#btnSubmitWadek').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
        
        $.ajax({
            url: '{{ route("admin_fakultas.sk.penguji-skripsi.submit-wadek") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sk_id: sk_id,
                nomor_surat: nomor_surat,
                tahun_akademik: tahun_akademik
            },
            success: function(response) {
                if (response.success) {
                     Swal.fire('Berhasil', response.message || 'SK berhasil diajukan', 'success').then(() => {
                         window.location.reload();
                     });
                } else {
                     Swal.fire('Error', response.message || 'Gagal mengajukan SK', 'error');
                     $('#btnSubmitWadek').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Teruskan ke Dekan');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                $('#btnSubmitWadek').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Teruskan ke Wadek1');
            }
        });
    }
</script>
@endpush
