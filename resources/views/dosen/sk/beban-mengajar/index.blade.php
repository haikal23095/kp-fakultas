@extends('layouts.dosen')

@section('title', 'SK Beban Mengajar')

@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #ffffff;
        color: #000;
        border: 1px solid #000;
        padding: 2cm 2.5cm;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        font-size: 11pt;
        line-height: 1.5;
        min-height: 29.7cm;
        width: 21cm;
        max-width: 100%;
        margin: 0 auto;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
        position: relative;
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
    }
    .preview-table-beban thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
    }
    .preview-signature {
        margin-top: 40px;
        text-align: right;
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
        <h1 class="h3 fw-bold mb-0">SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">Daftar SK beban mengajar yang melibatkan Anda</p>
    </div>
    <a href="{{ route('dosen.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
                @if($filteredSK->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Nomor SK</th>
                                <th width="12%">Semester</th>
                                <th width="15%">Tahun Akademik</th>
                                <th width="15%">Tanggal SK</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredSK as $index => $sk)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-primary">{{ $sk->Nomor_Surat }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'info' : 'warning' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td>
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    {{ $sk->{'Tanggal-Persetujuan-Dekan'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Persetujuan-Dekan'})->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="lihatDetail({{ $sk->No }})" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                        <a href="{{ route('dosen.sk.beban-mengajar.download', $sk->No) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada SK Beban Mengajar</h5>
                    <p class="text-muted">Anda belum terdaftar dalam SK beban mengajar yang telah disetujui</p>
                </div>
                @endif
            </div>
        </div>

<!-- Modal Detail SK -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-file-pdf me-2"></i>Preview SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="background-color: #525659;">
                <div class="d-flex justify-content-center align-items-center p-3" style="background-color: #323639;">
                    <a href="#" id="downloadSKLink" class="btn btn-light me-2" target="_blank">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
                <div class="d-flex justify-content-center p-4">
                    <div id="modalDetailContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-white">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function lihatDetail(skId) {
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
        
        // Set download link
        document.getElementById('downloadSKLink').href = `/dosen/sk/beban-mengajar/${skId}/download`;
        
        fetch(`/dosen/sk/beban-mengajar/${skId}/detail`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDetail(data.sk, data.dekanName, data.dekanNip, data.qrCodePath);
                } else {
                    document.getElementById('modalDetailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Gagal memuat detail SK
                    </div>
                `;
            });
    }
    
    function displayDetail(sk, dekanName, dekanNip, qrCodePath) {
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
        const dateFormatted = sk['Tanggal-Persetujuan-Dekan'] ? new Date(sk['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        const ttdHtml = qrCodePath 
            ? `<div class="preview-signature" style="font-size: 10pt; margin: 40px 0 30px 0;">
                    <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                    <p style="margin-bottom: 3px;">pada tanggal ${dateFormatted}</p>
                    <p style="margin-bottom: 10px;"><strong>DEKAN,</strong></p>
                    <img src="${qrCodePath.startsWith('http') || qrCodePath.startsWith('data:') ? qrCodePath : `/storage/${qrCodePath}`}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0; border: 1px solid #000;">
                    <p style="margin-bottom: 0;">
                        <strong><u>${dekanName}</u></strong><br>
                        NIP. ${dekanNip}
                    </p>
                </div>`
            : `<div class="preview-signature" style="font-size: 10pt; margin: 40px 0 30px 0;">
                    <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                    <p style="margin-bottom: 3px;">pada tanggal ${dateFormatted}</p>
                    <p style="margin-bottom: 70px;"><strong>DEKAN,</strong></p>
                    <p style="margin-bottom: 0;">
                        <strong><u>${dekanName}</u></strong><br>
                        NIP. ${dekanNip}
                    </p>
                </div>`;

        let lampiranHtml = '';
        Object.keys(groupedByProdi).forEach((prodiName, index) => {
            const items = groupedByProdi[prodiName];
            let listDosenHtml = '';
            
            items.forEach((item, idx) => {
                const mataKuliah = item.nama_mata_kuliah || item.mata_kuliah || item.Nama_Matakuliah || '-';
                const kelas = item.kelas || item.Kelas || '-';
                const sks = item.sks || item.SKS || 0;
                const namaDosen = item.nama_dosen || item.Nama_Dosen || '-';
                const nip = item.nip || item.NIP || '-';
                
                listDosenHtml += `
                    <tr>
                        <td style="text-align: center;">${idx + 1}.</td>
                        <td>${namaDosen}<br><small>NIP. ${nip}</small></td>
                        <td>${mataKuliah}</td>
                        <td style="text-align: center;">${kelas}</td>
                        <td style="text-align: center;">${sks}</td>
                    </tr>
                `;
            });

            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: 60px; page-break-before: always;">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN ${index + 1} KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 30px 0 13px 0; text-align: center; font-weight: bold;">BEBAN MENGAJAR DOSEN ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
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
                            ${listDosenHtml}
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 3px 0;">pada tanggal ${dateFormatted}</p>
                            <p style="margin: 0 0 ${qrCodePath ? '10px' : '70px'} 0;"><strong>DEKAN,</strong></p>
                            ${qrCodePath ? `<img src="${qrCodePath.startsWith('http') || qrCodePath.startsWith('data:') ? qrCodePath : `/storage/${qrCodePath}`}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0; border: 1px solid #000;">` : ''}
                            <p style="margin: 0 0 0 0;">
                                <strong><u>${dekanName}</u></strong><br>
                                NIP. ${dekanNip}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        let html = `
            <div class="preview-document">
                <div class="preview-header">
                    <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
                    <strong style="font-size: 14pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                    <strong style="font-size: 16pt;">UNIVERSITAS TRUNODJOYO</strong>
                    <strong style="font-size: 14pt;">FAKULTAS TEKNIK</strong>
                    <div style="font-size: 10pt; margin-top: 5px; font-weight: normal;">
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

                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">TENTANG</div>

                <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                    BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK<br>
                    UNIVERSITAS TRUNODJOYO<br>
                    SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
                </div>

                <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                    DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
                </div>

                <div style="text-align: justify; margin-bottom: 20px; font-size: 10pt;">
                    <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
                    <table style="width: 100%; margin-bottom: 15px;">
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
                    <table style="width: 100%; margin-bottom: 15px;">
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
                    <table style="width: 100%; margin-bottom: 15px;">
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

                    <div style="text-align: center; margin: 15px 0; font-weight: bold;">MEMUTUSKAN :</div>

                    <table style="width: 100%; margin-bottom: 15px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify; font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 10px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify;">Beban mengajar dosen Program Studi S1 di lingkungan Fakultas Teknik Universitas Trunodjoyo Semester ${semesterUpper} Tahun Akademik ${tahunAkademik} sebagaimana terlampir dalam surat keputusan ini.</td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 15px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kedua</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify;">Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                        </tr>
                    </table>
                </div>

                <div style="font-size: 10pt; margin: 40px 0 30px 0; text-align: right;">
                    ${ttdHtml}
                </div>

                ${lampiranHtml}
            </div>
        `;

        document.getElementById('modalDetailContent').innerHTML = html;
    }
</script>
@endpush

@endsection
