@extends('layouts.dosen')

@section('title', 'SK Dosen Wali')

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
        <h1 class="h3 fw-bold mb-0">SK Dosen Wali</h1>
        <p class="mb-0 text-muted">Daftar SK Dosen Wali yang melibatkan Anda</p>
    </div>
    <a href="{{ route('dosen.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($filteredSK->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Belum ada SK Dosen Wali yang melibatkan Anda</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Nomor SK</th>
                            <th style="width: 20%;">Semester/Tahun</th>
                            <th style="width: 15%;">Tanggal Persetujuan</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredSK as $index => $sk)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $sk->Nomor_Surat ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $sk->Semester ?? 'Ganjil' }}</span>
                                    <span class="text-muted">{{ $sk->Tahun_Akademik ?? '2023/2024' }}</span>
                                </td>
                                <td>
                                    @if($sk->{'Tanggal-Persetujuan-Dekan'})
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($sk->{'Tanggal-Persetujuan-Dekan'})->format('d M Y') }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $sk->Status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success" onclick="showSKPreview({{ $sk->No }})">
                                        <i class="fas fa-file-pdf me-1"></i>Lihat SK
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Info Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Informasi Dosen</h6>
                        <p class="mb-0 small text-muted">
                            <strong>Nama:</strong> {{ $dosen->Nama_Dosen ?? '-' }}<br>
                            <strong>NIP:</strong> {{ $dosen->NIP ?? '-' }}<br>
                            <strong>Prodi:</strong> {{ $dosen->prodi->Nama_Prodi ?? '-' }}
                        </p>
                    </div>
                </div>
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
                    <i class="fas fa-file-pdf me-2"></i>Preview SK Dosen Wali
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
                    <div id="previewSKContent" style="background: white; box-shadow: 0 0 20px rgba(0,0,0,0.3);"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showSKPreview(skNo) {
        const modal = new bootstrap.Modal(document.getElementById('modalPreviewSK'));
        modal.show();
        
        // Set download link
        document.getElementById('downloadSKLink').href = `{{ url('dosen/sk/dosen-wali') }}/${skNo}/download`;
        
        // Fetch SK detail
        fetch(`{{ url('dosen/sk/dosen-wali') }}/${skNo}/detail`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderSKPreview(data.sk, data.dekanName, data.dekanNip);
                } else {
                    alert('Gagal memuat detail SK');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat detail SK');
            });
    }

    function renderSKPreview(sk, dekanName, dekanNip) {
        const dosenList = sk.Data_Dosen_Wali || [];
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';
        const qrCodePath = sk.QR_Code ? `{{ asset('storage') }}/${sk.QR_Code}` : null;
        const tanggalTTD = sk['Tanggal-Persetujuan-Dekan'] ? new Date(sk['Tanggal-Persetujuan-Dekan']).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});

        // Kelompokkan dosen per prodi
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

        // Generate lampiran HTML untuk setiap prodi
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
</script>
@endpush

@endsection
