@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Dosen Wali')

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
        <h1 class="h3 fw-bold mb-0">Riwayat SK Dosen Wali</h1>
        <p class="mb-0 text-muted">Daftar SK Dosen Wali yang telah diajukan</p>
    </div>
    <div>
        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('kaprodi.sk.dosen-wali.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($skList->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Belum ada SK Dosen Wali yang diajukan</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Semester / Tahun</th>
                            <th>Program Studi</th>
                            <th>Jumlah Dosen</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Tanggal Tenggat</th>
                            <th>Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skList as $index => $sk)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $sk->Semester }}</div>
                                <small class="text-muted">{{ $sk->Tahun_Akademik }}</small>
                            </td>
                            <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                            <td>
                                @php
                                    $dosenCount = is_array($sk->Data_Dosen_Wali) ? count($sk->Data_Dosen_Wali) : 0;
                                @endphp
                                <span class="badge bg-info">{{ $dosenCount }} Dosen</span>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($sk->{'Tanggal-Tenggat'})->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($sk->Status) {
                                        'Dikerjakan admin' => 'bg-warning text-dark',
                                        'Menunggu-Persetujuan-Wadek-1' => 'bg-info',
                                        'Menunggu-Persetujuan-Dekan' => 'bg-primary',
                                        'Selesai' => 'bg-success',
                                        'Ditolak-Admin' => 'bg-danger',
                                        'Ditolak' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    $statusText = str_replace('-', ' ', $sk->Status);
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showDetail({{ $sk->No }})">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </button>
                                    @if($sk->Status === 'Selesai')
                                        <button class="btn btn-sm btn-outline-success" onclick="showSKPreview({{ $sk->No }})">
                                            <i class="fas fa-file-pdf me-1"></i>Lihat SK
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Dosen Wali
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
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

<!-- Modal Preview SK -->
<div class="modal fade" id="modalPreviewSK" tabindex="-1" aria-labelledby="modalPreviewSKLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalPreviewSKLabel">
                    <i class="fas fa-file-contract me-2"></i>Preview SK Dosen Wali
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #525659;">
                <div style="max-height: 85vh; overflow-y: auto; padding: 20px;">
                    <div class="preview-document" id="previewSKContent" style="margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <a href="#" id="btnDownloadSK" class="btn btn-success" target="_blank">
                    <i class="fas fa-download me-2"></i>Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentSKId = null;

    function showDetail(skNo) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Find SK data from the list
        const skData = @json($skList);
        const sk = skData.find(item => item.No === skNo);

        if (!sk) {
            document.getElementById('modalDetailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Data tidak ditemukan
                </div>
            `;
            return;
        }

        // Debug: log SK data to console
        console.log('SK Data:', sk);
        console.log('Status:', sk.Status);
        console.log('Alasan-Tolak:', sk['Alasan-Tolak']);

        // Render detail
        renderDetail(sk);
    }

    function showSKPreview(skNo) {
        currentSKId = skNo;
        const modal = new bootstrap.Modal(document.getElementById('modalPreviewSK'));
        modal.show();

        // Set download link
        document.getElementById('btnDownloadSK').href = `{{ url('/kaprodi/sk/dosen-wali') }}/${skNo}/download`;

        // Fetch detail with QR code
        fetch(`{{ url('/kaprodi/sk/dosen-wali') }}/${skNo}/detail`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSKPreview(data.sk, data.accSK, data.dekanName, data.dekanNip);
            } else {
                document.getElementById('previewSKContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message || 'Gagal memuat preview'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewSKContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat preview
                </div>
            `;
        });
    }

    function renderSKPreview(sk, accSK, dekanName, dekanNip) {
        const dosenList = sk.Data_Dosen_Wali || [];
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || accSK?.Nomor_Surat || '-';
        const qrCodePath = accSK?.QR_Code ? `{{ asset('storage') }}/${accSK.QR_Code}` : null;
        const tanggalTTD = accSK?.['Tanggal-Persetujuan-Dekan'] ? new Date(accSK['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});

        // Kelompokkan dosen per prodi (sama seperti di Dekan)
        const groupedByProdi = {};
        dosenList.forEach(dosen => {
            const prodiName = dosen.prodi || '-';
            if (!groupedByProdi[prodiName]) {
                groupedByProdi[prodiName] = [];
            }
            groupedByProdi[prodiName].push(dosen);
        });

        const ttdHtml = qrCodePath 
            ? `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 10px 0;">pada tanggal ${tanggalTTD}</p>
               <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
               <img src="${qrCodePath}" alt="QR Code" style="width: 100px; height: 100px; margin: 10px 0; border: 1px solid #000;">
               <p style="margin: 0 0 0 0;">
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

        // Generate lampiran HTML untuk setiap prodi (sama seperti di Dekan)
        let lampiranHtml = '';
        Object.keys(groupedByProdi).forEach((prodiName, index) => {
            const dosenProdi = groupedByProdi[prodiName];
            
            let dosenTableHtml = '';
            dosenProdi.forEach((dosen, idx) => {
                dosenTableHtml += `
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-size: 10pt;">${idx + 1}.</td>
                        <td style="border: 1px solid #000; padding: 5px 8px; text-align: left; font-size: 10pt;">${dosen.nama_dosen || '-'}</td>
                        <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-size: 10pt;">${dosen.jumlah_anak_wali || 0}</td>
                    </tr>
                `;
            });

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
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 11pt; margin: 15px 0;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #000; padding: 5px 8px; background-color: #ffffff; font-weight: bold; text-align: center; width: 8%;">No.</th>
                                <th style="border: 1px solid #000; padding: 5px 8px; background-color: #ffffff; font-weight: bold; text-align: center; width: 67%;">Nama Dosen</th>
                                <th style="border: 1px solid #000; padding: 5px 8px; background-color: #ffffff; font-weight: bold; text-align: center; width: 25%;">Jumlah Anak Wali</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${dosenTableHtml}
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
            <div style="font-family: 'Times New Roman', Times, serif; background: #ffffff; color: #000; border: 1px solid #000; padding: 2cm 2.5cm; font-size: 11pt; line-height: 1.5; min-height: 29.7cm; width: 21cm; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px;">
                    <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="width: 80px; float: left; margin-top: -5px;">
                    <strong style="display: block; text-transform: uppercase; font-size: 14pt; font-weight: bold;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                    <strong style="display: block; text-transform: uppercase; font-size: 16pt; font-weight: bold;">UNIVERSITAS TRUNODJOYO</strong>
                    <strong style="display: block; text-transform: uppercase; font-size: 14pt; font-weight: bold;">FAKULTAS TEKNIK</strong>
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
                    DOSEN WALI MAHASISWA FAKULTAS TEKNIK<br>
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
                            <td style="text-align: justify;">bahwa dalam rangka membantu mahasiswa menyelesaikan program sarjana/diploma sesuai rencana studi, perlu menugaskan dosen tetap di lingkungan Fakultas Teknik Universitas Trunodjoyo sebagai dosen wali;</td>
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
                            <td style="text-align: justify;">Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor 73649/MPK.A/KP.06.02/2022 tentang pengangkatan Rektor UTM periode 2022-2026;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="vertical-align: top;">6.</td>
                            <td style="text-align: justify;">Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunodjoyo periode 2021-2025;</td>
                        </tr>
                    </table>

                    <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
                    <table style="width: 100%; margin-bottom: 15px;">
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

                    <div style="text-align: center; margin: 15px 0; font-weight: bold;">MEMUTUSKAN :</div>

                    <table style="width: 100%; margin-bottom: 15px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Menetapkan</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify; font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 10px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kesatu</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify;">Menugaskan dosen tetap di Fakultas Teknik Universitas Trunodjoyo yang namanya tersebut dalam lampiran Surat Keputusan ini sebagai dosen wali Semester ${semesterText} Tahun Akademik ${tahunAkademik};</td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 15px;">
                        <tr>
                            <td style="width: 15%; vertical-align: top; font-weight: normal;">Kedua</td>
                            <td style="width: 3%; vertical-align: top;">:</td>
                            <td style="text-align: justify;">Tugas dan fungsi dosen wali tersebut yaitu:<br>
                                <span style="margin-left: 15px;">a. Membantu mengarahkan dan mengesahkan rencana studi;</span><br>
                                <span style="margin-left: 15px;">b. Memberi bimbingan dan nasehat mengenai berbagai masalah yang bersifat kurikuler akademik;</span>
                            </td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-bottom: 10px;">
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
            </div>
        `;

        document.getElementById('previewSKContent').innerHTML = html;
    }

    function renderDetail(sk) {
        const dosenList = sk.Data_Dosen_Wali || [];
        const prodi = sk.prodi ? sk.prodi.Nama_Prodi : '-';

        let dosenHtml = '';
        dosenList.forEach((dosen, index) => {
            dosenHtml += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>${dosen.nama_dosen || '-'}</td>
                    <td>${dosen.nip || '-'}</td>
                    <td class="text-center">${dosen.jumlah_anak_wali || 0}</td>
                </tr>
            `;
        });

        const statusClass = {
            'Dikerjakan admin': 'bg-warning text-dark',
            'Menunggu-Persetujuan-Wadek-1': 'bg-info',
            'Menunggu-Persetujuan-Dekan': 'bg-primary',
            'Selesai': 'bg-success',
            'Ditolak-Admin': 'bg-danger',
            'Ditolak': 'bg-danger'
        }[sk.Status] || 'bg-secondary';

        const statusText = sk.Status.replace(/-/g, ' ');

        const html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150" class="text-muted">Program Studi</td>
                            <td><strong>${prodi}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td><strong>${sk.Semester}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Akademik</td>
                            <td><strong>${sk.Tahun_Akademik}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150" class="text-muted">Tanggal Pengajuan</td>
                            <td><strong>${new Date(sk['Tanggal-Pengajuan']).toLocaleString('id-ID')}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Tenggat</td>
                            <td><strong>${new Date(sk['Tanggal-Tenggat']).toLocaleString('id-ID')}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge ${statusClass}">${statusText}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            ${sk.Nomor_Surat ? `
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nomor Surat:</strong> ${sk.Nomor_Surat}
            </div>
            ` : ''}

            ${(sk.Status === 'Ditolak-Admin' || sk.Status === 'Ditolak') && sk['Alasan-Tolak'] ? `
            <div class="alert alert-danger mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-2">SK Ditolak</h6>
                        <p class="mb-0"><strong>Alasan Penolakan:</strong></p>
                        <p class="mb-0">${sk['Alasan-Tolak']}</p>
                    </div>
                </div>
            </div>
            ` : ''}

            <h6 class="fw-bold mb-3">Daftar Dosen Wali</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Dosen</th>
                            <th width="200">NIP</th>
                            <th class="text-center" width="150">Jumlah Anak Wali</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${dosenHtml || '<tr><td colspan="4" class="text-center text-muted">Tidak ada data dosen</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;

        document.getElementById('modalDetailContent').innerHTML = html;
    }
</script>
@endpush

@endsection
