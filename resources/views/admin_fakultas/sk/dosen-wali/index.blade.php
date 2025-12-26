@extends('layouts.admin_fakultas')

@section('title', 'Request SK Dosen Wali')

@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #fdfdfd;
        color: #000;
        border: 1px solid #ccc;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 11pt;
        line-height: 1.6;
        min-height: 500px;
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
    .preview-table-dosen {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
    }
    .preview-table-dosen th,
    .preview-table-dosen td {
        border: 1px solid #000;
        padding: 6px 8px;
        text-align: left;
        vertical-align: middle;
    }
    .preview-table-dosen th {
        background-color: #e8e8e8;
        font-weight: bold;
        text-align: center;
        font-size: 10pt;
    }
    .preview-table-dosen td {
        font-size: 10pt;
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
        <h1 class="h3 fw-bold mb-0">Request SK Dosen Wali</h1>
        <p class="mb-0 text-muted">Kelola pengajuan SK Dosen Wali dari Kaprodi</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="btnBuatkanSurat" disabled>
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
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Dikerjakan admin">Dikerjakan Admin</option>
                    <option value="Menunggu-Persetujuan-Wadek-1">Menunggu Wadek 1</option>
                    <option value="Menunggu-Persetujuan-Dekan">Menunggu Dekan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterProdi">
                    <option value="">Semua Prodi</option>
                    @foreach($skList->pluck('prodi')->unique('Id_Prodi') as $prodi)
                        @if($prodi)
                            <option value="{{ $prodi->Id_Prodi }}">{{ $prodi->Nama_Prodi }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterSemester">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="col-md-3">
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
        <h6 class="m-0 fw-bold text-success">Daftar Pengajuan SK Dosen Wali</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="3%">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>No</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Jumlah Dosen</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tenggat</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $sk)
                    <tr>
                        @php
                            $dosenData = $sk->Data_Dosen_Wali;
                            // Handle double-encoded JSON (old data)
                            if (is_string($dosenData)) {
                                $dosenData = json_decode($dosenData, true);
                            }
                            $jumlahDosen = is_array($dosenData) ? count($dosenData) : 0;
                        @endphp
                        <td>
                            @if($sk->Status == 'Dikerjakan admin')
                            <input type="checkbox" class="form-check-input sk-checkbox" 
                                   data-id="{{ $sk->No }}"
                                   data-prodi="{{ $sk->prodi->Nama_Prodi ?? 'N/A' }}"
                                   data-semester="{{ $sk->Semester }}"
                                   data-tahun="{{ $sk->Tahun_Akademik }}"
                                   data-jumlah="{{ $jumlahDosen }}">
                            @endif
                        </td>
                        <td>{{ $sk->No }}</td>
                        <td>{{ $sk->prodi->Nama_Prodi ?? 'N/A' }}</td>
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
                                $tanggalTenggat = $sk->{'Tanggal-Tenggat'};
                                $isOverdue = $tanggalTenggat && $tanggalTenggat->isPast();
                            @endphp
                            <span class="text-{{ $isOverdue ? 'danger' : 'muted' }}">
                                {{ $tanggalTenggat ? $tanggalTenggat->format('d M Y') : '-' }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle ms-1"></i>
                                @endif
                            </span>
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
                                    case 'Ditolak':
                                        $badgeClass = 'danger';
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $sk->Status }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.sk.dosen-wali.detail', $sk->No) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada pengajuan SK Dosen Wali
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
                    <i class="fas fa-file-signature me-2"></i>Buatkan Surat SK Dosen Wali
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
                                    <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
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
                                    UNIVERSITAS TRUNOJOYO MADURA<br>
                                    NOMOR <span id="preview-nomor-surat"><span class="preview-placeholder">[Nomor Surat]</span></span>
                                </div>

                                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                    TENTANG
                                </div>

                                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                    DOSEN WALI MAHASISWA FAKULTAS TEKNIK<br>
                                    UNIVERSITAS TRUNOJOYO MADURA<br>
                                    SEMESTER <span id="preview-semester-text">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-text">2023/2024</span>
                                </div>

                                <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                                    DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
                                </div>

                                <!-- Menimbang -->
                                <div class="preview-content">
                                    <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 10%; vertical-align: top;">:</td>
                                            <td style="width: 5%; vertical-align: top;">a.</td>
                                            <td style="text-align: justify;">bahwa dalam rangka membantu mahasiswa menyelesaikan program sarjana/diploma sesuai rencana studi, perlu menugaskan dosen tetap di lingkungan Fakultas Teknik Universitas Trunojoyo Madura sebagai dosen wali;</td>
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
                                            <td style="text-align: justify;">Keputusan Presiden RI Nomor 85 tahun 2001, tentang pendirian Universitas Trunojoyo Madura;</td>
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
                                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunojoyo Madura Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunojoyo Madura periode 2021-2025;</td>
                                        </tr>
                                    </table>

                                    <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 10%; vertical-align: top;">:</td>
                                            <td style="width: 5%; vertical-align: top;">1.</td>
                                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunojoyo Madura Nomor 190/UN46/2016, tentang Buku Pedoman Akademik Universitas Trunojoyo Madura Tahun Akademik 2016/2017;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="vertical-align: top;">2.</td>
                                            <td style="text-align: justify;">Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Dosen Wali Ganjil <span id="preview-tahun-text-2">2023/2024</span>;</td>
                                        </tr>
                                    </table>

                                    <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                                        MEMUTUSKAN :
                                    </div>

                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify; font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER <span id="preview-semester-text-2">GANJIL</span> TAHUN AKADEMIK <span id="preview-tahun-text-3">2023/2024</span>.</td>
                                        </tr>
                                    </table>

                                    <table style="width: 100%; margin-bottom: 10px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify;">Menugaskan dosen tetap di Fakultas Teknik Universitas Trunojoyo Madura yang namanya tersebut dalam lampiran Surat Keputusan ini sebagai dosen wali Semester <span id="preview-semester-text-3">Ganjil</span> Tahun Akademik <span id="preview-tahun-text-4">2023/2024</span>;</td>
                                        </tr>
                                    </table>

                                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kedua</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify;">Tugas dan fungsi dosen wali tersebut yaitu:<br>
                                                <span style="margin-left: 15px;">a. Membantu mengarahkan dan mengesahkan rencana studi;</span><br>
                                                <span style="margin-left: 15px;">b. Memberi bimbingan dan nasehat mengenai berbagai masalah yang bersifat kurikuler akademik;</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tabel Dosen -->
                                <table class="preview-table-dosen" id="preview-table-dosen" style="margin-top: 15px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%;">No</th>
                                            <th style="width: 65%;">Nama Dosen</th>
                                            <th style="width: 27%;">Jumlah Anak Wali</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <span class="preview-placeholder">Data akan muncul setelah memilih SK</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Ketiga -->
                                <div class="preview-content">
                                    <table style="width: 100%; margin-top: 15px; margin-bottom: 15px; font-size: 10pt;">
                                        <tr>
                                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Ketiga</td>
                                            <td style="width: 3%; vertical-align: top;">:</td>
                                            <td style="text-align: justify;">Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tanda Tangan -->
                                <div class="preview-signature" style="font-size: 10pt;">
                                    <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                                    <p style="margin-bottom: 3px;">pada tanggal <span id="preview-tanggal">{{ date('d F Y') }}</span></p>
                                    <p style="margin-bottom: 70px;"><strong>DEKAN,</strong></p>
                                    <p style="margin-bottom: 0;">
                                        <strong><u>FAIKUL UMAM</u></strong><br>
                                        NIP. 198301182008121001
                                    </p>
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
                <button type="button" class="btn btn-primary" onclick="submitToWadek()">
                    <i class="fas fa-paper-plane me-1"></i>Ajukan ke Wadek 1
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
            jumlah: cb.dataset.jumlah
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
        // Fetch detail data for all selected SK
        const skIds = selectedSK.map(sk => sk.id);
        
        fetch('{{ route("admin_fakultas.sk.dosen-wali.get-details") }}', {
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
                updatePreviewTable(data.dosen_list);
            }
        })
        .catch(error => {
            console.error('Error loading preview:', error);
        });
    }

    // Update preview table dengan data dosen
    function updatePreviewTable(dosenList) {
        let tbody = '<tbody>';
        let counter = 1;
        
        // Update semester dan tahun akademik di semua tempat
        if (selectedSK.length > 0) {
            const firstSK = selectedSK[0];
            const semesterUpper = firstSK.semester.toUpperCase();
            document.getElementById('preview-semester-text').textContent = semesterUpper;
            document.getElementById('preview-semester-text-2').textContent = semesterUpper;
            document.getElementById('preview-semester-text-3').textContent = firstSK.semester;
            document.getElementById('preview-tahun-text').textContent = firstSK.tahun;
            document.getElementById('preview-tahun-text-2').textContent = firstSK.tahun;
            document.getElementById('preview-tahun-text-3').textContent = firstSK.tahun;
            document.getElementById('preview-tahun-text-4').textContent = firstSK.tahun;
        }
        
        dosenList.forEach(dosen => {
            tbody += `<tr>
                <td class="text-center">${counter++}</td>
                <td>${dosen.nama_dosen}</td>
                <td class="text-center">${dosen.jumlah_anak_wali}</td>
            </tr>`;
        });
        
        tbody += '</tbody>';
        
        document.querySelector('#preview-table-dosen tbody').replaceWith(
            document.createRange().createContextualFragment(tbody)
        );
    }

    // Live update nomor surat
    document.getElementById('nomorSurat').addEventListener('input', function(e) {
        const nomor = e.target.value.trim();
        const previewElement = document.getElementById('preview-nomor-surat');
        
        if (nomor) {
            previewElement.innerHTML = nomor;
        } else {
            previewElement.innerHTML = '<span class="preview-placeholder">[Nomor Surat]</span>';
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
        fetch('{{ route("admin_fakultas.sk.dosen-wali.submit-wadek") }}', {
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
                alert('Berhasil diajukan ke Wadek 1');
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

    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const prodi = document.getElementById('filterProdi').value;
        const semester = document.getElementById('filterSemester').value;
        
        let url = new URL(window.location.href);
        
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (prodi) url.searchParams.set('prodi', prodi);
        else url.searchParams.delete('prodi');
        
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        
        window.location.href = url.toString();
    }
</script>
@endpush

@endsection
