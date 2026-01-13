@extends('layouts.admin_fakultas')

@section('title', 'Request SK Pembimbing Skripsi')

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
        position: relative;
        padding-left: 90px;
    }
    .preview-header img {
        width: 80px;
        position: absolute;
        left: 0;
        top: 0;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
        text-align: center;
    }
    .preview-header .line-1 { font-size: 14pt; font-weight: bold; }
    .preview-header .line-2 { font-size: 16pt; font-weight: bold; }
    .preview-header .line-3 { font-size: 14pt; font-weight: bold; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
        font-weight: normal;
    }
    .preview-title {
        font-weight: bold;
        font-size: 14pt;
        margin: 30px 0 10px 0;
        text-align: center;
        text-decoration: underline;
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
        <h1 class="h3 fw-bold mb-0">Request SK Pembimbing Skripsi</h1>
        <p class="mb-0 text-muted">Kelola pengajuan SK Pembimbing Skripsi dari Kaprodi</p>
    </div>
    <a href="{{ route('admin_fakultas.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <select class="form-select" id="filterSemester">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="col-md-6">
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
        <h6 class="m-0 fw-bold text-warning">Daftar SK Pembimbing Skripsi</h6>
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
                            $pembimbingData = $sk->Data_Pembimbing_Skripsi;
                            // Handle double-encoded JSON (old data)
                            if (is_string($pembimbingData)) {
                                $pembimbingData = json_decode($pembimbingData, true);
                            }
                            $jumlahMahasiswa = is_array($pembimbingData) ? count($pembimbingData) : 0;
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
                            @php
                                $tanggalPengajuan = $sk->{'Tanggal-Pengajuan'};
                            @endphp
                            {{ $tanggalPengajuan ? $tanggalPengajuan->format('d M Y H:i') : '-' }}
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
                                <a href="{{ route('admin_fakultas.sk.pembimbing-skripsi.detail', $sk->No) }}" 
                                   class="btn btn-info"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sk->Status == 'Dikerjakan admin' || $sk->Status == 'Ditolak-Wadek1' || $sk->Status == 'Ditolak-Dekan')
                                <button type="button" 
                                        class="btn btn-warning" 
                                        onclick="showBuatkanSuratModal({{ $sk->No }}, '{{ $sk->prodi->Nama_Prodi ?? '-' }}', '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}', {{ $jumlahMahasiswa }})"
                                        title="Buatkan Surat">
                                    <i class="fas fa-file-signature"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="showRejectModal({{ $sk->No }}, '{{ $sk->prodi->Nama_Prodi ?? '-' }}', '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}')"
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
                            Belum ada request SK Pembimbing Skripsi
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
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Pembimbing Skripsi
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
                    <i class="fas fa-file-signature me-2"></i>Buatkan Surat SK Pembimbing Skripsi
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
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-file-alt me-2"></i>Preview Surat
                        </h6>
                        <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                            <div class="preview-document">
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

                                <!-- Document Title -->
                                <div class="preview-title">
                                    KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNOJOYO MADURA
                                </div>
                                
                                <div class="preview-nomor">
                                    NOMOR: <span id="preview-nomor-surat" class="preview-placeholder">[Nomor Surat]</span>
                                </div>

                                <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
                                    TENTANG<br><br>
                                    PENETAPAN DOSEN PEMBIMBING SKRIPSI<br>
                                    PROGRAM STUDI <span id="preview-prodi-text" style="text-transform: uppercase;">TEKNIK INFORMATIKA</span> FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNOJOYO MADURA SEMESTER <span id="preview-semester-text">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-text-2" class="preview-placeholder">2023/2024</span>
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
                                                        <td style="text-align: justify; border: none;">Bahwa untuk memperlancar penyusunan Skripsi mahasiswa, perlu menugaskan dosen sebagai pembimbing Skripsi;</td>
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
                                            <td style="width: 120px; vertical-align: top; border: none;">Mengingat</td>
                                            <td style="width: 20px; vertical-align: top; border: none;">:</td>
                                            <td style="text-align: justify; border: none;">
                                                <table style="border: none; width: 100%;">
                                                    <tr>
                                                        <td style="width: 20px; vertical-align: top; border: none;">Surat dari Ketua Jurusan Teknik Informatika tentang permohonan SK Dosen Pembimbing Skripsi;</td>
                                                        
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
                                                PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI S1 <span id="preview-prodi-menetapkan" style="text-transform: uppercase;">TEKNIK INFORMATIKA</span> FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER <span id="preview-semester-menetapkan">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-menetapkan">2023/2024</span>.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 120px; vertical-align: top; border: none; padding-top: 10px;">Kesatu</td>
                                            <td style="width: 20px; vertical-align: top; border: none; padding-top: 10px;">:</td>
                                            <td style="text-align: justify; border: none; padding-top: 10px;">
                                                Dosen Pembimbing Skripsi Program Studi S1 <span id="preview-prodi-kesatu" style="text-transform: capitalize;">Teknik Informatika</span> Fakultas Teknik Universitas Trunojoyo Madura semester <span id="preview-semester-kesatu">Ganjil</span> Tahun Akademik <span id="preview-tahun-kesatu">2023/2024</span> sebagaimana tercantum dalam lampiran Keputusan ini;
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

                                <!-- Signature Section -->
                                <div class="preview-signature">
                                    <div style="display: inline-block; text-align: center; margin-top: 30px;">
                                        <div>Ditetapkan di : Bangkalan</div>
                                        <div>Pada tanggal : <span id="preview-tanggal"></span></div>
                                        <div style="margin-top: 10px; font-weight: bold;">Dekan,</div>
                                        <div style="margin-top: 80px; font-weight: bold;">
                                            <span id="preview-dekan-name"></span>
                                        </div>
                                        <div>
                                            NIP. <span id="preview-dekan-nip"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lampiran will be inserted here -->
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
                <button type="button" class="btn btn-warning" onclick="submitToWadek()" id="btnSubmitSK">
                    <i class="fas fa-paper-plane me-1"></i><span id="submitText">Ajukan SK</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const dekanName = @json($dekanName ?? '');
    const dekanNip = @json($dekanNip ?? '');
    let currentSKData = null;

    // Set dekan info and date on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('preview-dekan-name').textContent = dekanName || '[Nama Dekan]';
        document.getElementById('preview-dekan-nip').textContent = dekanNip || '[NIP Dekan]';
        
        const today = new Date();
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('preview-tanggal').textContent = today.toLocaleDateString('id-ID', options);
    });

    // Show modal buatkan surat for single SK
    function showBuatkanSuratModal(skId, prodi, semester, tahunAkademik, jumlahMahasiswa) {
        // Set current SK data
        currentSKData = {
            id: skId,
            prodi: prodi,
            semester: semester,
            tahun: tahunAkademik,
            jumlah: jumlahMahasiswa
        };
        
        // Populate info
        document.getElementById('info-prodi').textContent = prodi;
        document.getElementById('info-semester').textContent = semester;
        document.getElementById('info-tahun').textContent = tahunAkademik;
        document.getElementById('info-jumlah').textContent = jumlahMahasiswa + ' Mahasiswa';
        document.getElementById('current-sk-id').value = skId;
        
        // Reset input fields
        document.getElementById('nomorSurat').value = '';
        document.getElementById('tahun-akademik').value = tahunAkademik;
        
        // Update preview with prodi name
        document.getElementById('preview-prodi-text').textContent = prodi.toUpperCase();
        document.getElementById('preview-prodi-menetapkan').textContent = prodi.toUpperCase();
        document.getElementById('preview-prodi-kesatu').textContent = prodi;
        
        // Update semester
        const semesterUpper = semester.toUpperCase();
        const semesterCapitalize = semester.charAt(0).toUpperCase() + semester.slice(1).toLowerCase();
        document.getElementById('preview-semester-text').textContent = semesterUpper;
        document.getElementById('preview-semester-menetapkan').textContent = semesterUpper;
        document.getElementById('preview-semester-kesatu').textContent = semesterCapitalize;
        
        // Update tahun akademik
        document.getElementById('preview-tahun-text-2').textContent = tahunAkademik;
        document.getElementById('preview-tahun-text-2').classList.remove('preview-placeholder');
        document.getElementById('preview-tahun-menetapkan').textContent = tahunAkademik;
        document.getElementById('preview-tahun-kesatu').textContent = tahunAkademik;
        
        // Load preview document data
        loadPreviewData(skId);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalBuatkanSurat'));
        modal.show();
    }

    // Load data ke preview document
    function loadPreviewData(skId) {
        fetch('{{ route("admin_fakultas.sk.pembimbing-skripsi.get-details") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sk_ids: [skId] })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePreviewTable(data.mahasiswa_list);
            }
        })
        .catch(error => {
            console.error('Error loading preview:', error);
        });
    }

    // Update preview table dengan data mahasiswa
    function updatePreviewTable(mahasiswaList) {
        const lampiranContainer = document.getElementById('lampiran-container');
        if (!lampiranContainer) return;

        const nomorSuratInput = document.getElementById('nomorSurat');
        const tahunAkademikInput = document.getElementById('tahun-akademik');
        
        const nomorSuratValue = nomorSuratInput && nomorSuratInput.value.trim();
        const nomorSuratHtml = nomorSuratValue
            ? nomorSuratValue
            : '<span class="preview-placeholder">[Nomor Surat]</span>';

        const tahunAkademikText = (tahunAkademikInput && tahunAkademikInput.value.trim())
            ? tahunAkademikInput.value.trim()
            : currentSKData.tahun;

        const prodiName = currentSKData.prodi;
        const semesterUpper = currentSKData.semester.toUpperCase();

        let lampiranHtml = `
            <div class="lampiran-prodi" style="margin-top: 60px; page-break-before: auto;">
                <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR <span class="preview-nomor-surat-lampiran">${nomorSuratHtml}</span></p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK <span class="preview-tahun-akademik-lampiran">${tahunAkademikText}</span></p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PEMBIMBING SKRIPSI</p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK</p>
                    <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNOJOYO MADURA</p>
                    <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK <span class="preview-tahun-akademik-lampiran">${tahunAkademikText}</span></p>
                </div>
                <table class="preview-table-mahasiswa">
                    <colgroup>
                        <col style="width: 4%;">
                        <col style="width: 8%;">
                        <col style="width: 15%;">
                        <col style="width: 23%;">
                        <col style="width: 25%;">
                        <col style="width: 25%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Judul Skripsi</th>
                            <th>Pembimbing 1</th>
                            <th>Pembimbing 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${mahasiswaList.map((mhs, idx) => `
                            <tr>
                                <td style="text-align: center;">${idx + 1}</td>
                                <td style="text-align: center;">${mhs.nim || '-'}</td>
                                <td>${mhs.nama_mahasiswa || '-'}</td>
                                <td style="font-size: 9pt;">${mhs.judul_skripsi || '-'}</td>
                                <td style="font-size: 9pt;">
                                    ${mhs.pembimbing_1 ? mhs.pembimbing_1.nama_dosen : '-'}<br>
                                    <small>NIP: ${mhs.pembimbing_1 ? mhs.pembimbing_1.nip : '-'}</small>
                                </td>
                                <td style="font-size: 9pt;">
                                    ${mhs.pembimbing_2 ? mhs.pembimbing_2.nama_dosen : '-'}<br>
                                    <small>NIP: ${mhs.pembimbing_2 ? mhs.pembimbing_2.nip : '-'}</small>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div style="margin-top: 50px; font-size: 10pt;">
                    <div style="text-align: right;">
                        <div>Ditetapkan di : Bangkalan</div>
                        <div>Pada tanggal : ${document.getElementById('preview-tanggal').textContent}</div>
                        <div style="margin-top: 10px; font-weight: bold;">Dekan,</div>
                        <div style="margin-top: 80px; font-weight: bold;">
                            ${dekanName || '[Nama Dekan]'}
                        </div>
                        <div>NIP. ${dekanNip || '[NIP Dekan]'}</div>
                    </div>
                </div>
            </div>
        `;

        lampiranContainer.innerHTML = lampiranHtml;
    }

    // Live update nomor surat
    document.getElementById('nomorSurat').addEventListener('input', function(e) {
        const nomor = e.target.value.trim();
        const previewElement = document.getElementById('preview-nomor-surat');
        const lampiranElements = document.querySelectorAll('.preview-nomor-surat-lampiran');

        if (nomor) {
            previewElement.textContent = nomor;
            previewElement.classList.remove('preview-placeholder');
            lampiranElements.forEach(el => {
                el.innerHTML = nomor;
                el.classList.remove('preview-placeholder');
            });
        } else {
            previewElement.textContent = '[Nomor Surat]';
            previewElement.classList.add('preview-placeholder');
            lampiranElements.forEach(el => {
                el.innerHTML = '<span class="preview-placeholder">[Nomor Surat]</span>';
            });
        }
    });

    // Live update tahun akademik
    document.getElementById('tahun-akademik').addEventListener('input', function(e) {
        const tahun = e.target.value.trim();
        const tahunText2 = document.getElementById('preview-tahun-text-2');
        const tahunPlaceholders = document.querySelectorAll('.preview-tahun-akademik-lampiran');

        if (tahun) {
            if (tahunText2) {
                tahunText2.textContent = tahun;
                tahunText2.classList.remove('preview-placeholder');
            }
            tahunPlaceholders.forEach(el => {
                el.textContent = tahun;
                el.classList.remove('preview-placeholder');
            });
        } else {
            if (tahunText2) {
                tahunText2.textContent = '[2023/2024]';
                tahunText2.classList.add('preview-placeholder');
            }
            tahunPlaceholders.forEach(el => {
                el.textContent = '[2023/2024]';
                el.classList.add('preview-placeholder');
            });
        }
    });

    function submitToWadek() {
        const nomorSurat = document.getElementById('nomorSurat').value.trim();
        const tahunAkademik = document.getElementById('tahun-akademik').value.trim();
        const skId = document.getElementById('current-sk-id').value;
        
        if (!nomorSurat) {
            alert('Nomor surat harus diisi');
            return;
        }
        
        if (!tahunAkademik) {
            alert('Tahun akademik harus diisi');
            return;
        }
        
        if (!skId) {
            alert('Data SK tidak valid');
            return;
        }
        
        // Disable button
        const btnSubmit = document.getElementById('btnSubmitSK');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sedang diproses...';
        
        // Prepare data
        const formData = {
            sk_ids: [skId],
            nomor_surat: nomorSurat,
            tahun_akademik: tahunAkademik,
            _token: '{{ csrf_token() }}'
        };
        
        // Submit to server
        fetch('{{ route("admin_fakultas.sk.pembimbing-skripsi.submit-wadek") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan server');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('SK berhasil diajukan!');
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Ajukan SK';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Ajukan SK';
        });
    }

    function applyFilters() {
        const semester = document.getElementById('filterSemester').value;
        
        let url = new URL(window.location.href);
        
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        
        window.location.href = url.toString();
    }

    // Show reject modal
    function showRejectModal(skId, prodi, semester, tahun) {
        document.getElementById('reject-sk-id').value = skId;
        document.getElementById('reject-prodi').textContent = prodi;
        document.getElementById('reject-semester').textContent = semester;
        document.getElementById('reject-tahun').textContent = tahun;
        document.getElementById('reject-alasan').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }

    // Submit rejection
    function submitRejection() {
        const skId = document.getElementById('reject-sk-id').value;
        const alasan = document.getElementById('reject-alasan').value.trim();
        
        if (!alasan) {
            alert('Alasan penolakan harus diisi');
            return;
        }
        
        if (alasan.length < 10) {
            alert('Alasan penolakan minimal 10 karakter');
            return;
        }
        
        // Disable button to prevent double click
        const btnSubmit = event.target;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
        
        // Submit to server
        fetch('{{ route("admin_fakultas.sk.pembimbing-skripsi.reject") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sk_id: skId,
                alasan: alasan
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan server');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('SK berhasil ditolak');
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolakSK'));
                if (modal) modal.hide();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menolak SK: ' + error.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-ban me-1"></i>Tolak SK';
        });
    }
</script>
@endpush

@endsection
