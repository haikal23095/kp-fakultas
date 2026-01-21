@extends('layouts.dekan')

@section('title', 'SK Beban Mengajar - Dekan')

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
        <h1 class="h3 fw-bold mb-0">Daftar SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">SK Beban Mengajar yang menunggu persetujuan dan penandatanganan Dekan.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-warning" onclick="showHistory()">
            <i class="fas fa-history me-1"></i> History
        </button>
        <a href="{{ route('dekan.persetujuan.sk_dosen') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Filter Semester</label>
                <select class="form-select" id="filterSemester" onchange="applyFilter()">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">SK Beban Mengajar</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Semester</th>
                        <th>Tahun Ajaran</th>
                        <th>Nomor Surat</th>
                        <th>Jumlah Dosen</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSK as $index => $sk)
                        @php
                            $bebanData = $sk->Data_Beban_Mengajar;
                            if (is_string($bebanData)) {
                                $bebanData = json_decode($bebanData, true);
                            }
                            $jumlahDosen = is_array($bebanData) ? count($bebanData) : 0;
                        @endphp
                        <tr>
                            <td>{{ $daftarSK->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-{{ $sk->Semester === 'Ganjil' ? 'primary' : 'info' }}">
                                    {{ $sk->Semester }}
                                </span>
                            </td>
                            <td>{{ $sk->Tahun_Akademik }}</td>
                            <td>{{ $sk->Nomor_Surat ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $jumlahDosen }} Dosen</span>
                            </td>
                            <td>
                                {{ isset($sk->{'Tanggal-Pengajuan'}) ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($sk->Status) {
                                        'Menunggu-Persetujuan-Dekan' => 'warning',
                                        'Selesai' => 'success',
                                        'Ditolak-Dekan' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $sk->Status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="showDetail({{ $sk->No }})" 
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($sk->Status === 'Menunggu-Persetujuan-Dekan')
                                        <button type="button" 
                                                class="btn btn-success" 
                                                onclick="approveSK({{ $sk->No }})" 
                                                title="Setujui dan Tandatangani">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="showRejectModal({{ $sk->No }}, '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}')" 
                                                title="Tolak">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada SK Beban Mengajar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($daftarSK->hasPages())
    <div class="card-footer bg-white">
        {{ $daftarSK->links() }}
    </div>
    @endif
</div>

<!-- Modal Detail SK -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Preview SK Beban Mengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject SK -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRejectLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    <strong>SK Beban Mengajar:</strong><br>
                    <span id="rejectSKInfo"></span>
                </p>
                
                <div class="mb-3">
                    <label for="rejectTarget" class="form-label">Kirim Penolakan Ke:</label>
                    <select class="form-select" id="rejectTarget" required>
                        <option value="">Pilih tujuan...</option>
                        <option value="admin">Admin Fakultas</option>
                        <option value="kaprodi">Kaprodi</option>
                    </select>
                    <div class="form-text">Pilih siapa yang akan menerima notifikasi penolakan</div>
                </div>

                <div class="mb-3">
                    <label for="rejectReason" class="form-label">Alasan Penolakan:</label>
                    <textarea class="form-control" id="rejectReason" rows="4" required 
                              placeholder="Masukkan alasan penolakan SK ini..."></textarea>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> SK akan dikembalikan ke pihak yang dipilih untuk diperbaiki.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal History -->
<div class="modal fade" id="modalHistory" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoryLabel">
                    <i class="fas fa-history me-2"></i>History SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSKId = null;

function applyFilter() {
    const semester = document.getElementById('filterSemester').value;
    
    const url = new URL(window.location.href);
    
    if (semester) {
        url.searchParams.set('semester', semester);
    } else {
        url.searchParams.delete('semester');
    }
    
    window.location.href = url.toString();
}

function showDetail(skId) {
    currentSKId = skId;
    
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
    
    // Show loading
    document.getElementById('modalDetailBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data...</p>
        </div>
    `;
    
    // Fetch detail data
    const detailUrl = '/dekan/sk-beban-mengajar/' + skId;
    console.log('Fetching URL:', detailUrl); // Debug URL
    fetch(detailUrl)
        .then(response => response.json())
        .then(data => {
            console.log('SK Data:', data); // Debug: lihat data yang diterima
            if (data.success) {
                displayDetail(data.sk, data.dekan);
            } else {
                document.getElementById('modalDetailBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>${data.message || 'Gagal memuat data SK'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalDetailBody').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat data
                </div>
            `;
        });
}

function displayDetail(sk, dekan) {
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
    const dekanName = dekan.nama || 'Nama Dekan';
    const dekanNip = dekan.nip || '-';

    let html = `
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
                    NOMOR ${nomorSurat}
                </div>

                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                    TENTANG
                </div>

                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                    BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK<br>
                    UNIVERSITAS TRUNODJOYO<br>
                    SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
                </div>

                <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                    DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
                </div>

                <!-- Content Preview -->
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
                            <td style="text-align: justify;">Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang pengangkatan Rektor Universitas Trunodjoyo;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="vertical-align: top;">6.</td>
                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunodjoyo tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunodjoyo;</td>
                        </tr>
                    </table>

                    <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
                    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                        <tr>
                            <td style="width: 10%; vertical-align: top;">:</td>
                            <td style="width: 5%; vertical-align: top;">1.</td>
                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunodjoyo tentang Buku Pedoman Akademik Universitas Trunodjoyo;</td>
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
                            <td style="text-align: justify; font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                        </tr>
                    </table>
                    <table style="width: 100%; margin-bottom: 10px; font-size: 10pt;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify;">Beban mengajar dosen Program Studi S1 di lingkungan Fakultas Teknik Universitas Trunodjoyo Semester ${semesterUpper} Tahun Akademik ${tahunAkademik} sebagaimana terlampir dalam surat keputusan ini.</td>
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
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN ${index + 1} KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                    <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                    <p style="margin: 0 0 13px 0; text-align: center; font-weight: bold;">BEBAN MENGAJAR DOSEN ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
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

    document.getElementById('modalDetailBody').innerHTML = html;
}

function approveSK(skId) {
    if (!confirm('Apakah Anda yakin ingin menyetujui dan menandatangani SK Beban Mengajar ini? SK akan ditandatangani dengan QR Code.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/dekan/sk-beban-mengajar/${skId}/approve`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}

function showRejectModal(skId, semester, tahunAjaran) {
    currentSKId = skId;
    document.getElementById('rejectSKInfo').textContent = `Semester ${semester} - Tahun Ajaran ${tahunAjaran}`;
    document.getElementById('rejectTarget').value = '';
    document.getElementById('rejectReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalReject'));
    modal.show();
}

function submitRejection() {
    const target = document.getElementById('rejectTarget').value;
    const reason = document.getElementById('rejectReason').value.trim();
    
    if (!target) {
        alert('Silakan pilih tujuan penolakan');
        return;
    }
    
    if (!reason) {
        alert('Silakan masukkan alasan penolakan');
        return;
    }
    
    // Get CSRF token with fallback
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
    
    fetch(`/dekan/sk-beban-mengajar/${currentSKId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            target: target,
            alasan_penolakan: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('SK berhasil ditolak');
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menolak SK');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menolak SK');
    });
}

function showHistory() {
    const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
    modal.show();
    
    fetch('{{ route('dekan.sk.beban-mengajar.history') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderHistory(data.history);
            } else {
                document.getElementById('historyContent').innerHTML = `
                    <div class="alert alert-danger">${data.message || 'Gagal memuat history'}</div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('historyContent').innerHTML = `
                <div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>
            `;
        });
}

function renderHistory(history) {
    if (!history || history.length === 0) {
        document.getElementById('historyContent').innerHTML = `
            <div class="alert alert-info">Belum ada history SK yang diproses.</div>
        `;
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Semester/Tahun</th>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
    `;

    history.forEach((sk, index) => {
        const date = sk['Tanggal-Persetujuan-Dekan'] ? new Date(sk['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }) : '-';
        
        const statusClass = sk.Status === 'Selesai' ? 'success' : 'danger';

        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${sk.Semester} ${sk.Tahun_Akademik}</td>
                <td>${sk.Nomor_Surat || '-'}</td>
                <td><span class="badge bg-${statusClass}">${sk.Status}</span></td>
                <td>${date}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('historyContent').innerHTML = html;
}
</script>
@endpush
