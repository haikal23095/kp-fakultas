@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Penguji Skripsi')

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
</style>
@endpush

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">Riwayat SK Penguji Skripsi</li>
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
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Riwayat SK Penguji Skripsi</h5>
                            <small>Daftar pengajuan SK penguji skripsi yang telah diajukan</small>
                        </div>
                    </div>
                    <a href="{{ route('kaprodi.sk.penguji-skripsi.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filter Section -->
                <form method="GET" action="{{ route('kaprodi.sk.penguji-skripsi.history') }}">
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
                            <input type="text" class="form-control" name="tahun_akademik" placeholder="Contoh: 23/24" value="{{ request('tahun_akademik') }}">
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
                            <button type="submit" class="btn btn-danger w-100">
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
                                    $pengujiData = $sk->Data_Penguji_Skripsi;
                                    // Sudah di-cast ke array di model, tapi jaga-jaga jika null
                                    $jumlahMahasiswa = is_array($pengujiData) ? count($pengujiData) : 0;
                                @endphp
                                <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->accSKPengujiSkripsi && $sk->accSKPengujiSkripsi->Nomor_Surat)
                                        <strong class="text-danger">{{ $sk->accSKPengujiSkripsi->Nomor_Surat }}</strong>
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
                                    <small>{{ isset($sk->{'Tanggal-Pengajuan'}) ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}</small>
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
                                            case 'Ditolak-Wadek1':
                                            case 'Ditolak-Dekan':
                                                $badgeClass = 'danger';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $finalDataPenguji = $sk->Data_Penguji_Skripsi ?? [];
                                        if ($sk->accSKPengujiSkripsi && $sk->accSKPengujiSkripsi->Data_Penguji_Skripsi) {
                                            $finalDataPenguji = $sk->accSKPengujiSkripsi->Data_Penguji_Skripsi;
                                        }
                                        
                                        $dekanName = '-';
                                        $dekanNip = '-';
                                        $qrCode = null;
                                        
                                        if ($sk->accSKPengujiSkripsi) {
                                            $qrCode = $sk->accSKPengujiSkripsi->QR_Code;
                                            if ($sk->accSKPengujiSkripsi->dekan) {
                                                $dekanName = $sk->accSKPengujiSkripsi->dekan->Nama_Dosen;
                                                $dekanNip = $sk->accSKPengujiSkripsi->dekan->NIP;
                                            }
                                        }
                                    @endphp
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-danger btn-detail" 
                                            title="Detail"
                                            data-id="{{ $sk->No }}"
                                            data-nomor="{{ $sk->accSKPengujiSkripsi->Nomor_Surat ?? 'Belum ada nomor' }}"
                                            data-prodi="{{ $sk->prodi->Nama_Prodi ?? '-' }}"
                                            data-semester="{{ $sk->Semester }}"
                                            data-tahun="{{ $sk->Tahun_Akademik }}"
                                            data-status="{{ $sk->Status }}"
                                            data-tanggal="{{ $sk->{'Tanggal-Pengajuan'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}"
                                            data-tenggat="{{ $sk->{'Tanggal-Tenggat'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Tenggat'})->format('d M Y') : '-' }}"
                                            data-alasan="{{ $sk->{'Alasan-Tolak'} ?? '-' }}"
                                            data-penguji='@json($finalDataPenguji)'
                                            data-dekan-name="{{ $dekanName }}"
                                            data-dekan-nip="{{ $dekanNip }}"
                                            data-qr-code="{{ $qrCode ? asset('storage/' . $qrCode) : '' }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($sk->Status == 'Selesai')
                                        <a href="{{ route('kaprodi.sk.penguji-skripsi.download', $sk->No) }}" 
                                           class="btn btn-sm btn-outline-success" 
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
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat pengajuan SK Penguji Skripsi.</td>
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="fw-semibold">Nomor SK</td>
                                <td id="detail-nomor">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Program Studi</td>
                                <td id="detail-prodi">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Semester</td>
                                <td id="detail-semester">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tahun Akademik</td>
                                <td id="detail-tahun">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="fw-semibold">Status</td>
                                <td id="detail-status">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Pengajuan</td>
                                <td id="detail-tanggal">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Tenggat</td>
                                <td id="detail-tenggat">-</td>
                            </tr>
                            <tr id="alasan-tolak-row" style="display: none;">
                                <td class="fw-semibold">Alasan Ditolak</td>
                                <td id="detail-alasan" class="text-danger">-</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold border-bottom pb-2">
                        <i class="fas fa-user-check me-2 text-danger"></i>Daftar Mahasiswa & Penguji
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="12%">NIM</th>
                                    <th width="23%">Nama Mahasiswa</th>
                                    <th width="20%">Penguji 1</th>
                                    <th width="20%">Penguji 2</th>
                                    <th width="20%">Penguji 3</th>
                                </tr>
                            </thead>
                            <tbody id="detail-penguji-list">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold border-bottom pb-2">
                        <i class="fas fa-file-pdf me-2 text-danger"></i>Preview SK Penguji Skripsi
                    </h6>
                    <div style="max-height: 800px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; background: #f8f9fa; padding: 20px;">
                        <div class="preview-document" id="preview-content">
                            <!-- Konten SK akan di-generate melalui JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="download-btn-container"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailModal = document.getElementById('detailModal');
    
    detailModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        
        // Populate basic information
        document.getElementById('detail-nomor').textContent = button.dataset.nomor;
        document.getElementById('detail-prodi').textContent = button.dataset.prodi;
        document.getElementById('detail-semester').innerHTML = '<span class="badge bg-' + 
            (button.dataset.semester === 'Ganjil' ? 'primary' : 'info') + '">' + 
            button.dataset.semester + '</span>';
        document.getElementById('detail-tahun').textContent = button.dataset.tahun;
        document.getElementById('detail-tanggal').textContent = button.dataset.tanggal;
        document.getElementById('detail-tenggat').textContent = button.dataset.tenggat;
        
        // Status badge
        const status = button.dataset.status;
        let badgeClass = 'secondary';
        let statusText = status;
        
        switch(status) {
            case 'Dikerjakan admin':
                badgeClass = 'warning text-dark';
                break;
            case 'Menunggu-Persetujuan-Wadek-1':
                badgeClass = 'info';
                statusText = 'Menunggu Wadek 1';
                break;
            case 'Menunggu-Persetujuan-Dekan':
                badgeClass = 'primary';
                statusText = 'Menunggu Dekan';
                break;
            case 'Selesai':
                badgeClass = 'success';
                break;
            case 'Ditolak-Admin':
            case 'Ditolak-Wadek1':
            case 'Ditolak-Dekan':
                badgeClass = 'danger';
                break;
        }
        
        document.getElementById('detail-status').innerHTML = '<span class="badge bg-' + badgeClass + '">' + statusText + '</span>';
        
        // Handle Download Button in Modal
        const downloadContainer = document.getElementById('download-btn-container');
        if (status === 'Selesai') {
            const downloadUrl = "{{ route('kaprodi.sk.penguji-skripsi.download', ':id') }}".replace(':id', button.dataset.id);
            downloadContainer.innerHTML = `
                <a href="${downloadUrl}" target="_blank" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Download PDF
                </a>
            `;
        } else {
            downloadContainer.innerHTML = '';
        }

        // Show/hide alasan tolak
        if (status.includes('Ditolak')) {
            document.getElementById('alasan-tolak-row').style.display = '';
            document.getElementById('detail-alasan').textContent = button.dataset.alasan;
        } else {
            document.getElementById('alasan-tolak-row').style.display = 'none';
        }
        
        // Populate penguji list
        const pengujiData = JSON.parse(button.dataset.penguji);
        const pengujiList = document.getElementById('detail-penguji-list');
        pengujiList.innerHTML = '';
        
        if (pengujiData && pengujiData.length > 0) {
            pengujiData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td>${item.nim || '-'}</td>
                    <td>${item.nama_mahasiswa || '-'}</td>
                    <td><small>${item.nama_penguji_1 || '-'}</small></td>
                    <td><small>${item.nama_penguji_2 || '-'}</small></td>
                    <td><small>${item.nama_penguji_3 || '-'}</small></td>
                `;
                pengujiList.appendChild(row);
            });
            
            // Render SK Preview
            renderSKPreview({
                nomor: button.dataset.nomor,
                prodi: button.dataset.prodi,
                semester: button.dataset.semester,
                tahun: button.dataset.tahun,
                penguji: pengujiData,
                dekanName: button.dataset.dekanName,
                dekanNip: button.dataset.dekanNip,
                qrCode: button.dataset.qrCode
            });
        } else {
            pengujiList.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data mahasiswa</td></tr>';
            document.getElementById('preview-content').innerHTML = '<div class="text-center py-4 text-muted">Tidak ada data untuk preview</div>';
        }
    });

    function renderSKPreview(data) {
        const previewContent = document.getElementById('preview-content');
        const { nomor, prodi, semester, tahun, penguji, dekanName, dekanNip, qrCode } = data;
        const semesterUpper = semester.toUpperCase();
        const logoUrl = "{{ asset('images/logo_unijoyo.png') }}";
        
        let dataPenguji = penguji || [];
        const groupedByJurusan = {};
        dataPenguji.forEach(mhs => {
            let pName = mhs.prodi || mhs.nama_prodi || prodi || '-';
            if (!groupedByJurusan[pName]) {
                groupedByJurusan[pName] = { prodi: pName, mahasiswa: [] };
            }
            groupedByJurusan[pName].mahasiswa.push(mhs);
        });

        const ttdHtml = qrCode 
            ? `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 10px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
               <div style="text-align: right; margin: 10px 0;">
                   <img src="${qrCode}" alt="QR Code" style="width: 100px; height: 100px; border: 1px solid #000;">
               </div>
               <p style="margin: 10px 0 0 0;">
                   <strong><u>${dekanName}</u></strong><br>
                   NIP. ${dekanNip}
               </p>`
            : `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 30px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
               <p style="margin: 0 0 0 0;">
                   <strong><u>${dekanName}</u></strong><br>
                   NIP. ${dekanNip}
               </p>`;

        let lampiranHtml = '';
        Object.keys(groupedByJurusan).forEach((pName, index) => {
            const mhsList = groupedByJurusan[pName].mahasiswa;
            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomor}</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PENGUJI SKRIPSI PROGRAM STUDI ${pName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahun}</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PENGUJI SKRIPSI</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${pName.toUpperCase()} FAKULTAS TEKNIK</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNOJOYO MADURA</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahun}</p>
                    </div>
                    <table class="preview-table-mahasiswa">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 10%">NIM</th>
                                <th style="width: 20%">Nama Mahasiswa</th>
                                <th style="width: 20%">Judul Skripsi</th>
                                <th style="width: 15%">Penguji 1</th>
                                <th style="width: 15%">Penguji 2</th>
                                <th style="width: 15%">Penguji 3</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${mhsList.map((mhs, idx) => `
                                <tr>
                                    <td style="text-align: center;">${idx + 1}</td>
                                    <td style="text-align: center;">${mhs.nim || '-'}</td>
                                    <td>${mhs.nama_mahasiswa || '-'}</td>
                                    <td style="font-size: 9pt;">${mhs.judul_skripsi || '-'}</td>
                                    <td style="font-size: 9pt;">${mhs.nama_penguji_1 || '-'}${mhs.nip_penguji_1 ? '<br><small>NIP: '+mhs.nip_penguji_1+'</small>' : ''}</td>
                                    <td style="font-size: 9pt;">${mhs.nama_penguji_2 || '-'}${mhs.nip_penguji_2 ? '<br><small>NIP: '+mhs.nip_penguji_2+'</small>' : ''}</td>
                                    <td style="font-size: 9pt;">${mhs.nama_penguji_3 || '-'}${mhs.nip_penguji_3 ? '<br><small>NIP: '+mhs.nip_penguji_3+'</small>' : ''}</td>
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
                <img src="${logoUrl}" alt="Logo UTM">
                <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
                <strong class="line-1">RISET DAN TEKNOLOGI</strong>
                <strong class="line-2">UNIVERSITAS TRUNODJOYO MADURA</strong>
                <strong class="line-3">FAKULTAS TEKNIK</strong>
                <div class="address">
                    Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                    Telp: (031) 3011146, Fax. (031) 3011506<br>
                    Laman: www.trunojoyo.ac.id
                </div>
                <div style="clear: both;"></div>
            </div>

            <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 12pt;">
                KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNOJOYO MADURA
            </div>

            <div style="text-align: center; margin: 15px 0; font-size: 12pt;">
                NOMOR: ${nomor}
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                TENTANG
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                PENETAPAN DOSEN PENGUJI SKRIPSI<br>
                PROGRAM STUDI S1 ${prodi.toUpperCase()}  FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNOJOYO MADURA<br>
                SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahun}
            </div>

            <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
            </div>

            <div style="text-align: justify; margin-bottom: 20px; font-size: 10pt;">
                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 100px; vertical-align: top;"><strong>Menimbang</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <table style="width: 100%; border: none;">
                                <tr>
                                    <td style="width: 25px; vertical-align: top; border: none;">a.</td>
                                    <td style="border: none;">Bahwa untuk memperlancar penyelesaian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Skripsi;</td>
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
                        <td style="width: 100px; vertical-align: top;"><strong>Mengingat</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <ol style="margin: 0; padding-left: 20px;">
                                <li style="margin-bottom: 5px;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                                <li style="margin-bottom: 5px;">Undang-undang Nomor 12 Tahun 2012 Tentang Pendidikan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Peraturan Pemerintah Nomor 4 Tahun 2014 Tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Keputusan Presiden RI Nomor 85 tahun 2001, tentang Pendirian Universitas Trunojoyo Madura;</li>
                                <li style="margin-bottom: 5px;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/U/2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                                <li style="margin-bottom: 5px;">Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor 73649/MPK.A/KP.06.02/2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                                <li style="margin-bottom: 5px;">Keputusan Rektor Universitas Trunojoyo Madura Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunojoyo Madura periode 2021-2025;</li>
                            </ol>
                        </td>
                    </tr>
                </table>

                <p><strong>Memperhatikan:</strong> ${Object.keys(groupedByJurusan).map(pName => `Surat dari Kaprodi ${pName} tentang permohonan SK Dosen Penguji Skripsi`).join('; ')};</p>

                <div style="text-align: center; margin: 30px 0 20px 0; font-weight: bold;">
                    MEMUTUSKAN
                </div>

                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 20%; vertical-align: top; font-weight: bold;">Menetapkan</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>PENETAPAN DOSEN PENGUJI SKRIPSI PROGRAM STUDI S1 ${prodi.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahun}.</td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 20%; vertical-align: top; font-weight: bold;">Kesatu</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>Dosen Penguji Skripsi Program Studi S1 Teknik Informatika Fakultas Teknik Universitas Trunojoyo Madura semester Ganjil Tahun Akademik 2023/2024 sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                    </tr>
                </table>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 20%; vertical-align: top; font-weight: bold;">Kedua</td>
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

        previewContent.innerHTML = html;
    }
});
</script>
@endpush

@endsection
