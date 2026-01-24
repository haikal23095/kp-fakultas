@extends('layouts.wadek1')

@section('title', 'SK Dosen Wali - Wadek 1')

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
    .preview-table-dosen {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 11pt;
        border: 1px solid #000;
    }
    .preview-table-dosen th,
    .preview-table-dosen td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    .preview-table-dosen thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
    }
    .preview-table-dosen tbody td {
        font-size: 10pt;
        vertical-align: top;
    }
    .preview-table-dosen tbody td:nth-child(1) {
        text-align: center;
    }
    .preview-table-dosen tbody td:nth-child(2) {
        text-align: left;
    }
    .preview-table-dosen tbody td:nth-child(3) {
        text-align: center;
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
        <h1 class="h3 fw-bold mb-0">Daftar SK Dosen Wali</h1>
        <p class="mb-0 text-muted">SK Dosen Wali yang menunggu persetujuan dan history.</p>
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
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchSk" placeholder="Cari NOMOR SK..." onkeyup="applyFilter()">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-success">SK Dosen Wali</h6>
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
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $sk)
                        <tr>
                            <td>{{ $sk->No }}</td>
                            <td>
                                <span class="badge bg-{{ $sk->Semester === 'Ganjil' ? 'primary' : 'info' }}">{{ $sk->Semester }}</span>
                            </td>
                            <td>{{ $sk->Tahun_Akademik }}</td>
                            <td>{{ $sk->Nomor_Surat ?? '-' }}</td>
                            <td>
                                @php $tgl = $sk->{'Tanggal-Pengajuan'}; @endphp
                                {{ $tgl ? $tgl->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    switch($sk->Status) {
                                        case 'Menunggu-Persetujuan-Wadek-1':
                                            $badgeClass = 'warning text-dark';
                                            break;
                                        case 'Menunggu-Persetujuan-Dekan':
                                            $badgeClass = 'primary';
                                            break;
                                        case 'Selesai':
                                            $badgeClass = 'success';
                                            break;
                                        case 'Ditolak-Wadek1':
                                            $badgeClass = 'danger';
                                            break;
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ str_replace('-', ' ', $sk->Status) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-primary" onclick="showDetail({{ $sk->No }})" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($sk->Status === 'Menunggu-Persetujuan-Wadek-1')
                                        <button class="btn btn-success" onclick="approveSK({{ $sk->No }})" title="Setujui SK">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="showRejectModal({{ $sk->No }}, '{{ $sk->Semester }}', '{{ $sk->Tahun_Akademik }}', '{{ $sk->Nomor_Surat }}')" title="Tolak SK">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-secondary" disabled title="Sudah Diproses">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada SK Dosen Wali.
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Dosen Wali
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="preview-document" id="previewContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
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
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Dosen Wali
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
                                <strong>Kembalikan ke Admin Fakultas</strong>
                                <small class="d-block text-muted">Untuk revisi teknis (penomoran, format, kesalahan data)</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="reject-target" id="reject-to-kaprodi" value="kaprodi">
                            <label class="form-check-label" for="reject-to-kaprodi">
                                <strong>Tolak ke Kaprodi</strong>
                                <small class="d-block text-muted">Untuk penolakan substantif (data dosen tidak sesuai, dll)</small>
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
    
    console.log('Global dekanName:', dekanName);
    console.log('Global dekanNip:', dekanNip);

    function applyFilter() {
        const search = document.getElementById('searchSk').value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const noSurat = row.cells[3].textContent.toLowerCase();
            if (noSurat.includes(search)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function showDetail(skId) {
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Fetch detail
        fetch(`{{ url('/wadek1/sk-dosen-wali') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('===== RESPONSE DEBUG =====');
            console.log('Full Response:', data);
            console.log('Success:', data.success);
            console.log('SK Data:', data.sk);
            console.log('Dekan Name from response:', data.dekanName);
            console.log('Dekan NIP from response:', data.dekanNip);
            console.log('Debug info:', data.debug);
            console.log('========================');
            
            if (data.success) {
                renderPreview(data.sk, data.dekanName, data.dekanNip);
            } else {
                alert('Gagal memuat detail SK: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail: ' + error.message);
        });
    }

    function renderPreview(sk, dekanNameParam, dekanNipParam) {
        // Use parameter if provided, otherwise fall back to global variables
        const finalDekanName = dekanNameParam || dekanName || '-';
        const finalDekanNip = dekanNipParam || dekanNip || '-';
        
        console.log('renderPreview called');
        console.log('dekanNameParam:', dekanNameParam);
        console.log('dekanNipParam:', dekanNipParam);
        console.log('finalDekanName:', finalDekanName);
        console.log('finalDekanNip:', finalDekanNip);
        
        const dosenList = sk.Data_Dosen_Wali || [];
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';

        // Kelompokkan dosen per prodi
        const groupedByProdi = {};
        dosenList.forEach(dosen => {
            const prodiName = dosen.prodi || '-';
            if (!groupedByProdi[prodiName]) {
                groupedByProdi[prodiName] = [];
            }
            groupedByProdi[prodiName].push(dosen);
        });

        let lampiranHtml = '';
        Object.keys(groupedByProdi).forEach((prodiName, index) => {
            const dosenProdi = groupedByProdi[prodiName];
            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN I KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PERIHAL</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold; text-decoration: underline;">Daftar Dosen Wali Mahasiswa Prodi ${prodiName}</p>
                    </div>
                    <table class="preview-table-dosen">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No.</th>
                                <th style="width: 67%;">Nama Dosen</th>
                                <th style="width: 25%;">Jumlah Anak Wali</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${dosenProdi.map((dosen, idx) => `
                                <tr>
                                    <td>${idx + 1}.</td>
                                    <td>${dosen.nama_dosen}</td>
                                    <td>${dosen.jumlah_anak_wali}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 30px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                            <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
                            <p style="margin: 0 0 0 0;">
                                <strong><u>${finalDekanName}</u></strong><br>
                                NIP. ${finalDekanNip}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        const html = `
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

            <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 11pt;">
                KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNODJOYO<br>
                NOMOR ${nomorSurat}
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                TENTANG
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                DOSEN WALI MAHASISWA FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNODJOYO<br>
                SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
            </div>

            <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
            </div>

            <div style="text-align: justify; margin-bottom: 20px;">
                <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
                <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                    <tr>
                        <td style="width: 10%; vertical-align: top;">:</td>
                        <td style="width: 5%; vertical-align: top;">a.</td>
                        <td style="text-align: justify;">bahwa dalam rangka membantu mahasiswa menyelesaikan program sarjana/diploma sesuai rencana studi, perlu menugaskan dosen tetap di lingkungan Fakultas Teknik Universitas Trunodjoyo sebagai dosen wali;</td>
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
                        <td style="text-align: justify;">Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Dosen Wali ${semesterText} ${tahunAkademik};</td>
                    </tr>
                </table>

                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                    MEMUTUSKAN :
                </div>

                <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td style="text-align: justify; font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 10px; font-size: 10pt;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td style="text-align: justify;">Menugaskan dosen tetap di Fakultas Teknik Universitas Trunodjoyo yang namanya tersebut dalam lampiran Surat Keputusan ini sebagai dosen wali Semester ${semesterText} Tahun Akademik ${tahunAkademik};</td>
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

                <table style="width: 100%; font-size: 10pt; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: normal;">Ketiga</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td style="text-align: justify;">Keputusan ini berlaku sejak tanggal ditetapkan.</td>
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
        if (!confirm('Apakah Anda yakin ingin menyetujui SK Dosen Wali ini? SK akan diteruskan ke Dekan.')) {
            return;
        }

        fetch(`{{ url('/wadek1/sk-dosen-wali') }}/${skId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
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
            alert('Terjadi kesalahan saat menyetujui SK: ' + error.message);
        });
    }

    function showRejectModal(skId, semester, tahun, nomorSK) {
        document.getElementById('reject-sk-id').value = skId;
        document.getElementById('reject-nomor').textContent = nomorSK || '-';
        document.getElementById('reject-semester').textContent = semester;
        document.getElementById('reject-tahun').textContent = tahun;
        document.getElementById('reject-alasan').value = '';
        
        // Reset radio button ke admin (default)
        document.getElementById('reject-to-admin').checked = true;
        document.getElementById('reject-target-text').textContent = 'Admin Fakultas';
        
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }
    
    // Update target text when radio button changes
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
        
        fetch('{{ url("/wadek1/sk-dosen-wali") }}/' + skId + '/reject', {
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
        .then(response => {
            // Parse JSON bahkan jika response tidak ok
            return response.json().then(data => {
                if (!response.ok) {
                    // Jika ada error validation atau lainnya
                    throw { status: response.status, data: data };
                }
                return data;
            });
        })
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
            if (error.status === 422 && error.data) {
                // Validation error
                let errorMsg = 'Validasi gagal:\n';
                if (error.data.errors) {
                    Object.keys(error.data.errors).forEach(key => {
                        errorMsg += '- ' + error.data.errors[key].join(', ') + '\n';
                    });
                } else if (error.data.message) {
                    errorMsg = error.data.message;
                }
                alert(errorMsg);
            } else {
                alert('Terjadi kesalahan saat menolak SK: ' + (error.message || error.data?.message || 'Unknown error'));
            }
        });
    }
</script>
@endpush

@endsection
