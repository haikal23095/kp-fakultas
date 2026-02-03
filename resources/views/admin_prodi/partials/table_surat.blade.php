<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-secondary text-uppercase small fw-bold">
            <tr>
                <th class="px-4 py-3">Mahasiswa</th>
                <th class="py-3">Keterangan</th>
                <th class="py-3">Tanggal</th>
                <th class="py-3 text-center">Status</th>
                <th class="py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suratData as $surat)
                @php
                    $mahasiswa = $surat->pemberiTugas->mahasiswa ?? null;
                    $status = strtolower(trim($surat->Status ?? ''));
                    $nomor = $surat->Nomor_Surat ?? null;
                    
                    // Logic for status badge
                    $badgeClass = 'bg-secondary';
                    $statusText = $surat->Status;

                    if ($status === 'dikerjakan-admin' || $status === 'baru' || $status === 'diterima admin' || $status === 'diajukan-ke-koordinator') {
                        $badgeClass = 'bg-warning text-dark';
                        $statusText = 'Perlu Proses';
                    } elseif ($status === 'diajukan-ke-dekan') {
                        $badgeClass = 'bg-info text-white';
                        $statusText = 'Menunggu TTD Dekan';
                    } elseif ($status === 'success') {
                        $badgeClass = 'bg-success';
                    }
                @endphp
                <tr>
                    <td class="px-4">
                        <div class="fw-bold text-dark">{{ $mahasiswa?->Nama_Mahasiswa ?? 'N/A' }}</div>
                        <div class="small text-muted">NIM: {{ $mahasiswa?->NIM ?? 'N/A' }}</div>
                    </td>
                    <td>
                        @if($type === 'magang')
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $surat->Nama_Instansi }}">
                                <strong>Instansi:</strong> {{ $surat->Nama_Instansi ?? '-' }}
                            </div>
                        @else
                            <div class="small">Surat Keterangan Aktif</div>
                        @endif
                    </td>
                    <td>
                        <div class="small text-muted">
                            {{ $surat->Tanggal_Diberikan ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan)->format('d M Y') : '-' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        @if($nomor)
                            <div class="small mt-1 text-muted">{{ $nomor }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @if($type === 'magang')
                                <a href="{{ route('admin_prodi.surat.preview_magang', $surat->id_no) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                            @endif
                            
                            @if(!$nomor && ($status === 'dikerjakan-admin' || $status === 'baru' || $status === 'diterima admin' || $status === 'diajukan-ke-koordinator'))
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#nomorModal{{ $surat->id_no }}">
                                    <i class="fas fa-plus-circle"></i> No. Surat
                                </button>
                            @endif
                        </div>

                        {{-- Modal Tambah Nomor Surat --}}
                        @if(!$nomor)
                        <div class="modal fade text-start" id="nomorModal{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title fw-bold">Input Nomor Surat</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin_prodi.surat.add_nomor', $surat->id_no) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Mahasiswa</label>
                                                <input type="text" class="form-control bg-light" value="{{ $mahasiswa?->Nama_Mahasiswa }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nomor_surat" placeholder="Contoh: 456/UN46.FT/KM/2025" required>
                                            </div>
                                            <div class="alert alert-info small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Surat akan diteruskan ke Dekan untuk proses tanda tangan elektronik/manual setelah nomor disimpan.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-paper-plane me-1"></i> Simpan & Teruskan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>Tidak ada data surat untuk kategori ini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
