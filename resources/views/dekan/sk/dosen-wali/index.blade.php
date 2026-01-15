@extends('layouts.dekan')

@section('title', 'SK Dosen Wali - Dekan')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .card-sk {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .card-sk:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .badge-status {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }

    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #4e73df;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

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

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        .page-header h3 {
            font-size: 1.1rem;
        }
        .table {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1 fw-bold text-dark">SK Dosen Wali</h3>
            <p class="mb-0 text-muted small">Daftar SK Dosen Wali yang menunggu tanda tangan Dekan</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="showHistory()">
                <i class="fas fa-history me-2"></i>History
            </button>
            <a href="{{ route('dekan.persetujuan.sk_dosen') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Info Box --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="info-box">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1"></i>
                <div>
                    <strong>Total SK:</strong> {{ $daftarSK->count() }} SK Dosen Wali
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            <select class="form-select" id="filterStatus" onchange="applyFilter()" style="max-width: 250px;">
                <option value="">Semua Status</option>
                <option value="Menunggu-Persetujuan-Dekan">Menunggu Persetujuan</option>
                <option value="Selesai">Selesai</option>
                <option value="Ditolak-Dekan">Ditolak</option>
            </select>
        </div>
    </div>
</div>

{{-- Daftar SK Dosen Wali --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list me-2 text-success"></i>
            Daftar SK Dosen Wali
        </h5>
    </div>
    <div class="card-body p-0">
        @if($daftarSK->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada SK Dosen Wali yang menunggu persetujuan</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Program Studi</th>
                            <th width="15%">Semester</th>
                            <th width="15%">Tahun Akademik</th>
                            <th width="12%">Tanggal Ajuan</th>
                            <th width="10%" class="text-center">Jumlah Dosen</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="13%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSK as $index => $sk)
                            @php
                                $firstReq = $sk->reqSKDosenWali->first();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $firstReq->prodi->Nama_Prodi ?? 'Gabungan Prodi' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Diajukan: {{ $firstReq->kaprodi->user->name ?? 'Admin Fakultas' }}
                                    </small>
                                </td>
                                <td>{{ $sk->Semester ?? '-' }}</td>
                                <td>{{ $sk->Tahun_Akademik ?? '-' }}</td>
                                <td>
                                    {{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ is_array($sk->Data_Dosen_Wali) ? count($sk->Data_Dosen_Wali) : 0 }} Dosen
                                    </span>
                                </td>
                                <td class="text-center">
                                    @switch($sk->Status)
                                        @case('Menunggu-Persetujuan-Dekan')
                                            <span class="badge bg-warning badge-status">
                                                <i class="fas fa-clock me-1"></i>Menunggu TTD Dekan
                                            </span>
                                            @break
                                        @case('Selesai')
                                            <span class="badge bg-success badge-status">
                                                <i class="fas fa-check-circle me-1"></i>Selesai
                                            </span>
                                            @break
                                        @case('Ditolak-Dekan')
                                            <span class="badge bg-danger badge-status">
                                                <i class="fas fa-times-circle me-1"></i>Ditolak
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary badge-status">{{ $sk->Status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-info btn-sm" title="Lihat Detail" onclick="showDetail({{ $sk->No }})">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                        @if($sk->Status == 'Menunggu-Persetujuan-Dekan')
                                            <button type="button" class="btn btn-success btn-sm" title="Setujui & TTD" onclick="approveSK({{ $sk->No }})">
                                                <i class="fas fa-check me-1"></i> Setujui
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" title="Tolak" onclick="rejectSK({{ $sk->No }})">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Info Tambahan --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info border-0" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-2">Informasi</h6>
                    <ul class="mb-0 small">
                        <li>SK Dosen Wali yang muncul di sini adalah yang sudah disetujui oleh Wadek 1.</li>
                        <li>Anda dapat melihat detail SK dan memberikan tanda tangan elektronik.</li>
                        <li>Setelah ditandatangani, SK akan dikirimkan ke Kaprodi yang mengajukan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail SK -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Preview SK Dosen Wali
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
                <button type="button" class="btn btn-success" onclick="approveFromModal()" id="btnApprove">
                    <i class="fas fa-check me-1"></i>Setujui & TTD
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal History -->
<div class="modal fade" id="modalHistory" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalHistoryLabel">
                    <i class="fas fa-history me-2"></i>History SK Dosen Wali yang Sudah Ditandatangani
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data history...</p>
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

<!-- Modal Rejection -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRejectLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Dosen Wali
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> SK yang ditolak akan dikembalikan untuk diperbaiki.
                </div>
                
                <!-- Pilih Target Penolakan -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Kembalikan SK ke: <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectTarget" id="targetAdmin" value="admin" checked>
                        <label class="form-check-label" for="targetAdmin">
                            <strong>Admin Fakultas</strong>
                            <small class="d-block text-muted">SK akan dikembalikan ke Admin Fakultas untuk diperbaiki (misal: nomor surat salah, format tidak sesuai)</small>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="rejectTarget" id="targetKaprodi" value="kaprodi">
                        <label class="form-check-label" for="targetKaprodi">
                            <strong>Kaprodi</strong>
                            <small class="d-block text-muted">SK akan dikembalikan ke Kaprodi untuk diperbaiki (misal: data dosen salah, prodi tidak sesuai)</small>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alasanPenolakan" rows="4" placeholder="Tuliskan alasan penolakan (minimal 10 karakter)" required></textarea>
                    <small class="text-muted">Minimal 10 karakter</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentSKId = null;
    let currentDekanName = '';
    let currentDekanNip = '';
    let currentQRCode = '';
    let isHistoryMode = false;

    function showHistory() {
        const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
        modal.show();

        // Fetch history data
        fetch('{{ url('/dekan/sk-dosen-wali/history') }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderHistory(data.history);
            } else {
                document.getElementById('historyContent').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message || 'Gagal memuat history'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('historyContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat history
                </div>
            `;
        });
    }

    function renderHistory(historyList) {
        if (historyList.length === 0) {
            document.getElementById('historyContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada SK Dosen Wali yang ditandatangani</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Program Studi</th>
                            <th width="15%">Semester/TA</th>
                            <th width="15%">Tanggal TTD</th>
                            <th width="15%">Ditandatangani Oleh</th>
                            <th width="10%">Jumlah Dosen</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        historyList.forEach((sk, index) => {
            const firstReq = sk.req_sk_dosen_wali && sk.req_sk_dosen_wali.length > 0 ? sk.req_sk_dosen_wali[0] : null;
            const prodiName = firstReq && firstReq.prodi ? firstReq.prodi.Nama_Prodi : 'Gabungan Prodi';
            const tanggalTTD = sk['Tanggal-Persetujuan-Dekan'] || '-';
            const dosenCount = sk.Data_Dosen_Wali ? sk.Data_Dosen_Wali.length : 0;
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${prodiName}</strong></td>
                    <td>${sk.Semester || '-'} / ${sk.Tahun_Akademik || '-'}</td>
                    <td>${tanggalTTD}</td>
                    <td>${sk.dekan_name || 'Dr. Budi Hartono, S.Kom., M.Kom.'}</td>
                    <td><span class="badge bg-primary">${dosenCount} Dosen</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" onclick="showHistoryDetail(${sk.No})">
                            <i class="fas fa-eye me-1"></i>Lihat Preview
                        </button>
                    </td>
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

    function showHistoryDetail(skId) {
        isHistoryMode = true;
        currentSKId = skId;
        
        // Close history modal
        bootstrap.Modal.getInstance(document.getElementById('modalHistory')).hide();
        
        // Show detail modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Hide approve button for history items
        document.getElementById('btnApprove').style.display = 'none';

        // Fetch detail
        fetch(`{{ url('/dekan/sk-dosen-wali') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentDekanName = data.dekanName;
                currentDekanNip = data.dekanNip;
                // Render with QR code from database
                const qrCodeUrl = data.sk.QR_Code ? `{{ asset('storage') }}/${data.sk.QR_Code}` : null;
                renderPreview(data.sk, data.dekanName, data.dekanNip, qrCodeUrl);
            } else {
                alert('Gagal memuat detail SK: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail: ' + error.message);
        });
    }

    function showDetail(skId) {
        isHistoryMode = false;
        currentSKId = skId;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Show approve button for pending items
        document.getElementById('btnApprove').style.display = 'inline-block';

        // Fetch detail        modal.show();

        // Fetch detail
        fetch(`{{ url('/dekan/sk-dosen-wali') }}/${skId}`, {
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
            console.log('Response data:', data);
            if (data.success) {
                currentDekanName = data.dekanName;
                currentDekanNip = data.dekanNip;
                renderPreview(data.sk, data.dekanName, data.dekanNip, null);
            } else {
                console.error('Error details:', data);
                alert('Gagal memuat detail SK: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail: ' + error.message);
        });
    }

    function renderPreview(sk, dekanNameParam, dekanNipParam, qrCode) {
        const finalDekanName = dekanNameParam || currentDekanName || '-';
        const finalDekanNip = dekanNipParam || currentDekanNip || '-';
        
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

        // Buat HTML untuk tanda tangan dengan atau tanpa QR code
        const ttdHtml = qrCode 
            ? `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 10px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
               <img src="${qrCode}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0; border: 1px solid #000;">
               <p style="margin: 0 0 0 0;">
                   <strong><u>${finalDekanName}</u></strong><br>
                   NIP. ${finalDekanNip}
               </p>`
            : `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 30px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
               <p style="margin: 0 0 0 0;">
                   <strong><u>${finalDekanName}</u></strong><br>
                   NIP. ${finalDekanNip}
               </p>`;

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
                            ${ttdHtml}
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
                ${ttdHtml}
            </div>

            ${lampiranHtml}
        `;

        document.getElementById('previewContent').innerHTML = html;
    }

    function approveSK(skId, fromModal = false) {
        if (!confirm('Apakah Anda yakin ingin menyetujui dan menandatangani SK Dosen Wali ini?')) {
            return;
        }

        currentSKId = skId;
        
        fetch(`{{ url('/dekan/sk-dosen-wali') }}/${skId}/approve`, {
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
                currentQRCode = data.qr_code;
                
                if (fromModal) {
                    // Jika dari modal, render ulang preview dengan QR code
                    fetch(`{{ url('/dekan/sk-dosen-wali') }}/${skId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(detailData => {
                        if (detailData.success) {
                            currentDekanName = detailData.dekanName;
                            currentDekanNip = detailData.dekanNip;
                            renderPreview(detailData.sk, detailData.dekanName, detailData.dekanNip, currentQRCode);
                            alert(data.message + '\n\nSK telah ditandatangani dengan QR Code. Silakan lihat preview di atas.');
                        }
                    })
                    .catch(error => {
                        console.error('Error refreshing preview:', error);
                        alert(data.message);
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyetujui SK: ' + error.message);
        });
    }

    function approveFromModal() {
        if (!currentSKId) {
            alert('ID SK tidak ditemukan');
            return;
        }
        approveSK(currentSKId, true);
    }

    // Function untuk reject SK
    let currentRejectSKId = null;

    function rejectSK(skId) {
        currentRejectSKId = skId;
        document.getElementById('alasanPenolakan').value = '';
        document.getElementById('targetAdmin').checked = true; // Default ke admin
        
        const modal = new bootstrap.Modal(document.getElementById('modalReject'));
        modal.show();
    }

    function submitRejection() {
        const alasan = document.getElementById('alasanPenolakan').value.trim();
        const targetAdmin = document.getElementById('targetAdmin').checked;
        const targetKaprodi = document.getElementById('targetKaprodi').checked;
        
        // Validasi
        if (alasan.length < 10) {
            alert('Alasan penolakan harus minimal 10 karakter');
            return;
        }
        
        if (!targetAdmin && !targetKaprodi) {
            alert('Silakan pilih target penolakan');
            return;
        }
        
        const target = targetAdmin ? 'admin' : 'kaprodi';
        
        // Konfirmasi
        const targetText = target === 'admin' ? 'Admin Fakultas' : 'Kaprodi';
        if (!confirm(`Yakin ingin menolak SK ini dan mengembalikan ke ${targetText}?`)) {
            return;
        }
        
        // Kirim request
        fetch(`{{ url('/dekan/sk-dosen-wali') }}/${currentRejectSKId}/reject`, {
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
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menolak SK: ' + error.message);
        });
    }

    // Function untuk apply filter
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

    // Set filter value dari URL saat page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        if (status) {
            document.getElementById('filterStatus').value = status;
        }
    });

    // Add hover effect
    document.querySelectorAll('.card-sk').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4e73df';
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e9ecef';
        });
    });

</script>
@endpush
