@extends('layouts.wadek1')

@section('title', 'SK Pembimbing Skripsi - Wadek 1')

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
        <h1 class="h3 fw-bold mb-0">Daftar SK Pembimbing Skripsi</h1>
        <p class="mb-0 text-muted">SK Pembimbing Skripsi yang menunggu persetujuan dan history.</p>
    </div>
    <div>
        <a href="{{ route('wadek1.sk.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Ringkasan SK
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Filter Status</label>
                <select class="form-select" id="filterStatus" onchange="applyFilter()">
                    <option value="">Semua Status</option>
                    <option value="Menunggu-Persetujuan-Wadek-1" {{ request('status') == 'Menunggu-Persetujuan-Wadek-1' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>Disetujui (Menunggu Dekan)</option>
                    <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-warning">SK Pembimbing Skripsi</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Nomor Surat</th>
                        <th>Jumlah Mahasiswa</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $sk)
                        @php
                            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
                            if (is_string($dataPembimbing)) {
                                $dataPembimbing = json_decode($dataPembimbing, true);
                            }
                            $jumlahMahasiswa = is_array($dataPembimbing) ? count($dataPembimbing) : 0;
                        @endphp
                        <tr>
                            <td>{{ $sk->No }}</td>
                            <td>
                                <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                    {{ $sk->Semester }}
                                </span>
                            </td>
                            <td>{{ $sk->Tahun_Akademik }}</td>
                            <td>{{ $sk->Nomor_Surat ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $jumlahMahasiswa }} Mahasiswa</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    $statusText = $sk->Status;
                                    
                                    switch($sk->Status) {
                                        case 'Menunggu-Persetujuan-Wadek-1':
                                            $badgeClass = 'warning';
                                            $statusText = 'Menunggu Persetujuan';
                                            break;
                                        case 'Menunggu-Persetujuan-Dekan':
                                            $badgeClass = 'info';
                                            $statusText = 'Disetujui (Menunggu Dekan)';
                                            break;
                                        case 'Ditolak-Wadek1':
                                            $badgeClass = 'danger';
                                            $statusText = 'Ditolak';
                                            break;
                                        case 'Selesai':
                                            $badgeClass = 'success';
                                            $statusText = 'Selesai';
                                            break;
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" 
                                            class="btn btn-info" 
                                            onclick="showDetail({{ $sk->No }})"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($sk->Status == 'Menunggu-Persetujuan-Wadek-1')
                                    <button type="button" 
                                            class="btn btn-success" 
                                            onclick="approveSK({{ $sk->No }})"
                                            title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="showRejectModal({{ $sk->No }}, '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}', '{{ $sk->Nomor_Surat ?? '-' }}')"
                                            title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada SK Pembimbing Skripsi
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

<!-- Modal Detail SK -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="preview-document" id="previewContent">
                        <div class="text-center">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
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
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Anda akan menolak SK berikut:
                </div>
                
                <div class="mb-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Nomor SK</th>
                            <td>: <span id="reject-nomor">-</span></td>
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
                        <label class="form-label fw-semibold">
                            Tujuan Penolakan <span class="text-danger">*</span>
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reject-target" id="reject-to-admin" value="admin" checked>
                            <label class="form-check-label" for="reject-to-admin">
                                <strong>Kembalikan ke Admin Fakultas</strong><br>
                                <small class="text-muted">Untuk revisi dokumen/data</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="reject-target" id="reject-to-kaprodi" value="kaprodi">
                            <label class="form-check-label" for="reject-to-kaprodi">
                                <strong>Tolak dan Kirim ke Kaprodi</strong><br>
                                <small class="text-muted">SK ditolak secara permanen</small>
                            </label>
                        </div>
                    </div>
                    
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
                        <div class="form-text" id="reject-help-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Alasan ini akan dikirimkan sebagai notifikasi ke <span id="reject-target-text">Admin Fakultas</span>
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

    function applyFilter() {
        const status = document.getElementById('filterStatus').value;
        const url = new URL(window.location.href);
        
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        window.location.href = url.toString();
    }

    function showDetail(skId) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        console.log('Fetching SK detail for ID:', skId);

        fetch(`{{ url('/wadek1/sk-pembimbing-skripsi') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                renderPreview(data.sk, data.dekanName, data.dekanNip);
            } else {
                document.getElementById('previewContent').innerHTML = 
                    `<div class="alert alert-danger">${data.message || 'Gagal memuat detail SK'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = 
                `<div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan saat memuat detail: ${error.message}
                </div>`;
        });
    }

    function renderPreview(sk, dekanNameParam, dekanNipParam) {
        const finalDekanName = dekanNameParam || dekanName || '-';
        const finalDekanNip = dekanNipParam || dekanNip || '-';
        
        // Handle berbagai tipe data untuk Data_Pembimbing_Skripsi
        let dataPembimbing = sk.Data_Pembimbing_Skripsi || [];
        
        // Jika string, parse JSON
        if (typeof dataPembimbing === 'string') {
            try {
                dataPembimbing = JSON.parse(dataPembimbing);
            } catch (e) {
                console.error('Error parsing Data_Pembimbing_Skripsi:', e);
                dataPembimbing = [];
            }
        }
        
        // Jika object bukan array, convert ke array
        if (dataPembimbing && typeof dataPembimbing === 'object' && !Array.isArray(dataPembimbing)) {
            dataPembimbing = Object.values(dataPembimbing);
        }
        
        // Pastikan dataPembimbing adalah array
        if (!Array.isArray(dataPembimbing)) {
            console.error('Data_Pembimbing_Skripsi bukan array:', dataPembimbing);
            dataPembimbing = [];
        }
        
        console.log('Data Pembimbing (processed):', dataPembimbing);
        
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';

        // Kelompokkan mahasiswa per jurusan
        const groupedByJurusan = {};
        dataPembimbing.forEach(mhs => {
            // Ambil nama jurusan dari relasi prodi, dengan fallback yang lebih baik
            let jurusanName = '-';
            if (mhs.prodi_data && mhs.prodi_data.jurusan && mhs.prodi_data.jurusan.Nama_Jurusan) {
                jurusanName = mhs.prodi_data.jurusan.Nama_Jurusan;
            } else if (mhs.prodi && mhs.prodi !== '-') {
                // Jika ada nama prodi tapi tidak ada jurusan, gunakan prodi sebagai pengganti
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

        let lampiranHtml = '';
        Object.keys(groupedByJurusan).forEach((jurusanName, index) => {
            const jurusanData = groupedByJurusan[jurusanName];
            const mahasiswaProdi = jurusanData.mahasiswa;
            const prodiName = jurusanData.prodi[0] || jurusanName;
            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PEMBIMBING SKRIPSI</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK</p>
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
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 30px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                            <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
                            <p style="margin: 0 0 3px 0; text-decoration: underline;"><strong>${finalDekanName}</strong></p>
                            <p style="margin: 0;">NIP. ${finalDekanNip}</p>
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

            <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 14pt; text-decoration: underline;">
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
                        <td>PENETAPAN DOSEN PEMBIMBING SKRIPSI FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
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
                <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                <p style="margin-bottom: 3px;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                <p style="margin-bottom: 70px;"><strong>DEKAN,</strong></p>
                <p style="margin-bottom: 0;">
                    <strong><u>${finalDekanName}</u></strong><br>
                    NIP. ${finalDekanNip}
                </p>
            </div>

            ${lampiranHtml}
        `;

        document.getElementById('previewContent').innerHTML = html;
    }

    function approveSK(skId) {
        if (!confirm('Apakah Anda yakin ingin menyetujui SK Pembimbing Skripsi ini? SK akan diteruskan ke Dekan.')) {
            return;
        }

        fetch(`{{ url('/wadek1/sk-pembimbing-skripsi') }}/${skId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyetujui SK');
        });
    }

    function showRejectModal(skId, semester, tahun, nomorSK) {
        document.getElementById('reject-sk-id').value = skId;
        document.getElementById('reject-nomor').textContent = nomorSK || '-';
        document.getElementById('reject-semester').textContent = semester;
        document.getElementById('reject-tahun').textContent = tahun;
        document.getElementById('reject-alasan').value = '';
        
        document.getElementById('reject-to-admin').checked = true;
        document.getElementById('reject-target-text').textContent = 'Admin Fakultas';
        
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const radioButtons = document.querySelectorAll('input[name="reject-target"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                const targetText = this.value === 'admin' ? 'Admin Fakultas' : 'Kaprodi';
                document.getElementById('reject-target-text').textContent = targetText;
            });
        });
    });

    function submitRejection() {
        const skId = document.getElementById('reject-sk-id').value;
        const alasan = document.getElementById('reject-alasan').value.trim();
        const target = document.querySelector('input[name="reject-target"]:checked').value;
        
        if (!alasan) {
            alert('Alasan penolakan harus diisi');
            return;
        }
        
        const targetName = target === 'admin' ? 'Admin Fakultas' : 'Kaprodi';
        if (!confirm(`Apakah Anda yakin ingin menolak SK ini dan mengirimkan ke ${targetName}?`)) {
            return;
        }
        
        fetch(`{{ url('/wadek1/sk-pembimbing-skripsi') }}/${skId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                alasan: alasan,
                target: target
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolakSK'));
                modal.hide();
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menolak SK');
        });
    }
</script>
@endpush

@endsection
