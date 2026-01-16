@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Pembimbing Skripsi')

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

    @media print {
        .modal-header, .modal-footer, .btn, nav, .breadcrumb {
            display: none !important;
        }
        .modal-body {
            padding: 0 !important;
        }
        .preview-document {
            border: none;
            box-shadow: none;
        }
    }
</style>
@endpush

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">Riwayat SK Pembimbing Skripsi</li>
        </ol>
    </nav>
</div>

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

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Riwayat SK Pembimbing Skripsi</h5>
                            <small>Daftar pengajuan SK pembimbing skripsi yang telah diajukan</small>
                        </div>
                    </div>
                    <a href="{{ route('kaprodi.sk.pembimbing-skripsi.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filter Section -->
                <form method="GET" action="{{ route('kaprodi.sk.pembimbing-skripsi.history') }}">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Semester</label>
                            <select class="form-select" name="semester">
                                <option value="">Semua Semester</option>
                                <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tahun Akademik</label>
                            <select class="form-select" name="tahun_akademik">
                                <option value="">Semua Tahun</option>
                                <option value="2025/2026" {{ request('tahun_akademik') == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                                <option value="2024/2025" {{ request('tahun_akademik') == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                                <option value="2023/2024" {{ request('tahun_akademik') == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="Dikerjakan admin" {{ request('status') == 'Dikerjakan admin' ? 'selected' : '' }}>Dikerjakan Admin</option>
                                <option value="Menunggu-Persetujuan-Wadek-1" {{ request('status') == 'Menunggu-Persetujuan-Wadek-1' ? 'selected' : '' }}>Menunggu Wadek 1</option>
                                <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>Menunggu Dekan</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditolak-Admin" {{ request('status') == 'Ditolak-Admin' ? 'selected' : '' }}>Ditolak Admin</option>
                                <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>Ditolak Wadek 1</option>
                                <option value="Ditolak-Dekan" {{ request('status') == 'Ditolak-Dekan' ? 'selected' : '' }}>Ditolak Dekan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Program Studi</th>
                                <th width="12%">Nomor SK</th>
                                <th width="10%">Semester</th>
                                <th width="12%">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="12%">Tanggal Ajuan</th>
                                <th width="12%" class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skList as $index => $sk)
                            <tr>
                                @php
                                    $pembimbingData = $sk->Data_Pembimbing_Skripsi;
                                    if (is_string($pembimbingData)) {
                                        $pembimbingData = json_decode($pembimbingData, true);
                                    }
                                    $jumlahMahasiswa = is_array($pembimbingData) ? count($pembimbingData) : 0;
                                @endphp
                                <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->accSKPembimbingSkripsi && $sk->accSKPembimbingSkripsi->Nomor_Surat)
                                        <strong class="text-warning">{{ $sk->accSKPembimbingSkripsi->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user-graduate me-1"></i>{{ $jumlahMahasiswa }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $tanggalPengajuan = $sk->{'Tanggal-Pengajuan'};
                                    @endphp
                                    <small>{{ $tanggalPengajuan ? $tanggalPengajuan->format('d M Y H:i') : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = 'secondary';
                                        $statusText = $sk->Status;
                                        switch($sk->Status) {
                                            case 'Dikerjakan admin':
                                                $badgeClass = 'warning text-dark';
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
                                        <button type="button" 
                                                class="btn btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $sk->No }}"
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        @if($sk->Status == 'Selesai' && $sk->accSKPembimbingSkripsi && $sk->accSKPembimbingSkripsi->Nomor_Surat)
                                        <a href="{{ route('kaprodi.sk.pembimbing-skripsi.download', $sk->No) }}" 
                                           class="btn btn-outline-warning" 
                                           target="_blank"
                                           title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">Belum ada riwayat pengajuan SK Pembimbing Skripsi</p>
                                    <a href="{{ route('kaprodi.sk.pembimbing-skripsi.create') }}" class="btn btn-warning btn-sm mt-3">
                                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Pertama
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($skList->hasPages())
                <div class="mt-4">
                    {{ $skList->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail for each SK -->
@push('scripts')
<script>
@foreach($skList as $sk)
@if($sk->Status == 'Selesai')
function renderPreviewKaprodi{{ $sk->No }}() {
    const sk = @json($sk);
    console.log('SK Data:', sk);
    console.log('AccSK Relation:', sk.acc_s_k_pembimbing_skripsi);
    
    const qrCodePath = (sk.acc_s_k_pembimbing_skripsi && sk.acc_s_k_pembimbing_skripsi.QR_Code) 
        ? sk.acc_s_k_pembimbing_skripsi.QR_Code 
        : null;
    const qrCodeUrl = qrCodePath ? `{{ asset('storage') }}/${qrCodePath}` : null;
    
    console.log('QR Code Path:', qrCodePath);
    console.log('QR Code URL:', qrCodeUrl);
    
    let dataPembimbing = sk.Data_Pembimbing_Skripsi || [];
    
    // Parse JSON if string
    if (typeof dataPembimbing === 'string') {
        try {
            dataPembimbing = JSON.parse(dataPembimbing);
        } catch (e) {
            console.error('Error parsing Data_Pembimbing_Skripsi:', e);
            dataPembimbing = [];
        }
    }
    
    // Convert object to array
    if (dataPembimbing && typeof dataPembimbing === 'object' && !Array.isArray(dataPembimbing)) {
        dataPembimbing = Object.values(dataPembimbing);
    }
    
    if (!Array.isArray(dataPembimbing)) {
        console.error('Data_Pembimbing_Skripsi bukan array:', dataPembimbing);
        dataPembimbing = [];
    }
    
    const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
    const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
    const namaProdi = (sk.prodi && sk.prodi.Nama_Prodi) ? sk.prodi.Nama_Prodi.toUpperCase() : 'TEKNIK INFORMATIKA';
    
    // Get data from AccSK relation (note: Laravel converts to acc_s_k not acc_sk)
    const accSK = sk.acc_s_k_pembimbing_skripsi || {};
    const nomorSurat = accSK.Nomor_Surat || '-';
    
    // Get Dekan info
    const dekanName = (accSK.dekan && (accSK.dekan.Nama_Dosen || accSK.dekan.nama_dosen)) ? (accSK.dekan.Nama_Dosen || accSK.dekan.nama_dosen) : 'Dekan Fakultas Teknik';
    const dekanNip = (accSK.dekan && (accSK.dekan.NIP || accSK.dekan.nip)) ? (accSK.dekan.NIP || accSK.dekan.nip) : '-';
    const tanggalTTD = accSK.Tanggal_Persetujuan_Dekan || new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
    
    // Group by jurusan
    const groupedByJurusan = {};
    dataPembimbing.forEach(mhs => {
        let jurusanName = '-';
        if (mhs.prodi_data && mhs.prodi_data.jurusan && mhs.prodi_data.jurusan.Nama_Jurusan) {
            jurusanName = mhs.prodi_data.jurusan.Nama_Jurusan;
        } else if (mhs.prodi && mhs.prodi !== '-') {
            jurusanName = mhs.prodi;
        } else if (mhs.prodi_data && mhs.prodi_data.nama_prodi) {
            jurusanName = mhs.prodi_data.nama_prodi;
        }
        
        const prodiName = (mhs.prodi_data && mhs.prodi_data.nama_prodi) ? mhs.prodi_data.nama_prodi : (mhs.prodi || '-');
        
        if (!groupedByJurusan[jurusanName]) {
            groupedByJurusan[jurusanName] = { jurusan: jurusanName, prodi: [] };
        }
        if (!groupedByJurusan[jurusanName].prodi.includes(prodiName)) {
            groupedByJurusan[jurusanName].prodi.push(prodiName);
        }
        if (!groupedByJurusan[jurusanName].mahasiswa) {
            groupedByJurusan[jurusanName].mahasiswa = [];
        }
        groupedByJurusan[jurusanName].mahasiswa.push(mhs);
    });
    
    // TTD HTML with QR Code
    const ttdHtml = qrCodeUrl 
        ? `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
           <p style="margin: 0 0 10px 0;">pada tanggal ${tanggalTTD}</p>
           <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
           <div style="text-align: right; margin: 10px 0;">
               <img src="${qrCodeUrl}" alt="QR Code" style="width: 100px; height: 100px; border: 1px solid #000;" 
                    onerror="console.error('Failed to load QR Code'); this.style.border='2px solid red';">
           </div>
           <p style="margin: 10px 0 0 0;">
               <strong><u>${dekanName}</u></strong><br>
               NIP. ${dekanNip}
           </p>`
        : `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
           <p style="margin: 0 0 30px 0;">pada tanggal ${tanggalTTD}</p>
           <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
           <p style="margin: 0 0 0 0;">
               <strong><u>${dekanName}</u></strong><br>
               NIP. ${dekanNip}
           </p>`;
    
    // Lampiran HTML
    let lampiranHtml = '';
    Object.keys(groupedByJurusan).forEach((jurusanName, index) => {
        const jurusanData = groupedByJurusan[jurusanName];
        const mahasiswaProdi = jurusanData.mahasiswa;
        // Use namaProdi from SK data instead of jurusanData
        lampiranHtml += `
            <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                    <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI S1 ${namaProdi} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PEMBIMBING SKRIPSI</p>
                    <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI S1 ${namaProdi} FAKULTAS TEKNIK</p>
                    <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNODJOYO</p>
                    <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
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
                        ${mahasiswaProdi.map((mhs, idx) => `
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
                        ${ttdHtml}
                    </div>
                </div>
            </div>
        `;
    });
    
    const html = `
        <div class="preview-header">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
            <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
            <strong class="line-1">RISET DAN TEKNOLOGI</strong>
            <strong class="line-2">UNIVERSITAS TRUNODJOYO</strong>
            <strong class="line-3">FAKULTAS TEKNIK</strong>
            <div class="address">
                Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                Telp: (031) 3011146, Fax. (031) 3011506<br>
                Laman: www.trunojoyo.ac.id
            </div>
            <div style="clear: both;"></div>
        </div>

        <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 14pt;">
            KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNODJOYO
        </div>

        <div style="text-align: center; margin: 15px 0; font-size: 12pt;">
            NOMOR: ${nomorSurat}
        </div>

        <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
            TENTANG
        </div>

        <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
            PENETAPAN DOSEN PEMBIMBING SKRIPSI<br>
            PROGRAM STUDI S1 ${namaProdi}<br>
            FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO<br>
            SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
        </div>

        <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
            DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
        </div>

        <div style="text-align: justify; margin-bottom: 20px; font-size: 10pt;">
            <table style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 120px; vertical-align: top;"><strong>Menimbang</strong></td>
                    <td style="width: 20px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 30px; vertical-align: top; border: none;">a.</td>
                                <td style="border: none;">Bahwa untuk memperlancar penyusunan Skripsi mahasiswa, perlu menugaskan dosen sebagai pembimbing Skripsi;</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; border: none; padding-top: 5px;">b.</td>
                                <td style="border: none; padding-top: 5px;">Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 120px; vertical-align: top;"><strong>Mengingat</strong></td>
                    <td style="width: 20px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        <ol style="margin: 0; padding-left: 20px;">
                            <li style="margin-bottom: 5px;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                            <li style="margin-bottom: 5px;">Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                            <li style="margin-bottom: 5px;">Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                            <li style="margin-bottom: 5px;">Keputusan RI Nomor 85 tahun 2001, tentang Statuta Universitas Trunodjoyo;</li>
                            <li style="margin-bottom: 5px;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/ U/ 2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                            <li style="margin-bottom: 5px;">Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi RI Nomor 79/M/MPK.A/ KP.09.02/ 2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                            <li>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UNM3/KP/ 2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</li>
                        </ol>
                    </td>
                </tr>
            </table>

            <p><strong>Memperhatikan:</strong> ${Object.keys(groupedByJurusan).map(jurusanName => `Surat dari Ketua Jurusan ${jurusanName} tentang permohonan SK Dosen Pembimbing Skripsi`).join('; ')};</p>

            <div style="text-align: center; margin: 30px 0 20px 0; font-weight: bold;">
                MEMUTUSKAN
            </div>

            <table style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 15%; vertical-align: top; font-weight: bold;">Menetapkan</td>
                    <td style="width: 3%; vertical-align: top;">:</td>
                    <td>PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI S1 ${namaProdi} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                </tr>
            </table>

            <table style="width: 100%; margin-bottom: 10px;">
                <tr>
                    <td style="width: 15%; vertical-align: top; font-weight: bold;">Kesatu</td>
                    <td style="width: 3%; vertical-align: top;">:</td>
                    <td>Dosen Pembimbing Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                </tr>
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="width: 15%; vertical-align: top; font-weight: bold;">Kedua</td>
                    <td style="width: 3%; vertical-align: top;">:</td>
                    <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                </tr>
            </table>
        </div>

        <div style="font-size: 10pt; margin: 40px 0 30px 0; text-align: right;">
            ${ttdHtml}
        </div>

        ${lampiranHtml}
    `;
    
    document.getElementById('previewContent{{ $sk->No }}').innerHTML = html;
}

function printSK{{ $sk->No }}() {
    window.print();
}
@endif
@endforeach
</script>
@endpush

@foreach($skList as $sk)
<div class="modal fade" id="detailModal{{ $sk->No }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $sk->No }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="detailModalLabel{{ $sk->No }}">
                    <i class="fas fa-info-circle me-2"></i>Detail SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- SK Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="fas fa-file-alt me-2 text-warning"></i>Informasi SK</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th width="40%">Nomor SK</th>
                                        <td>: {{ $sk->accSKPembimbingSkripsi->Nomor_Surat ?? 'Belum ada nomor' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>: {{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Semester</th>
                                        <td>: <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">{{ $sk->Semester }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Akademik</th>
                                        <td>: {{ $sk->Tahun_Akademik }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2 text-warning"></i>Status & Timeline</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th width="40%">Status</th>
                                        <td>: 
                                            @php
                                                $badgeClass = 'secondary';
                                                $statusText = $sk->Status;
                                                switch($sk->Status) {
                                                    case 'Dikerjakan admin':
                                                        $badgeClass = 'warning text-dark';
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
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pengajuan</th>
                                        <td>: {{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d F Y H:i') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tenggat Waktu</th>
                                        <td>: {{ $sk->{'Tanggal-Tenggat'} ? $sk->{'Tanggal-Tenggat'}->format('d F Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alasan Tolak jika ada -->
                @if(in_array($sk->Status, ['Ditolak-Admin', 'Ditolak-Wadek1', 'Ditolak-Dekan']) && $sk->{'Alasan-Tolak'})
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Alasan Penolakan</h6>
                            <p class="mb-0">{{ $sk->{'Alasan-Tolak'} }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Data Mahasiswa & Pembimbing -->
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-users me-2 text-warning"></i>Daftar Mahasiswa dan Pembimbing</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="10%">NIM</th>
                                        <th width="20%">Nama Mahasiswa</th>
                                        <th width="25%">Judul Skripsi</th>
                                        <th width="20%">Pembimbing 1</th>
                                        <th width="20%">Pembimbing 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pembimbingData = $sk->Data_Pembimbing_Skripsi;
                                        if (is_string($pembimbingData)) {
                                            $pembimbingData = json_decode($pembimbingData, true);
                                        }
                                    @endphp
                                    @if(is_array($pembimbingData) && count($pembimbingData) > 0)
                                        @foreach($pembimbingData as $idx => $data)
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td>{{ $data['nim'] ?? '-' }}</td>
                                            <td>{{ $data['nama_mahasiswa'] ?? '-' }}</td>
                                            <td><small>{{ $data['judul_skripsi'] ?? '-' }}</small></td>
                                            <td>
                                                @if(isset($data['pembimbing_1']) && is_array($data['pembimbing_1']))
                                                    <strong>{{ $data['pembimbing_1']['nama_dosen'] ?? '-' }}</strong><br>
                                                    <small class="text-muted">NIP: {{ $data['pembimbing_1']['nip'] ?? '-' }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($data['pembimbing_2']) && is_array($data['pembimbing_2']))
                                                    <strong>{{ $data['pembimbing_2']['nama_dosen'] ?? '-' }}</strong><br>
                                                    <small class="text-muted">NIP: {{ $data['pembimbing_2']['nip'] ?? '-' }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data mahasiswa</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if(is_array($pembimbingData) && count($pembimbingData) > 0)
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Total: <strong>{{ count($pembimbingData) }}</strong> mahasiswa
                            </small>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Preview SK Document (Only for Status Selesai) -->
                @if($sk->Status == 'Selesai')
                <div class="card border-0 bg-light mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fas fa-file-alt me-2 text-warning"></i>Preview SK Pembimbing Skripsi</h6>
                        <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; background: white;">
                            <div class="preview-document" id="previewContent{{ $sk->No }}">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Memuat preview SK...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    // Auto-load preview when modal opens
                    document.getElementById('detailModal{{ $sk->No }}').addEventListener('shown.bs.modal', function() {
                        renderPreviewKaprodi{{ $sk->No }}();
                    });
                </script>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                @if($sk->Status == 'Selesai' && $sk->Nomor_Surat)
                <button type="button" class="btn btn-info" onclick="printSK{{ $sk->No }}()">
                    <i class="fas fa-print me-2"></i>Print SK
                </button>
                <a href="{{ route('kaprodi.sk.pembimbing-skripsi.download', $sk->No) }}" class="btn btn-warning" target="_blank">
                    <i class="fas fa-download me-2"></i>Download PDF
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
