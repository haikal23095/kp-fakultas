@extends('layouts.kaprodi')

@section('title', 'Dashboard Kepala Program Studi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard Kepala Program Studi</h1>
        <p class="mb-0 text-muted">Selamat datang, {{ auth()->user()->Name_User ?? 'Kepala Program Studi' }}. Kelola persetujuan surat dan monitor aktivitas prodi Anda.</p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('kaprodi.surat.index') }}" class="text-decoration-none">
            <div class="card border-start border-danger border-4 shadow-sm h-100 hover-shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Surat Masuk</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $suratMasuk ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-inbox fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Surat Keluar</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $suratKeluar ?? 4 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-secondary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Total Arsip Prodi</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalArsip ?? 45 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-archive fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">Antrian Permintaan Pengantar KP/Magang</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Instansi Tujuan</th>
                        <th>Tgl. Masuk</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antrianSurat ?? [] as $surat)
                        @php
                            $mahasiswaList = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                            $namaMahasiswa = $mahasiswaList[0]['nama'] ?? 'N/A';
                            $tanggalMasuk = $surat->tugasSurat?->Tanggal_Diberikan_Tugas_Surat ?? now();
                        @endphp
                        <tr>
                            <td>{{ $namaMahasiswa }}</td>
                            <td>{{ $surat->Nama_Instansi ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($tanggalMasuk)->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm" onclick="showDetail({{ $surat->id_no }})">Detail</button>
                                <button class="btn btn-success btn-sm" onclick="confirmApprove({{ $surat->id_no }}, '{{ $namaMahasiswa }}')">Setujui</button>
                                <button class="btn btn-danger btn-sm" onclick="showRejectModal({{ $surat->id_no }}, '{{ $namaMahasiswa }}')">Tolak</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada antrian surat magang saat ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Modal Detail Surat -->
<div class="modal fade" id="detailSuratModal" tabindex="-1" aria-labelledby="detailSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailSuratModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail Surat Pengantar Magang/KP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailSuratContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadProposalBtn" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-download me-2"></i>Download Proposal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak Surat -->
<div class="modal fade" id="rejectSuratModal" tabindex="-1" aria-labelledby="rejectSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectSuratForm" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectSuratModalLabel">
                        <i class="fas fa-times-circle me-2"></i>Tolak Surat Pengantar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Anda akan menolak pengajuan surat dari <strong id="reject-mahasiswa-name"></strong>
                    </div>
                    <div class="mb-3">
                        <label for="komentar" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="komentar" name="komentar" rows="4" 
                                  placeholder="Tuliskan alasan penolakan (minimal 10 karakter)" 
                                  required minlength="10" maxlength="1000"></textarea>
                        <small class="text-muted">Minimal 10 karakter, maksimal 1000 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Tolak Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Setujui -->
<div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-labelledby="approveConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveConfirmModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Persetujuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-question-circle fa-3x text-success"></i>
                </div>
                <p class="text-center">
                    Apakah Anda yakin ingin menyetujui pengajuan surat dari <br>
                    <strong id="approve-mahasiswa-name"></strong>?
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Surat yang disetujui akan ditandatangani dengan QR Code dan diteruskan ke admin untuk diproses lebih lanjut.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="approveForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Ya, Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card.lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card.lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card.hover-shadow {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }
    .card.hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.35rem 0.75rem rgba(0, 0, 0, 0.12) !important;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
    }
    
    /* Style untuk pratinjau surat */
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #fdfdfd;
        color: #000;
        border: 1px solid #ccc;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 12pt;
        line-height: 1.5;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 10px;
        border-bottom: 3px double #000;
        padding-bottom: 5px;
    }
    .preview-header img {
        width: 120px;
        float: left;
        margin-top: -12px;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
    }
    .preview-header .line-1 { font-size: 13pt; }
    .preview-header .line-2 { font-size: 15pt; }
    .preview-header .line-3 { font-size: 15pt; }
    .preview-header .address {
        font-size: 10pt;
        font-style: italic;
        margin-top: 5px;
    }
    .preview-title {
        font-weight: bold;
        font-size: 14pt;
        margin-top: 25px;
        text-align: center;
        text-decoration: underline;
    }
    .preview-table {
        margin-top: 15px;
        width: 100%;
        font-size: 12pt;
    }
    .preview-table td {
        padding: 2px 0px;
        vertical-align: top;
    }
    .preview-table td:nth-child(1) { width: 30%; }
    .preview-table td:nth-child(2) { width: 2%; }
    .preview-table td:nth-child(3) {
        width: 68%;
        word-break: break-word;
    }
    .preview-magang-section {
        margin-top: 10px;
    }
    .preview-signature {
        font-size: 12pt;
        margin-top: 30px;
    }
</style>
@endpush

@push('scripts')
<script>
function showDetail(suratId) {
    const modal = new bootstrap.Modal(document.getElementById('detailSuratModal'));
    const content = document.getElementById('detailSuratContent');
    const downloadBtn = document.getElementById('downloadProposalBtn');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Memuat data...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch detail surat
    fetch(`/kaprodi/permintaan-kp/${suratId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                
                // Build mahasiswa list
                let mahasiswaHtml = '';
                if (data.mahasiswa && data.mahasiswa.length > 0) {
                    data.mahasiswa.forEach((mhs, index) => {
                        mahasiswaHtml += `
                            <div style="margin-bottom: 10px;">
                                <strong>${index + 1}. ${mhs.nama}</strong><br>
                                <small>NIM: ${mhs.nim} | Angkatan: ${mhs.angkatan || '-'}</small>
                            </div>
                        `;
                    });
                } else {
                    mahasiswaHtml = '<span style="color: #999; font-style: italic;">-</span>';
                }
                
                // Build dosen pembimbing list
                let dosenHtml = '';
                if (data.dosen_pembimbing && data.dosen_pembimbing.length > 0) {
                    data.dosen_pembimbing.forEach((dosen, index) => {
                        if (index === 0) {
                            dosenHtml = `${dosen.nama_dosen} (NIP: ${dosen.nip})`;
                        }
                    });
                } else {
                    dosenHtml = '-';
                }
                
                // Dosen Pembimbing 2
                let dosen2Html = '-';
                if (data.dosen_pembimbing && data.dosen_pembimbing.length > 1) {
                    const dosen2 = data.dosen_pembimbing[1];
                    dosen2Html = `${dosen2.nama_dosen} (NIP: ${dosen2.nip})`;
                }
                
                // Format tanggal
                const formatDate = (dateString) => {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', { 
                        day: 'numeric', 
                        month: 'long', 
                        year: 'numeric' 
                    });
                };
                
                // Jangka waktu
                const jangkaWaktu = data.tanggal_mulai && data.tanggal_selesai 
                    ? `${formatDate(data.tanggal_mulai)} s/d ${formatDate(data.tanggal_selesai)}`
                    : '-';
                
                // Nama mahasiswa pertama untuk TTD
                const namaMahasiswaTTD = data.mahasiswa && data.mahasiswa.length > 0 
                    ? data.mahasiswa[0].nama 
                    : '-';
                const nimMahasiswaTTD = data.mahasiswa && data.mahasiswa.length > 0 
                    ? data.mahasiswa[0].nim 
                    : '-';
                
                // Get current date for Bangkalan
                const today = new Date();
                const tanggalBangkalan = formatDate(today);
                
                // Get koordinator name from auth user or data
                const koordinatorName = '{{ auth()->user()->dosen->Nama_Dosen ?? auth()->user()->pegawai->Nama_Pegawai ?? "Koordinator KP/TA" }}';
                const koordinatorNIP = '{{ auth()->user()->dosen->NIP ?? auth()->user()->pegawai->NIP ?? "-" }}';
                
                // Display content with preview document style
                content.innerHTML = `
                    <div class="preview-document">
                        <div class="preview-header">
                            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas Trunojoyo Madura">
                            <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                            <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
                            <strong class="line-3">FAKULTAS TEKNIK</strong>
                            <div class="address">
                                Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
                            </div>
                            <div style="clear: both;"></div>
                        </div>

                        <p class="preview-title">FORM PENGAJUAN SURAT PENGANTAR</p>

                        <table class="preview-table">
                            <tr>
                                <td style="vertical-align: top;">Nama</td>
                                <td style="vertical-align: top;">:</td>
                                <td>${mahasiswaHtml}</td>
                            </tr>
                            <tr>
                                <td>Jurusan</td>
                                <td>:</td>
                                <td>${data.prodi}</td>
                            </tr>
                            <tr>
                                <td>Dosen Pembimbing</td>
                                <td>:</td>
                                <td>${dosenHtml}</td>
                            </tr>
                            <tr>
                                <td>Dosen Pembimbing 2</td>
                                <td>:</td>
                                <td>${dosen2Html}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Surat Pengantar*</td>
                                <td style="vertical-align: top;">:</td>
                                <td>
                                    1. Pengantar Kerja Praktek<br>
                                    2. Pengantar TA<br>
                                    3. Pengantar Dosen Pembimbing I TA<br>
                                    4. Magang
                                </td>
                            </tr>
                            <tr>
                                <td>Instansi/Perusahaan</td>
                                <td>:</td>
                                <td>${data.nama_instansi || '-'}<br><small>${data.alamat_instansi || ''}</small></td>
                            </tr>
                        </table>

                        <div class="preview-magang-section">
                            <strong><u>Isian berikut utk pengantar Magang</u></strong>
                            <table class="preview-table" style="margin-top: 0;">
                                <tr>
                                    <td>Judul Penelitian</td>
                                    <td>:</td>
                                    <td>${data.judul_penelitian || '-'}</td>
                                </tr>
                                <tr>
                                    <td>Jangka waktu penelitian</td>
                                    <td>:</td>
                                    <td>${jangkaWaktu}</td>
                                </tr>
                                <tr>
                                    <td>Identitas Surat Balasan**</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>

                        <div class="preview-signature">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1">Menyetujui<br>Koordinator KP/TA</p>
                                    <div style="height: 70px;"></div>
                                    <p class="mb-0">( ${koordinatorName} )</p>
                                    <p>NIP. ${koordinatorNIP}</p>
                                </div>
                                <div class="col-6 text-center">
                                    <p class="mb-1">Bangkalan, ${tanggalBangkalan}</p>
                                    <p class="mb-1">Pemohon</p>
                                    ${data.foto_ttd ? `<img src="/storage/${data.foto_ttd}" alt="Tanda Tangan" style="max-height: 80px; max-width: 200px; display: block; margin: 10px auto;">` : '<div style="height: 70px;"></div>'}
                                    <p class="mb-0">( ${namaMahasiswaTTD} )</p>
                                    <p class="mt-1">NIM. ${nimMahasiswaTTD}</p>
                                </div>
                            </div>
                        </div>
                        <hr style="border-top: 1px dashed #000; margin-top: 15px;">
                        <small style="font-size: 10pt;">
                            Cat: *Tulis alamat Instansi/perusahaan yg dituju<br>
                            **Diisi untuk permohonan kedua dan seterusnya
                        </small>
                    </div>
                `;
                
                // Show/hide download button
                if (data.dokumen_proposal) {
                    downloadBtn.style.display = 'inline-block';
                    downloadBtn.href = `/kaprodi/permintaan-kp/${data.id_no}/download-proposal`;
                } else {
                    downloadBtn.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat data. Silakan coba lagi.
                </div>
            `;
        });
}

function confirmApprove(suratId, mahasiswaName) {
    const modal = new bootstrap.Modal(document.getElementById('approveConfirmModal'));
    document.getElementById('approve-mahasiswa-name').textContent = mahasiswaName;
    const form = document.getElementById('approveForm');
    form.action = `/kaprodi/permintaan-kp/${suratId}/approve`;
    
    // Set session untuk redirect ke dashboard
    fetch('/kaprodi/set-redirect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ redirect_to: 'dashboard' })
    });
    
    modal.show();
}

function showRejectModal(suratId, mahasiswaName) {
    const modal = new bootstrap.Modal(document.getElementById('rejectSuratModal'));
    document.getElementById('reject-mahasiswa-name').textContent = mahasiswaName;
    document.getElementById('rejectSuratForm').action = `/kaprodi/permintaan-kp/${suratId}/reject`;
    document.getElementById('komentar').value = '';
    modal.show();
}
</script>
@endpush

@endsection