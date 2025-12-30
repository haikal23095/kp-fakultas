@extends('layouts.dekan')

@section('title', 'Persetujuan Legalisir')

@section('content')
    <h1 class="h3 fw-bold mb-4">Persetujuan Legalisir</h1>

    {{-- Flash Messages --}}
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Legalisir Menunggu Tanda Tangan</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-0 mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Berkas fisik telah diberi nomor surat oleh admin. Setelah Anda menandatangani secara offline, admin akan memberi stampel dan mahasiswa dapat mengambil berkas.
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Nomor Surat</th>
                            <th scope="col">Mahasiswa</th>
                            <th scope="col">Jenis Dokumen</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Tanggal Input</th>
                            <th scope="col" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarLegalisir as $legalisir)
                            <tr>
                                <td>
                                    @if($legalisir->Nomor_Surat_Legalisir)
                                        <span class="badge bg-primary fw-bold">{{ $legalisir->Nomor_Surat_Legalisir }}</span>
                                    @else
                                        <span class="text-muted fst-italic small">Belum ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $legalisir->user->Name_User ?? '-' }}</div>
                                    <small class="text-muted">
                                        NIM: {{ $legalisir->user->mahasiswa->NIM ?? '-' }}<br>
                                        {{ $legalisir->user->mahasiswa->prodi->Nama_Prodi ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $legalisir->Jenis_Dokumen }}</span>
                                </td>
                                <td>{{ $legalisir->Jumlah_Salinan }} Lembar</td>
                                <td>
                                    @if($legalisir->tugasSurat)
                                        {{ $legalisir->tugasSurat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">Menunggu TTD</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada legalisir yang menunggu tanda tangan.</p>
                                    <small class="text-muted d-block mt-2">
                                        Berkas legalisir akan muncul di sini setelah admin memberi nomor surat.
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
