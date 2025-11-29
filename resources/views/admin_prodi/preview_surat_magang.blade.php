@extends('layouts.admin_prodi')

@section('title', 'Preview Surat Pengantar Magang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Preview Surat Pengantar</h1>
            <p class="text-muted mb-0">Tampilan resmi dokumen pada halaman terpisah.</p>
        </div>
        <div>
            <a href="{{ route('admin_prodi.surat.manage') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="paper-preview mx-auto shadow-sm">
        <div class="text-center mb-4 border-bottom border-dark pb-3">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo" style="height: 80px;" class="me-3">
                <div class="text-center">
                    <h6 class="mb-0 fw-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h6>
                    <h5 class="mb-0 fw-bold">UNIVERSITAS TRUNOJOYO MADURA</h5>
                    <h4 class="mb-0 fw-bold">FAKULTAS TEKNIK</h4>
                    <small class="d-block">Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506</small>
                </div>
            </div>
        </div>

        <h5 class="text-center fw-bold text-decoration-underline mb-4">FORM PENGAJUAN SURAT PENGANTAR</h5>

        <table class="table table-borderless table-sm mb-4">
            <tr>
                <td width="30%">Nama</td>
                <td width="2%">:</td>
                <td>
                    @foreach($dataMahasiswa as $idx => $mhs)
                        <div>{{ $idx + 1 }}. {{ $mhs['nama'] ?? '' }} ({{ $mhs['nim'] ?? '' }})</div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>:</td>
                <td>{{ $mahasiswa?->prodi->Nama_Prodi ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td>
                <td>:</td>
                <td>{{ $dosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Instansi Tujuan</td>
                <td>:</td>
                <td><strong>{{ $surat->Nama_Instansi ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Periode Magang</td>
                <td>:</td>
                <td>
                    @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d 
                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <div class="row mt-5">
            <div class="col-6 text-center">
                <p class="mb-5">Menyetujui,<br>Koordinator KP/TA</p>
                @if($surat->Qr_code)
                    <img src="{{ asset('storage/' . $surat->Qr_code) }}" style="max-width: 100px;">
                @else
                    <div class="border rounded p-2 d-inline-block bg-light text-muted mb-2" style="border-style: dashed !important;">
                        Belum disetujui
                    </div>
                @endif
                <p class="fw-bold mb-0">{{ $surat->koordinator->Nama_Dosen ?? '[Nama Kaprodi]' }}</p>
                <p class="small">NIP. {{ $surat->koordinator->NIP ?? '...' }}</p>
            </div>
            <div class="col-6 text-center">
                <p class="mb-5">Bangkalan, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Pemohon</p>
                @if($surat->Foto_ttd)
                    <img src="{{ asset('storage/' . $surat->Foto_ttd) }}" height="60" class="mb-2">
                @else
                    <div class="mb-5"></div>
                @endif
                <p class="fw-bold mb-0">{{ $surat->tugasSurat->pemberiTugas->mahasiswa->Nama_Mahasiswa ?? '' }}</p>
                <p class="small">NIM. {{ $surat->tugasSurat->pemberiTugas->mahasiswa->NIM ?? '' }}</p>
            </div>
        </div>
    </div>
</div>

<style>
    .paper-preview {
        background: white;
        width: 100%;
        max-width: 210mm;
        min-height: 297mm;
        padding: 20mm;
        margin: 0 auto;
        border: 1px solid #d3d3d3;
        font-family: 'Times New Roman', Times, serif;
    }
</style>
@endsection
