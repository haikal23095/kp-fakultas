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
                <strong>Catatan:</strong> Klik "Tanda Tangan" untuk membubuhkan QR TTD digital Anda pada file scan. Siapa TTD lebih dulu, nama beliau yang akan tercantum pada dokumen.
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Mahasiswa</th>
                            <th scope="col">Jenis Dokumen</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">File Scan</th>
                            <th scope="col">Tanggal Input</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarLegalisir as $legalisir)
                            <tr>
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
                                    @if($legalisir->File_Scan_Path)
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalPreview{{ $legalisir->id_no }}">
                                            <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                                        </button>
                                    @else
                                        <small class="text-muted">Tidak ada file</small>
                                    @endif
                                </td>
                                <td>
                                    @if($legalisir->tugasSurat)
                                        {{ $legalisir->tugasSurat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('dekan.legalisir.approve', $legalisir->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Tanda tangani dokumen legalisir ini dengan QR digital Anda?')">
                                            <i class="fas fa-signature me-1"></i>Tanda Tangan
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- MODAL PREVIEW PDF --}}
                            @if($legalisir->File_Scan_Path)
                            <div class="modal fade" id="modalPreview{{ $legalisir->id_no }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title fw-bold">Preview File Scan - {{ $legalisir->user->Name_User ?? 'N/A' }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <iframe src="{{ asset('storage/' . $legalisir->File_Scan_Path) }}" 
                                                    style="width:100%; height:600px; border:none;"></iframe>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {{-- END MODAL --}}

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada legalisir yang menunggu tanda tangan.</p>
                                    <small class="text-muted d-block mt-2">
                                        Berkas legalisir akan muncul di sini setelah admin mengirim untuk TTD.
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
