@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Beban Mengajar')

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
</style>
@endpush

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">Riwayat SK Beban Mengajar</li>
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
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Riwayat SK Beban Mengajar</h5>
                            <small>Daftar pengajuan SK beban mengajar yang telah diajukan</small>
                        </div>
                    </div>
                    <a href="{{ route('kaprodi.sk.beban-mengajar.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filter Section -->
                <form method="GET" action="{{ route('kaprodi.sk.beban-mengajar.index') }}">
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
                            <button type="submit" class="btn btn-primary w-100">
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
                                <th width="10%" class="text-center">Jumlah Dosen</th>
                                <th width="12%">Tanggal Ajuan</th>
                                <th width="12%" class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skList as $index => $sk)
                            <tr>
                                @php
                                    $bebanData = $sk->Data_Beban_Mengajar;
                                    if (is_string($bebanData)) {
                                        $bebanData = json_decode($bebanData, true);
                                    }
                                    $jumlahDosen = is_array($bebanData) ? count($bebanData) : 0;
                                @endphp
                                <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->Nomor_Surat)
                                        <strong class="text-primary">{{ $sk->Nomor_Surat }}</strong>
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
                                        <i class="fas fa-chalkboard-teacher me-1"></i>{{ $jumlahDosen }}
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
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info" onclick="lihatDetail({{ $sk->No }})" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($sk->Status == 'Selesai' && $sk->Nomor_Surat)
                                        <button type="button" class="btn btn-success" onclick="downloadSK({{ $sk->No }})" title="Download SK">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat SK Beban Mengajar</p>
                                    <a href="{{ route('kaprodi.sk.beban-mengajar.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($skList->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $skList->firstItem() }} - {{ $skList->lastItem() }} dari {{ $skList->total() }} data
                    </div>
                    {{ $skList->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail SK -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function lihatDetail(skId) {
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
        
        // Load detail via AJAX
        fetch(`/kaprodi/sk/beban-mengajar/${skId}/detail`)
            .then(response => response.json())
            .then(data => {
                console.log('SK Data received:', data); // Debug: lihat data yang diterima
                console.log('QR_Code exists:', data.sk && data.sk.QR_Code ? 'YES' : 'NO'); // Debug: cek QR Code
                if (data.success) {
                    displayDetail(data.sk);
                } else {
                    document.getElementById('modalDetailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.message || 'Gagal memuat data'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Terjadi kesalahan saat memuat data
                    </div>
                `;
            });
    }
    
    function displayDetail(sk) {
        let bebanData = sk.Data_Beban_Mengajar;
        if (typeof bebanData === 'string') {
            bebanData = JSON.parse(bebanData);
        }

        // Group data by prodi
        const groupedByProdi = {};
        if (Array.isArray(bebanData)) {
            bebanData.forEach(item => {
                const prodiName = item.Nama_Prodi || item.prodi || 'Tidak Diketahui';
                if (!groupedByProdi[prodiName]) {
                    groupedByProdi[prodiName] = [];
                }
                groupedByProdi[prodiName].push(item);
            });
        }

        const semesterUpper = sk.Semester ? sk.Semester.toUpperCase() : 'GANJIL';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';
        const dekanName = sk.dekan ? sk.dekan.Nama_Dosen : 'Nama Dekan';
        const dekanNip = sk.dekan ? sk.dekan.NIP : '-';

        let html = `
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
                        NOMOR ${nomorSurat}
                    </div>

                    <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                        TENTANG
                    </div>

                    <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                        BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK<br>
                        UNIVERSITAS TRUNOJOYO MADURA<br>
                        SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
                    </div>

                    <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                        DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
                    </div>

                    <!-- Content Preview -->
                    <div class="preview-content">
                        <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
                        <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                            <tr>
                                <td style="width: 10%; vertical-align: top;">:</td>
                                <td style="width: 5%; vertical-align: top;">a.</td>
                                <td style="text-align: justify;">bahwa untuk kelancaran perkuliahan Program S1 di Fakultas Teknik Universitas Trunojoyo Madura, maka perlu menetapkan beban mengajar dosen;</td>
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
                                <td style="text-align: justify;">Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang pengangkatan Rektor Universitas Trunojoyo Madura;</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="vertical-align: top;">6.</td>
                                <td style="text-align: justify;">Keputusan Rektor Universitas Trunojoyo Madura tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunojoyo Madura;</td>
                            </tr>
                        </table>

                        <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
                        <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                            <tr>
                                <td style="width: 10%; vertical-align: top;">:</td>
                                <td style="width: 5%; vertical-align: top;">1.</td>
                                <td style="text-align: justify;">Keputusan Rektor Universitas Trunojoyo Madura tentang Buku Pedoman Akademik Universitas Trunojoyo Madura;</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="vertical-align: top;">2.</td>
                                <td style="text-align: justify;">Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Beban Mengajar ${semesterUpper} ${tahunAkademik};</td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                            MEMUTUSKAN :
                        </div>

                        <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                            <tr>
                                <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                                <td style="width: 3%; vertical-align: top;">:</td>
                                <td style="text-align: justify; font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                            </tr>
                        </table>
                        <table style="width: 100%; margin-bottom: 10px; font-size: 10pt;">
                            <tr>
                                <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                                <td style="width: 3%; vertical-align: top;">:</td>
                                <td style="text-align: justify;">Beban mengajar dosen Program Studi S1 di lingkungan Fakultas Teknik Universitas Trunojoyo Madura Semester ${semesterUpper} Tahun Akademik ${tahunAkademik} sebagaimana terlampir dalam surat keputusan ini.</td>
                            </tr>
                        </table>

                        <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                            <tr>
                                <td style="width: 15%; vertical-align: top; font-weight: normal;">Kedua</td>
                                <td style="width: 3%; vertical-align: top;">:</td>
                                <td style="text-align: justify;">Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Tanda Tangan Dekan -->
                    <div class="preview-signature" style="font-size: 10pt; margin: 40px 0 30px 0;">
                        <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                        <p style="margin-bottom: 3px;">pada tanggal ${sk['Tanggal-Persetujuan-Dekan'] ? new Date(sk['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                        <p style="margin-bottom: ${sk.QR_Code ? '10px' : '70px'};"><strong>DEKAN,</strong></p>
                        ${sk.QR_Code ? `<img src="data:image/png;base64,${sk.QR_Code}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0;">` : ''}
                        <p style="margin-bottom: 0;">
                            <strong><u>${dekanName}</u></strong><br>
                            NIP. ${dekanNip}
                        </p>
                    </div>

                    <!-- Lampiran per prodi -->
        `;

        // Display beban mengajar grouped by prodi
        Object.keys(groupedByProdi).forEach((prodiName, index) => {
            const items = groupedByProdi[prodiName];
            html += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN ${index + 1} KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 13px 0; text-align: center; font-weight: bold;">BEBAN MENGAJAR DOSEN ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
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
            `;

            items.forEach((item, idx) => {
                const mataKuliah = item.nama_mata_kuliah || item.mata_kuliah || item.Nama_Matakuliah || item.Nama_MK || item['nama-mata-kuliah'] || '-';
                const kelas = item.kelas || item.Kelas || '-';
                const sks = item.sks || item.SKS || 0;
                const namaDosen = item.nama_dosen || item.Nama_Dosen || '-';
                const nip = item.nip || item.NIP || '-';
                
                html += `
                    <tr>
                        <td>${idx + 1}.</td>
                        <td>${namaDosen}<br><small>${nip}</small></td>
                        <td>${mataKuliah}</td>
                        <td>${kelas}</td>
                        <td>${sks}</td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 3px 0;">pada tanggal ${sk['Tanggal-Persetujuan-Dekan'] ? new Date(sk['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                            <p style="margin: 0 0 ${sk.QR_Code ? '10px' : '70px'} 0;"><strong>DEKAN,</strong></p>
                            ${sk.QR_Code ? `<img src="data:image/png;base64,${sk.QR_Code}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0;">` : ''}
                            <p style="margin: 0 0 0 0;">
                                <strong><u>${dekanName}</u></strong><br>
                                NIP. ${dekanNip}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        document.getElementById('modalDetailContent').innerHTML = html;
    }
    
    function downloadSK(skId) {
        window.open(`/kaprodi/sk/beban-mengajar/${skId}/download`, '_blank');
    }
</script>
@endpush

@endsection
