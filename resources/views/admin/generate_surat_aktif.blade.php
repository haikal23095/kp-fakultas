@extends('layouts.admin')

@section('title', 'Generate Surat Keterangan Aktif')

{{-- CSS Khusus untuk Pratinjau --}}
@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #fdfdfd;
        color: #000;
        border: 1px solid #ccc;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 12pt;
        line-height: 1.5;
        max-width: 800px; /* Mirip A4 */
        margin: 20px auto;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
    }
    .preview-header img {
        width: 100px;
        position: absolute; /* Posisikan logo */
        left: 40px;
        top: 30px;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
        font-size: 14pt;
    }
    .preview-header .line-1 { font-size: 13pt; }
    .preview-header .line-2 { font-size: 15pt; }
    .preview-header .line-3 { font-size: 15pt; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
    }
    .preview-title {
        font-weight: bold;
        font-size: 14pt;
        margin-top: 30px;
        text-align: center;
        text-decoration: underline;
    }
    .preview-body {
        margin-top: 25px;
    }
    .preview-table {
        margin-top: 15px;
        width: 100%;
        font-size: 12pt;
        border-collapse: collapse; /* Penting untuk layout rapi */
    }
    .preview-table td {
        padding: 4px 0px;
        vertical-align: top;
    }
    .preview-table td:nth-child(1) { width: 25%; }
    .preview-table td:nth-child(2) { width: 2%; }
    .preview-table td:nth-child(3) { width: 73%; }
    
    .preview-signature {
        font-size: 12pt;
        margin-top: 40px;
        width: 40%; /* Tanda tangan di kanan */
        margin-left: 60%; /* Posisikan ke kanan */
    }
</style>
@endpush

@section('content')

<h1 class="h3 mb-2 text-gray-800">Generate Surat Keterangan Aktif</h1>
<p class="mb-4">Preview dan finalisasi surat untuk mahasiswa.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Pratinjau Surat</h6>
        <a href="{{ route('admin.surat.detail', $surat->Id_Tugas_Surat) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Kembali ke Detail
        </a>
    </div>
    <div class="card-body bg-light">
        
        {{-- Mengambil data dari controller --}}
        @php
            // Data Dekan
            $namaDekan = optional($dekan)->Nama_Dosen ?? '[Nama Dekan Tidak Ditemukan]';
            $nipDekan = optional($dekan)->NIP ?? '[NIP Dekan Tidak Ditemukan]';

            // Data Mahasiswa (tampil saja, tidak editable)
            $namaMhs = optional($detailPengaju)->Nama_Mahasiswa ?? '[Nama Mahasiswa]';
            $nimMhs = optional($detailPengaju)->NIM ?? '[NIM]';
            $prodiMhs = optional($detailPengaju->prodi)->Nama_Prodi ?? '[Program Studi]';

            // Data Spesifik dari JSON -> semester harus berupa angka (5,6,7)
            $rawSemester = $surat->data_spesifik['semester'] ?? ($semester ?? null);
            $allowed = ['5','6','7','Semester 5','Semester 6','Semester 7'];
            $semester = '[Semester]';
            if ($rawSemester !== null) {
                // cari angka 5/6/7 di string jika ada
                if (preg_match('/\b([567])\b/', (string)$rawSemester, $m)) {
                    $semester = 'Semester ' . $m[1];
                } elseif (in_array((string)$rawSemester, ['5','6','7'])) {
                    $semester = 'Semester ' . $rawSemester;
                } else {
                    // fallback jika value tidak sesuai, gunakan 'Semester 5'
                    $semester = 'Semester 5';
                }
            } else {
                // default apabila tidak tersedia di data_spesifik
                $semester = 'Semester 5';
            }

            // gunakan tahun_akademik dari controller jika tersedia, fallback ke data_spesifik
            $tahunAkademik = $tahun_akademik ?? ($surat->data_spesifik['tahun_akademik'] ?? $tanggalHariIni->format('Y') . '/' . ($tanggalHariIni->format('Y')+1));
        @endphp

        <form action="{{ route('admin.surat.finalize.aktif', $surat->Id_Tugas_Surat) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyelesaikan dan mengarsip surat ini?');">
            @csrf
            
            {{-- Dokumen Preview (seperti PDF) --}}
            <div class="preview-document">
                
                <div class="preview-header">
                    {{-- Logo bisa ditambahkan jika ada di public/images/logo.png --}}
                    {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo"> --}}
                    <strong class="line-1">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                    <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
                    <strong class="line-3">FAKULTAS TEKNIK</strong>
                    <div class="address">
                        Jl. Raya Telang. PO.Box. 2 Kamal, Bangkalan Madura<br>
                        Telp (031) 3011146, Fax. (031) 3011506, Laman www.trunojoyo.ac.id
                    </div>
                </div>

                <p class="preview-title">SURAT KETERANGAN</p>

                {{-- Input Nomor Surat --}}
                <div class="my-3">
                    <label for="nomor_surat" class="form-label font-weight-bold">Nomor Surat:</label>
                    <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" 
                           value="{{ $surat->Nomor_Surat ?? 'B/       /UN46.3.4/KM.01.00/' . $tanggalHariIni->format('Y') }}" 
                           required>
                    @error('nomor_surat')
                        <div class="small text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="preview-body">
                    <p>Yang bertanda tangan dibawah ini:</p>

                    <table class="preview-table">
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td><strong>{{ $namaDekan }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIP</td>
                            <td>:</td>
                            <td>{{ $nipDekan }}</td>
                        </tr>
                        <tr>
                            <td>Pangkat/golongan</td>
                            <td>:</td>
                            <td>Penata Tk. I/IIId</td> {{-- (Bisa dibuat dinamis jika ada di DB) --}}
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>:</td>
                            <td>Dekan Fakultas Teknik Universitas Trunojoyo Madura</td>
                        </tr>
                    </table>

                    <p style="margin-top: 15px;">menerangkan bahwa:</p>

                    <table class="preview-table">
                        <tr>
                            <td>nama</td>
                            <td>:</td>
                            <td><strong>{{ $namaMhs }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td>:</td>
                            <td>{{ $nimMhs }}</td>
                        </tr>
                        <tr>
                            <td>semester</td>
                            <td>:</td>
                            <td>{{ $semester }}</td>
                        </tr>
                    </table>

                    <p style="margin-top: 15px; text-align: justify;">
                        adalah benar mahasiswa aktif di Program Studi S1 {{ $prodiMhs }} Jurusan {{ $prodiMhs }}
                        Fakultas Teknik Universitas Trunojoyo Madura {{ $semester }} — Tahun Akademik {{ $tahunAkademik }}.
                    </p>

                    <p>
                        Demikian Surat Keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <div class="preview-signature">
                        {{ $tanggalHariIni->isoFormat('D MMMM Y') }}
                        <br>
                        Dekan Fakultas Teknik,
                        
                        <div style="height: 80px;">
                            {{-- Tempat untuk TTD Digital/Kosong --}}
                        </div>
                        
                        <strong>{{ $namaDekan }}</strong>
                        <br>
                        NIP {{ $nipDekan }}
                    </div>
                </div>
            </div>
            
            {{-- Tombol Aksi Finalisasi --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-check-circle me-2"></i> Finalisasi & Selesaikan Tugas
                </button>
            </div>

        </form>
    </div>
</div>

@endsection