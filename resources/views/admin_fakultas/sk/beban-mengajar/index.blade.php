@extends('layouts.admin_fakultas')

@section('title', 'Request SK Beban Mengajar')

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
        width: 80px;
        float: left;
        margin-top: -5px;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
    }
    .preview-header .line-1 { font-size: 14pt; font-weight: bold; }
    .preview-header .line-2 { font-size: 16pt; font-weight: bold; }
    .preview-header .line-3 { font-size: 14pt; font-weight: bold; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
        font-weight: normal;
    }
    .preview-table-beban {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
        border: 1px solid #000;
    }
    .preview-table-beban th,
    .preview-table-beban td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    .preview-table-beban thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
        text-transform: capitalize;
    }
    .preview-table-beban tbody td {
        font-size: 9pt;
        vertical-align: top;
    }
    .preview-table-beban tbody td:nth-child(1) {
        text-align: center;
    }
    .preview-table-beban tbody td:nth-child(2) {
        text-align: left;
    }
    .preview-table-beban tbody td:nth-child(3) {
        text-align: left;
    }
    .preview-table-beban tbody td:nth-child(4),
    .preview-table-beban tbody td:nth-child(5) {
        text-align: center;
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
        <h1 class="h3 fw-bold mb-0">Request SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">Kelola pengajuan SK Beban Mengajar dari Kaprodi</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" id="btnBuatkanSurat" disabled>
            <i class="fas fa-file-alt me-2"></i>Buatkan Surat (<span id="countSelected">0</span>)
        </button>
        <a href="{{ route('admin_fakultas.sk.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
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
        <h6 class="m-0 fw-bold text-primary">Riwayat SK Beban Mengajar</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>No</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Jumlah Dosen</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        @php
                            $bebanData = $sk->Data_Beban_Mengajar;
                            // Handle double-encoded JSON (old data)
                            if (is_string($bebanData)) {
                                $bebanData = json_decode($bebanData, true);
                            }
                            $jumlahDosen = is_array($bebanData) ? count($bebanData) : 0;
                        @endphp
                        <td>
                            @if($sk->Status == 'Dikerjakan admin' || $sk->Status == 'Ditolak-Wadek1' || $sk->Status == 'Ditolak-Dekan')
                            <input type="checkbox" class="form-check-input sk-checkbox" 
                                   data-id="{{ $sk->No }}"
                                   data-prodi="{{ $sk->prodi->Nama_Prodi ?? '-' }}"
                                   data-semester="{{ $sk->Semester }}"
                                   data-tahun="{{ $sk->Tahun_Akademik }}"
                                   data-status="{{ $sk->Status }}"
                                   data-jumlah="{{ $jumlahDosen }}">
                            @endif
                        </td>
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
                                {{ $jumlahDosen }} Dosen
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
                                switch($sk->Status) {
                                    case 'Dikerjakan admin':
                                        $badgeClass = 'warning';
                                        break;
                                    case 'Menunggu-Persetujuan-Wadek-1':
                                        $badgeClass = 'info';
                                        break;
                                    case 'Menunggu-Persetujuan-Dekan':
                                        $badgeClass = 'primary';
                                        break;
                                    case 'Selesai':
                                        $badgeClass = 'success';
                                        break;
                                    case 'Ditolak-Admin':
                                    case 'Ditolak-Wadek1':
                                    case 'Ditolak-Dekan':
                                    case 'Ditolak':
                                        $badgeClass = 'danger';
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $sk->Status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin_fakultas.sk.beban-mengajar.detail', $sk->No) }}" 
                                   class="btn btn-primary" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sk->Status == 'Dikerjakan admin')
                                <button onclick="showRejectModal({{ $sk->No }}, '{{ $sk->prodi->Nama_Prodi ?? '-' }}', '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}')" 
                                        class="btn btn-danger" title="Tolak Pengajuan">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                @if($sk->Status == 'Selesai')
                                <a href="{{ route('admin_fakultas.sk.beban-mengajar.download', $sk->No) }}" 
                                   class="btn btn-success" title="Download SK">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada riwayat SK Beban Mengajar
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

<!-- Modal Buatkan Surat -->
<div class="modal fade" id="modalBuatkanSurat" tabindex="-1" aria-labelledby="modalBuatkanSuratLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBuatkanSuratLabel">
                    <i class="fas fa-file-signature me-2"></i>Buatkan Surat SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Selected Data -->
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-list-check me-2"></i>Data SK yang Dipilih
                        </h6>
                        <div id="selectedDataTable"></div>
                        
                        <div class="mt-4">
                            <label for="nomorSurat" class="form-label fw-bold">
                                Nomor Surat <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nomorSurat" 
                                   placeholder="Contoh: 123/UN45.1/KM/2025" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Format: Nomor/Kode/Tahun
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="tahun-akademik" class="form-label fw-bold">
                                Tahun Akademik <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tahun-akademik" 
                                   placeholder="Contoh: 2023/2024" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Format: Tahun sekarang/Tahun depan
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
                                <!-- Header -->
                                <div class="preview-header">
                                    <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
                                    <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                                    <strong class="line-2">UNIVERSITAS TRUNODJOYO</strong>
                                    <strong class="line-3">FAKULTAS TEKNIK</strong>
                                    <div class="address">
                                        Kampus UTM, Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                                        Telp: (031) 3011146, Fax: (031) 3011506
                                    </div>
                                    <div style="clear: both;"></div>
                                </div>

                                <!-- Title -->
                                <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 11pt;">
                                    KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNODJOYO<br>
                                    NOMOR <span id="preview-nomor-surat"><span class="preview-placeholder">[Nomor Surat]</span></span>
                                </div>

                                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                    TENTANG
                                </div>

                                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                    BEBAN MENGAJAR DOSEN PROGRAM STUDI FAKULTAS TEKNIK FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNODJOYO<br>
                                    SEMESTER <span id="preview-semester-text">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-text"><span class="preview-placeholder">[2023/2024]</span></span>
                                </div>

                                <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                                    DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
                                </div>

                                <!-- Content Preview -->
                                <!-- Menimbang -->
                                <div class="preview-content">
                                    <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 10%; vertical-align: top;">:</td>
                                            <td style="width: 5%; vertical-align: top;">a.</td>
                                            <td style="text-align: justify;">bahwa untuk kelancaran perkuliahan Program S1 di Fakultas Teknik Universitas Trunodjoyo, maka perlu menetapkan beban mengajar dosen;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">b.</td>
                                            <td style="text-align: justify;">bahwa untuk pelaksanaan butir a di atas, perlu menerbitkan Surat Keputusan Dekan Fakultas Teknik;</td>
                                        </tr>
                                    </table>

                                    <p style="margin-bottom: 10px; font-weight: normal;">Mengingat</p>
                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 10%; vertical-align: top;">:</td>
                                            <td style="width: 5%; vertical-align: top;">1.</td>
                                            <td style="text-align: justify;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">2.</td>
                                            <td style="text-align: justify;">Peraturan Pemerintah Nomor 60 tahun 1999, tentang Pendidikan Tinggi;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">3.</td>
                                            <td style="text-align: justify;">Keputusan Presiden RI Nomor 85 tahun 2001, tentang pendirian Universitas Trunodjoyo;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">4.</td>
                                            <td style="text-align: justify;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/U/2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">5.</td>
                                            <td style="text-align: justify;">Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor 73649/MPK.A/KP.06.02/2022 tentang pengangkatan Rektor UTM periode 2022-2026;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">6.</td>
                                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunodjoyo periode 2021-2025;</td>
                                        </tr>
                                    </table>

                                    <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 10%; vertical-align: top;">:</td>
                                            <td style="width: 5%; vertical-align: top;">1.</td>
                                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunodjoyo Nomor 190/UN46/2016, tentang Buku Pedoman Akademik Universitas Trunodjoyo Tahun Akademik 2016/2017;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">2.</td>
                                            <td style="text-align: justify;">Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Beban Mengajar Ganjil <span id="preview-tahun-text-2">2023/2024</span>;</td>
                                        </tr>
                                    </table>

                                    <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                        MEMUTUSKAN :
                                    </div>

                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify; font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER <span id="preview-semester-text-2">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-text-3"><span class="preview-placeholder preview-tahun-akademik-lampiran">[2023/2024]</span></span>.</td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%; margin-bottom: 10px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify;">Beban mengajar dosen Program Studi S1 Teknik Informatika, Program Studi S1 Teknik Industri, Program Studi S1 Teknik Elektro, Program Studi S1 Teknik Elektro, Program Studi S1 Teknik Mesin, Program Studi S1 Sistem Informasi, Program Studi S1 Teknik Mekatronika Fakultas Teknik Universitas Trunodjoyo Semester <span id="preview-semester-text-3">Ganjil</span> Tahun Akademik <span id="preview-tahun-text-4"><span class="preview-placeholder preview-tahun-akademik-lampiran">[2023/2024]</span></span> sebagimana terlampir dalam surat keputusan ini.</td>
                                        </tr>
                                    </table>

                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kedua</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify;">Keputusan ini berlaku sejak tanggal ditetapkan.<br>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tanda Tangan Dekan -->
                                <div class="preview-signature" style="font-size: 10pt; margin: 40px 0 30px 0;">
                                    <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                                    <p style="margin-bottom: 3px;">pada tanggal <span id="preview-tanggal">{{ date('d F Y') }}</span></p>
                                    <p style="margin-bottom: 70px;"><strong>DEKAN,</strong></p>
                                    <p style="margin-bottom: 0;">
                                        <strong><u>{{ $dekanName }}</u></strong><br>
                                        NIP. {{ $dekanNip }}
                                    </p>
                                </div>

                                <!-- Lampiran: akan digenerate per prodi oleh JavaScript -->
                                <div id="lampiran-container">
                                    <div style="font-size: 11pt; text-align: left; margin-top: 30px; margin-bottom: 10px;">
                                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN I KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR 322/UN46.3.4/HK.04/2023</p>
                                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SEMESTER <span id="preview-semester-text-3">Ganjil</span> Tahun Akademik <span id="preview-tahun-text-4"><span class="preview-placeholder preview-tahun-akademik-lampiran">[2023/2024]</span></p>
                                    </div>
                                    <table class="preview-table-beban">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Dosen</th>
                                                <th>Mata Kuliah</th>
                                                <th>Kelas</th>
                                                <th>SKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" style="text-align: center;">
                                                    <span class="preview-placeholder">Data akan muncul setelah memilih SK</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="submitToWadek()" id="btnSubmitSK">
                    <i class="fas fa-paper-plane me-1"></i><span id="submitText">Ajukan SK</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak SK -->
<div class="modal fade" id="modalTolakSK" tabindex="-1" aria-labelledby="modalTolakSKLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakSKLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Beban Mengajar
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

@push('scripts')
<script>
    const dekanName = @json($dekanName ?? '');
    const dekanNip = @json($dekanNip ?? '');
    let selectedSK = [];

    // Select All Checkbox Handler
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.sk-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual Checkbox Handler
    document.querySelectorAll('.sk-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection();
            
            // Update select all checkbox
            const allCheckboxes = document.querySelectorAll('.sk-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.sk-checkbox:checked');
            document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
        });
    });

    function updateSelection() {
        const checkedCheckboxes = document.querySelectorAll('.sk-checkbox:checked');
        selectedSK = Array.from(checkedCheckboxes).map(cb => ({
            id: cb.dataset.id,
            prodi: cb.dataset.prodi,
            semester: cb.dataset.semester,
            tahun: cb.dataset.tahun,
            jumlah: cb.dataset.jumlah,
            status: cb.dataset.status
        }));
        
        // Update button state and counter
        const btnBuatkanSurat = document.getElementById('btnBuatkanSurat');
        const countSelected = document.getElementById('countSelected');
        
        if (selectedSK.length > 0) {
            btnBuatkanSurat.disabled = false;
            countSelected.textContent = selectedSK.length;
        } else {
            btnBuatkanSurat.disabled = true;
            countSelected.textContent = '0';
        }
    }

    // Open Modal with Selected Data
    document.getElementById('btnBuatkanSurat').addEventListener('click', function() {
        if (selectedSK.length === 0) {
            alert('Pilih minimal satu SK terlebih dahulu');
            return;
        }
        
        // Cek apakah ada SK yang ditolak Dekan
        const hasDitolakDekan = selectedSK.some(sk => sk.status === 'Ditolak-Dekan');
        
        // Update teks tombol submit
        const submitText = document.getElementById('submitText');
        if (hasDitolakDekan) {
            submitText.textContent = 'Ajukan ke Dekan';
        } else {
            submitText.textContent = 'Ajukan ke Wadek 1';
        }
        
        // Populate modal with selected data
        let tableHTML = '<table class="table table-sm table-bordered"><thead><tr><th>No</th><th>Prodi</th><th>Semester</th><th>Tahun Akademik</th><th>Jumlah Dosen</th></tr></thead><tbody>';
        selectedSK.forEach((sk, index) => {
            tableHTML += `<tr>
                <td>${index + 1}</td>
                <td>${sk.prodi}</td>
                <td>${sk.semester}</td>
                <td>${sk.tahun}</td>
                <td>${sk.jumlah} Dosen</td>
            </tr>`;
        });
        tableHTML += '</tbody></table>';
        
        document.getElementById('selectedDataTable').innerHTML = tableHTML;
        
        // Load preview document data
        loadPreviewData();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalBuatkanSurat'));
        modal.show();
    });

    // Load data ke preview document
    function loadPreviewData() {
        const skIds = selectedSK.map(sk => sk.id);
        
        fetch('{{ route("admin_fakultas.sk.beban-mengajar.get-details") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sk_ids: skIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePreviewTable(data.beban_mengajar_list);
            }
        })
        .catch(error => {
            console.error('Error loading preview:', error);
        });
    }

    // Update preview table dengan data beban mengajar
    function updatePreviewTable(bebanMengajarList) {
        // Update semester dan tahun akademik
        if (selectedSK.length > 0) {
            const firstSK = selectedSK[0];
            const semesterUpper = firstSK.semester.toUpperCase();
            
            // Update semua elemen semester
            const semesterElements = [
                'preview-semester-text',
                'preview-semester-text-2',
                'preview-semester-text-3'
            ];
            
            semesterElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.textContent = semesterUpper;
                }
            });

            const tahunInput = document.getElementById('tahun-akademik');
            if (tahunInput && !tahunInput.value.trim()) {
                tahunInput.value = firstSK.tahun;
            }
            if (tahunInput) {
                tahunInput.dispatchEvent(new Event('input'));
            }
        }

        // Kelompokkan beban mengajar per prodi
        const groupedByProdi = {};
        bebanMengajarList.forEach(item => {
            const prodiName = item.prodi || '-';
            if (!groupedByProdi[prodiName]) {
                groupedByProdi[prodiName] = [];
            }
            groupedByProdi[prodiName].push(item);
        });

        const lampiranContainer = document.getElementById('lampiran-container');
        if (!lampiranContainer) return;

        const nomorSuratInput = document.getElementById('nomorSurat');
        const tahunAkademikInput = document.getElementById('tahun-akademik');
        const firstSK = selectedSK[0] || {};
        const semesterUpper = (firstSK.semester || 'GANJIL').toUpperCase();

        const nomorSuratValue = nomorSuratInput && nomorSuratInput.value.trim();
        const nomorSuratHtml = nomorSuratValue
            ? nomorSuratValue
            : '<span class="preview-placeholder">[Nomor Surat]</span>';

        const tahunAkademikText = (tahunAkademikInput && tahunAkademikInput.value.trim())
            ? tahunAkademikInput.value.trim()
            : '2023/2024';

        let lampiranHtml = '';

        Object.keys(groupedByProdi).forEach((prodiName, index) => {
            const bebanProdi = groupedByProdi[prodiName];

            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN I KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSuratHtml}</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademikText}</p>
                        <p style="margin: 0 0 13px 0; text-align: center; font-weight: bold;">BEBAN MENGAJAR DOSEN ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademikText}</p>
                    </div>
                    <table class="preview-table-beban">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Dosen / NIP</th>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${bebanProdi.map((item, idx) => {
                                // Coba berbagai kemungkinan nama field untuk mata kuliah
                                const mataKuliah = item.nama_mata_kuliah || item.mata_kuliah || item.Nama_Matakuliah || item['nama-mata-kuliah'] || '-';
                                const kelas = item.kelas || item.Kelas || '-';
                                
                                return `
                                    <tr>
                                        <td>${idx + 1}.</td>
                                        <td>${item.nama_dosen || item.Nama_Dosen || '-'}<br><small>${item.nip || item.NIP || '-'}</small></td>
                                        <td>${mataKuliah}</td>
                                        <td>${kelas}</td>
                                        <td>${item.sks || item.SKS || 0}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 30px 0;">pada tanggal <span>${document.getElementById('preview-tanggal').textContent}</span></p>
                            <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
                            <p style="margin: 0 0 0 0;">
                                <strong><u>${dekanName}</u></strong><br>
                                NIP. ${dekanNip}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        lampiranContainer.innerHTML = lampiranHtml;
    }

    // Live update nomor surat
    document.getElementById('nomorSurat').addEventListener('input', function(e) {
        const nomor = e.target.value.trim();
        const previewElement = document.getElementById('preview-nomor-surat');

        if (nomor) {
            previewElement.innerHTML = nomor;
        } else {
            const placeholder = '<span class="preview-placeholder">[Nomor Surat]</span>';
            previewElement.innerHTML = placeholder;
        }
        
        // Re-render lampiran to update nomor surat there too
        if (selectedSK.length > 0) {
            loadPreviewData();
        }
    });

    // Live update tahun akademik
    document.getElementById('tahun-akademik').addEventListener('input', function(e) {
        const tahunAkademik = e.target.value.trim();
        
        // Update semua element yang menampilkan tahun akademik
        const tahunElements = [
            'preview-tahun-text',
            'preview-tahun-text-2',
            'preview-tahun-text-3',
            'preview-tahun-text-4'
        ];

        tahunElements.forEach(elementId => {
            const element = document.getElementById(elementId);
            if (element) {
                if (tahunAkademik) {
                    element.textContent = tahunAkademik;
                } else {
                    element.innerHTML = '<span class="preview-placeholder">[2023/2024]</span>';
                }
            }
        });
        
        // Re-render lampiran to update tahun there too
        if (selectedSK.length > 0) {
            loadPreviewData();
        }
    });

    function submitToWadek() {
        const nomorSurat = document.getElementById('nomorSurat').value.trim();
        
        if (!nomorSurat) {
            alert('Nomor surat harus diisi');
            return;
        }
        
        if (selectedSK.length === 0) {
            alert('Tidak ada SK yang dipilih');
            return;
        }
        
        // Prepare data
        const formData = {
            sk_ids: selectedSK.map(sk => sk.id),
            nomor_surat: nomorSurat,
            _token: '{{ csrf_token() }}'
        };
        
        // Submit to server
        fetch('{{ route("admin_fakultas.sk.beban-mengajar.submit-wadek") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', data.message || 'SK berhasil diajukan', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Gagal mengajukan SK', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat mengirim data', 'error');
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
        
        if (!confirm('Apakah Anda yakin ingin menolak SK ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }
        
        // Submit to server
        fetch('{{ route("admin_fakultas.sk.beban-mengajar.reject") }}', {
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolakSK'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data');
        });
    }

</script>
@endpush

@endsection
