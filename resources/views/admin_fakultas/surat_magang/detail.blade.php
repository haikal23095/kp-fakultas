@extends('layouts.admin_fakultas')

@section('title', 'Detail Surat Magang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail Form Pengantar KP/Magang</h1>
        <p class="mb-0 text-muted">
            <a href="{{ route('admin_fakultas.surat.magang') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </p>
    </div>
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
    {{-- Informasi Surat --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Surat</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Tanggal Pengajuan:</div>
                    <div class="col-md-8">
                        @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                            {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y H:i') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Mahasiswa:</div>
                    <div class="col-md-8">
                        @foreach($dataMahasiswa as $idx => $mhs)
                        <div class="mb-2">
                            {{ $idx + 1 }}. <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                            <small class="text-muted">NIM: {{ $mhs['nim'] ?? '' }} | Angkatan: {{ $mhs['angkatan'] ?? '-' }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Prodi:</div>
                    <div class="col-md-8">{{ $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing:</div>
                    <div class="col-md-8">
                        @if($dataDosenPembimbing)
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_1']))
                                <div>1. {{ $dataDosenPembimbing['dosen_pembimbing_1'] }}</div>
                            @endif
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_2']) && $dataDosenPembimbing['dosen_pembimbing_2'])
                                <div>2. {{ $dataDosenPembimbing['dosen_pembimbing_2'] }}</div>
                            @endif
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Instansi:</div>
                    <div class="col-md-8">{{ $surat->Nama_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Alamat Instansi:</div>
                    <div class="col-md-8">{{ $surat->Alamat_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Judul Penelitian:</div>
                    <div class="col-md-8">{{ $surat->Judul_Penelitian ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Periode Magang:</div>
                    <div class="col-md-8">
                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Koordinator yang Menyetujui:</div>
                    <div class="col-md-8">
                        {{ $surat->koordinator?->Nama_Dosen ?? 'N/A' }}
                        @if($surat->koordinator)
                            <br><small class="text-muted">NIP: {{ $surat->koordinator->NIP }}</small>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dokumen Proposal:</div>
                    <div class="col-md-8">
                        @if($surat->Dokumen_Proposal)
                            <a href="{{ route('admin_fakultas.surat_magang.download', $surat->id_no) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Unduh Proposal
                            </a>
                        @else
                            <span class="text-muted">Tidak ada dokumen</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Preview Form:</div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="togglePreview()">
                            <i class="fas fa-file-alt"></i> Lihat Form Pengajuan
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="togglePreviewSuratResmi()">
                            <i class="fas fa-file-contract"></i> Lihat Surat Pengantar Magang
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Foto Tanda Tangan:</div>
                    <div class="col-md-8">
                        @if($surat->Foto_ttd && !empty(trim($surat->Foto_ttd)))
                            @php
                                $ttdUrl = asset('storage/' . $surat->Foto_ttd);
                                $ttdFilePath = storage_path('app/public/' . $surat->Foto_ttd);
                                $fileExists = file_exists($ttdFilePath);
                            @endphp
                            @if($fileExists)
                            <img src="{{ $ttdUrl }}" 
                                 alt="Tanda Tangan" 
                                 style="max-height: 80px; border: 1px solid #ddd; padding: 5px; background: white;">
                            @else
                            <span class="text-muted">File tidak ditemukan</span>
                            @endif
                        @else
                            <span class="text-muted">Tidak ada foto tanda tangan</span>
                        @endif
                    </div>
                </div>

                @if($surat->Qr_code)
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">QR Code Verifikasi:</div>
                    <div class="col-md-8">
                        @php
                            $qrPath = storage_path('app/public/' . $surat->Qr_code);
                            $qrExists = file_exists($qrPath);
                        @endphp
                        
                        @if($qrExists)
                            <img src="{{ asset('storage/' . $surat->Qr_code) }}" 
                                 alt="QR Code" 
                                 style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                            <br><small class="text-muted">Scan untuk verifikasi surat</small>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <small>File QR code tidak ditemukan. Silakan regenerate QR code.</small>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Preview Form Pengajuan Surat Pengantar --}}
        <div id="previewSurat" style="display: none;" class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-eye me-2"></i>Preview Form Pengajuan Surat Pengantar
                </h6>
            </div>
            <div class="card-body">
                <div class="preview-document" style="border: 1px solid #ddd; padding: 20px; background: white; font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.6; max-width: 700px; margin: 0 auto;">
                    {{-- Header --}}
                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                        <div style="margin-left: 70px;">
                            <strong style="display: block; font-size: 11pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                            <strong style="display: block; font-size: 12pt;">UNIVERSITAS TRUNOJOYO MADURA</strong>
                            <strong style="display: block; font-size: 13pt;">FAKULTAS TEKNIK</strong>
                            <div style="font-size: 9pt; margin-top: 5px;">
                                Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    {{-- Judul --}}
                    <p style="text-align: center; font-weight: bold; margin: 20px 0; text-decoration: underline;">FORM PENGAJUAN SURAT PENGANTAR</p>

                    {{-- Tabel Data --}}
                    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
                        <tr>
                            <td style="width: 30%; vertical-align: top; padding: 3px 0;">Nama</td>
                            <td style="width: 5%; vertical-align: top; padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">
                                @foreach($dataMahasiswa as $idx => $mhs)
                                <div style="margin-bottom: 5px;">
                                    <strong>{{ $idx + 1 }}. {{ $mhs['nama'] ?? '' }}</strong><br>
                                    <small>NIM: {{ $mhs['nim'] ?? '' }} | Angkatan: {{ $mhs['angkatan'] ?? '-' }}</small>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">Program Studi</td>
                            <td style="padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">
                                @php
                                    $prodiName = $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi 
                                        ?? ($dataMahasiswa[0]['program-studi'] ?? $dataMahasiswa[0]['jurusan'] ?? 'N/A');
                                @endphp
                                {{ $prodiName }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">Dosen Pembimbing</td>
                            <td style="padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">{{ $dataDosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
                        </tr>
                        @if(isset($dataDosenPembimbing['dosen_pembimbing_2']) && $dataDosenPembimbing['dosen_pembimbing_2'])
                        <tr>
                            <td style="padding: 3px 0;">Dosen Pembimbing 2</td>
                            <td style="padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">{{ $dataDosenPembimbing['dosen_pembimbing_2'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="vertical-align: top; padding: 3px 0;">Surat Pengantar*</td>
                            <td style="vertical-align: top; padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">
                                1. Pengantar Kerja Praktek<br>
                                2. Pengantar TA<br>
                                3. Pengantar Dosen Pembimbing I TA<br>
                                4. Magang
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;">Instansi/Perusahaan</td>
                            <td style="padding: 3px 0;">:</td>
                            <td style="padding: 3px 0;">{{ $surat->Nama_Instansi ?? '-' }}</td>
                        </tr>
                    </table>

                    {{-- Bagian Khusus Magang --}}
                    <div style="margin-top: 15px;">
                        <strong><u>Isian berikut utk pengantar Magang</u></strong>
                        <table style="width: 100%; margin-top: 5px; border-collapse: collapse;">
                            <tr>
                                <td style="width: 30%; padding: 3px 0;">Judul Penelitian</td>
                                <td style="width: 5%; padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;">{{ $surat->Judul_Penelitian ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0;">Jangka waktu penelitian</td>
                                <td style="padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;">
                                    @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d 
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0;">Identitas Surat Balasan**</td>
                                <td style="padding: 3px 0;">:</td>
                                <td style="padding: 3px 0;"></td>
                            </tr>
                        </table>
                    </div>

                    {{-- Tanda Tangan --}}
                    <div style="margin-top: 30px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    <p style="margin: 0 0 5px 0;">Menyetujui<br>Koordinator KP/TA</p>
                                    @if($surat->Qr_code)
                                    <div style="margin: 10px 0;">
                                        <img src="{{ asset('storage/' . $surat->Qr_code) }}" 
                                             alt="QR Code" 
                                             style="width: 80px; height: 80px; border: 1px solid #000; padding: 3px;">
                                    </div>
                                    @else
                                    <div style="height: 60px;"></div>
                                    @endif
                                    <p style="margin: 0;">( {{ $surat->koordinator->Nama_Dosen ?? '[Nama Kaprodi]' }} )</p>
                                    <p style="margin: 0;">NIP. {{ $surat->koordinator->NIP ?? '...' }}</p>
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin: 0 0 5px 0;">Bangkalan, {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                    <p style="margin: 0 0 5px 0;">Pemohon</p>
                                    @if($surat->Foto_ttd && !empty(trim($surat->Foto_ttd)))
                                        @php
                                            $ttdUrl = asset('storage/' . $surat->Foto_ttd);
                                            $ttdFilePath = storage_path('app/public/' . $surat->Foto_ttd);
                                            $fileExists = file_exists($ttdFilePath);
                                        @endphp
                                        @if($fileExists)
                                        <img src="{{ $ttdUrl }}" 
                                             alt="TTD" 
                                             style="max-height: 60px; max-width: 150px; display: block; margin: 0 auto; background: white;"
                                             onerror="console.error('Failed to load:', this.src);">
                                        @else
                                        <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                                            <small style="color: #999; font-size: 8pt;">[File TTD tidak tersedia]</small>
                                        </div>
                                        @endif
                                    @else
                                    <div style="height: 60px;"></div>
                                    @endif
                                    @php
                                        $mahasiswaPemohon = $surat->tugasSurat?->pemberiTugas?->mahasiswa;
                                        $namaPemohon = $mahasiswaPemohon?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                                        $nimPemohon = $mahasiswaPemohon?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                                    @endphp
                                    <p style="margin: 0;">( {{ $namaPemohon }} )</p>
                                    <p style="margin: 5px 0 0 0;">NIM. {{ $nimPemohon }}</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <hr style="border-top: 1px dashed #000; margin-top: 15px;">
                    <small style="font-size: 9pt;">
                        Cat: *Tulis alamat Instansi/perusahaan yg dituju<br>
                        **Diisi untuk permohonan kedua dan seterusnya
                    </small>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary" onclick="togglePreview()">
                        <i class="fas fa-times"></i> Tutup Preview
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak Surat
                    </button>
                </div>
            </div>
        </div>

        {{-- Preview Surat Pengantar Magang Resmi --}}
        <div id="previewSuratResmi" style="display: none;" class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-contract me-2"></i>Preview Surat Pengantar Magang (Resmi)
                </h6>
            </div>
            <div class="card-body">
                <div class="preview-document" style="border: 1px solid #ddd; padding: 30px; background: white; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                    {{-- Header --}}
                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                        <div style="margin-left: 70px;">
                            <strong style="display: block; font-size: 11pt;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                            <strong style="display: block; font-size: 12pt;">UNIVERSITAS TRUNOJOYO MADURA</strong>
                            <strong style="display: block; font-size: 13pt;">FAKULTAS TEKNIK</strong>
                            <div style="font-size: 9pt; margin-top: 5px;">
                                Jl. Raya Telang, PO.Box. 2 Kamal, Bangkalan – Madura<br>
                                Telp : (031) 3011146, Fax. (031) 3011506<br>
                                Laman : www.trunojoyo.ac.id
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    {{-- Nomor Surat --}}
                    <div style="margin: 20px 0;">
                        <table style="width: 100%; font-size: 11pt;">
                            <tr>
                                <td style="width: 25%;">Nomor</td>
                                <td style="width: 2%;">:</td>
                                <td><strong>{{ $surat->tugasSurat->Nomor_Surat ?? '[Nomor Surat Belum Diberikan]' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Perihal</td>
                                <td>:</td>
                                <td><strong>Permohonan Izin Magang Mandiri</strong></td>
                            </tr>
                        </table>
                    </div>

                    {{-- Tanggal Surat --}}
                    <div style="text-align: right; margin: 20px 0 30px 0;">
                        {{ \Carbon\Carbon::now()->format('d F Y') }}
                    </div>

                    {{-- Kepada --}}
                    <div style="margin: 20px 0;">
                        <p style="margin: 0;">Yth. Pimpinan {{ $surat->Nama_Instansi ?? '[Nama Instansi]' }}</p>
                        <p style="margin: 0;">{{ $surat->Alamat_Instansi ?? '[Alamat Instansi]' }}</p>
                    </div>

                    {{-- Isi Surat --}}
                    <p style="text-align: justify; text-indent: 50px; margin: 20px 0; line-height: 1.8;">
                        Sehubungan dalam memperkenalkan mahasiswa pada dunia kerja sesuai bidang masing-masing, maka 
                        sesuai ketentuan Program Merdeka Belajar - Kampus Merdeka (MBKM) mahasiswa diperkenankan 
                        melaksanakan magang. Guna memperlancar kegiatan tersebut, kami mohon Bapak/Ibu untuk memberikan 
                        izin kepada mahasiswa kami untuk dapat melaksanakan kegiatan magang di perusahaan tersebut pada 
                        tanggal {{ $surat->Tanggal_Mulai ? \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d F') : '[Tanggal Mulai]' }} s.d. 
                        {{ $surat->Tanggal_Selesai ? \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d F Y') : '[Tanggal Selesai]' }}.
                    </p>

                    <p style="margin: 15px 0;">Adapun mahasiswa tersebut adalah:</p>

                    {{-- Tabel Mahasiswa --}}
                    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 5%;">No</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 40%;">Nama</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 35%;">Program Studi</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 20%;">No. WA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataMahasiswa as $idx => $mhs)
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $idx + 1 }}.</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                                    <small>NIM {{ $mhs['nim'] ?? '' }}</small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    @php
                                        $prodiName = $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi 
                                            ?? ($mhs['program-studi'] ?? $mhs['jurusan'] ?? 'Teknik Industri');
                                    @endphp
                                    {{ $prodiName }}
                                </td>
                                <td style="border: 1px solid #000; padding: 8px;">{{ $mhs['no_wa'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                        Besar harapan kami dapat menerima konfirmasi kesediaan menerima atau menolak pengajuan Magang 
                        Mandiri ini maksimal 14 (empat belas) hari dari tanggal surat ini dikeluarkan.
                    </p>

                    <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                        Demikian, atas perhatian dan bantuannya kami ucapkan terima kasih.
                    </p>

                    {{-- Tanda Tangan Dekan (tanpa QR Code) --}}
                    <div style="margin-top: 50px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    {{-- QR Code akan muncul setelah Dekan menyetujui --}}
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin: 0 0 5px 0;">Dekan Fakultas Teknik,</p>
                                    <div style="height: 100px;"></div>
                                    @php
                                        // Ambil Dekan dari fakultas mahasiswa yang mengajukan
                                        $mahasiswaPengaju = $surat->tugasSurat?->pemberiTugas?->mahasiswa;
                                        $fakultas = $mahasiswaPengaju?->prodi?->fakultas;
                                        
                                        // Ambil data Dekan langsung dari Id_Dekan di tabel Fakultas
                                        $dekan = null;
                                        if ($fakultas && $fakultas->Id_Dekan) {
                                            $dekan = \App\Models\Dosen::find($fakultas->Id_Dekan);
                                        }
                                        
                                        $namaDekan = $dekan?->Nama_Dosen ?? 'Dr. Budi Hartono, S.Kom., M.Kom.';
                                        $nipDekan = $dekan?->NIP ?? '198503152010121001';
                                    @endphp
                                    <p style="margin: 0;"><strong><u>{{ $namaDekan }}</u></strong></p>
                                    <p style="margin: 0;">NIP {{ $nipDekan }}</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary" onclick="togglePreviewSuratResmi()">
                        <i class="fas fa-times"></i> Tutup Preview
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printSuratResmi()">
                        <i class="fas fa-print"></i> Cetak Surat
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Penomoran --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-signature me-2"></i>Berikan Nomor Surat
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin_fakultas.surat_magang.assign', $surat->id_no) }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Setelah nomor surat diberikan, status akan berubah menjadi <strong>"Diajukan-ke-dekan"</strong></small>
                    </div>

                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label fw-bold">
                            Nomor Surat <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nomor_surat') is-invalid @enderror" 
                               id="nomor_surat" 
                               name="nomor_surat" 
                               placeholder="Contoh: 123/UN46.1/KM/2025"
                               required>
                        @error('nomor_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Format: [Nomor]/UN46.1/KM/[Tahun]
                        </small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin memberikan nomor surat ini?')">
                            <i class="fas fa-check me-2"></i>Berikan Nomor & Teruskan ke Dekan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-clipboard-list me-2"></i>Status Surat
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Status Saat Ini:</small>
                    <br>
                    <span class="badge bg-primary">
                        <i class="fas fa-file-alt me-1"></i> {{ $surat->Status }}
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Acc Koordinator:</small>
                    <br>
                    @if($surat->Acc_Koordinator)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-warning"><i class="fas fa-clock"></i> Belum</span>
                    @endif
                </div>
                <div>
                    <small class="text-muted">Acc Dekan:</small>
                    <br>
                    @if($surat->Acc_Dekan)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePreview() {
    var preview = document.getElementById('previewSurat');
    var previewResmi = document.getElementById('previewSuratResmi');
    if (preview.style.display === 'none') {
        preview.style.display = 'block';
        previewResmi.style.display = 'none';
        preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        preview.style.display = 'none';
    }
}

function togglePreviewSuratResmi() {
    var preview = document.getElementById('previewSurat');
    var previewResmi = document.getElementById('previewSuratResmi');
    if (previewResmi.style.display === 'none') {
        previewResmi.style.display = 'block';
        preview.style.display = 'none';
        previewResmi.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        previewResmi.style.display = 'none';
    }
}

function printSuratResmi() {
    var printContent = document.getElementById('previewSuratResmi').innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}
</script>

<style>
@media print {
    /* Hide everything except preview */
    body * {
        visibility: hidden;
    }
    #previewSurat, #previewSurat * {
        visibility: visible;
    }
    #previewSurat {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    /* Hide buttons when printing */
    #previewSurat .text-center.mt-3 {
        display: none !important;
    }
    /* Hide card borders */
    #previewSurat.card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        display: none !important;
    }
}
</style>

@endsection