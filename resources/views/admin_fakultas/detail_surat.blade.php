@extends('layouts.admin_fakultas')

@section('title', 'Detail Surat Keterangan Aktif')

@section('content')
@php 
    $status = trim(optional($surat)->Status ?? ''); 
@endphp

<div class="mb-3">
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali ke Manajemen Surat
    </a>
</div>

<div class="card mb-4 shadow-sm">
	<div class="card-body d-flex align-items-center justify-content-between">
		<div>
			<h4 class="mb-1"><i class="fa fa-file-alt text-primary me-2"></i> Detail Surat Keterangan Aktif</h4>
			<div class="text-muted small">Nomor: <strong>{{ optional($surat)->Nomor_Surat ?? 'N/A' }}</strong> &middot; ID: <strong>{{ optional($surat)->id_no ?? '-' }}</strong></div>
            
            <div class="mt-2 d-flex align-items-center flex-wrap gap-2">
                @if($surat->is_urgent)
                    <span class="badge bg-danger blink-badge"><i class="fas fa-exclamation-circle me-1"></i> URGENT</span>
                    @if($surat->urgent_reason)
                        <small class="text-danger">Alasan: {{ $surat->urgent_reason }}</small>
                    @endif
                @endif
                
                {{-- Tombol Toggle Urgent --}}
                <button type="button" class="btn btn-sm {{ $surat->is_urgent ? 'btn-outline-secondary' : 'btn-outline-danger' }}" 
                        data-bs-toggle="modal" data-bs-target="#urgentModal"
                        title="{{ $surat->is_urgent ? 'Hapus status urgent' : 'Tandai sebagai urgent' }}">
                    <i class="fas {{ $surat->is_urgent ? 'fa-minus-circle' : 'fa-exclamation-circle' }}"></i>
                    {{ $surat->is_urgent ? 'Batalkan Prioritas' : 'Set Prioritas' }}
                </button>
            </div>
		</div>

		<div class="text-end">
			@if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui' || strtolower($status) === 'success')
				<span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
			@elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
				<span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
			@elseif(strtolower($status) === 'proses' || strtolower($status) === 'dikerjakan-admin')
				<span class="badge rounded-pill bg-primary px-3 py-2"><i class="fas fa-spinner fa-spin me-1"></i> {{ $status }}</span>
            @elseif(strtolower($status) === 'diajukan-ke-koordinator')
                <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fas fa-user-tie me-1"></i> Ke Koordinator</span>
            @elseif(strtolower($status) === 'diajukan-ke-dekan' || strtolower($status) === 'menunggu-ttd-dekan')
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
			@else
				<span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status ?? '-' }}</span>
			@endif
		</div>
	</div>
</div>

<div class="row g-4">
	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-user-circle fa-lg text-secondary me-2"></i>
				<strong>Detail Pengaju</strong>
			</div>
			<div class="card-body">
				<h5 class="mb-1">{{ optional($surat->pemberiTugas)->Name_User ?? '-' }}</h5>
				<div class="text-muted mb-3">{{ optional(optional($surat->pemberiTugas)->role)->Name_Role ?? '-' }}</div>

				<p class="mb-1"><small class="text-muted">Tanggal Pengajuan</small><br>
                    @if($surat->Tanggal_Diberikan)
                        {{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan)->format('d M Y H:i') }}
                    @else
                        -
                    @endif
                </p>

				@if(!empty($detailPengaju))
					<hr />
					<p class="mb-1"><small class="text-muted">NIM</small><br>{{ $detailPengaju->NIM ?? '-' }}</p>
					<p class="mb-0"><small class="text-muted">Alamat</small><br>{{ $detailPengaju->Alamat_Mahasiswa ?? '-' }}</p>
				@endif
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-info-circle fa-lg text-secondary me-2"></i>
				<strong>Informasi Surat</strong>
			</div>
			<div class="card-body">
				<p class="mb-2"><small class="text-muted">Jenis Surat</small><br>Surat Keterangan Aktif</p>
				<p class="mb-0"><small class="text-muted">Keperluan</small><br>{{ $surat->Deskripsi ?? '-' }}</p>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-folder-open fa-lg text-secondary me-2"></i>
				<strong>Dokumen & Proses</strong>
			</div>
			<div class="card-body">
				<p class="mb-2"><small class="text-muted">Ditujukan ke</small><br>{{ optional($surat->penerimaTugas)->Name_User ?? '-' }} <br><span class="text-muted">({{ optional(optional($surat->penerimaTugas)->role)->Name_Role ?? '-' }})</span></p>

				<div class="mb-3">
					<small class="text-muted d-block mb-1">Dokumen Pendukung (KRS)</small>
                    @if(!empty($surat->KRS))
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin_fakultas.surat.preview', ['id' => $surat->id_no]) }}" target="_blank" class="btn btn-outline-info" title="Preview KRS"><i class="fa fa-eye me-1"></i> Preview</a>
						    <a href="{{ route('admin_fakultas.surat.download', ['id' => $surat->id_no]) }}" class="btn btn-outline-primary" title="Unduh KRS"><i class="fa fa-download me-1"></i> Unduh</a>
                        </div>
                    @else
						<span class="text-muted">-</span>
					@endif
				</div>

                {{-- Form Proses Surat (Beri Nomor & Teruskan ke Dekan) --}}
				@if(auth()->check() && auth()->user()->Id_Role == 7 && (empty(trim($surat->Status)) || in_array(strtolower(trim($surat->Status)), ['baru', 'proses', 'diterima admin', 'diajukan-ke-koordinator', 'dikerjakan-admin'])))
					<hr />
                    <h6 class="fw-bold text-primary"><i class="fas fa-cog me-1"></i> Tindakan Admin</h6>
                    
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin_fakultas.surat.forward', $surat->id_no) }}" onsubmit="return confirm('Apakah nomor surat sudah benar? Surat akan diteruskan ke Dekan.');">
                                @csrf
                                <div class="mb-3">
                                    <label for="nomor_surat" class="form-label fw-bold">Nomor Surat</label>
                                    <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" placeholder="Masukkan Nomor Surat (Wajib)" value="{{ $surat->Nomor_Surat }}" required>
                                    <div class="form-text">Nomor surat wajib diisi sebelum diteruskan ke Dekan.</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> Simpan & Teruskan ke Dekan
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fas fa-times-circle me-1"></i> Tolak Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
				@endif
			</div>
		</div>
	</div>
</div>

<!-- Modal Tolak Surat -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin_fakultas.surat.reject', $surat->id_no) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan Surat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required placeholder="Jelaskan mengapa surat ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Urgent --}}
<div class="modal fade" id="urgentModal" tabindex="-1" aria-labelledby="urgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin_fakultas.surat.toggle_urgent', $surat->id_no) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="urgentModalLabel">
                        {{ $surat->is_urgent ? 'Batalkan Prioritas' : 'Set Prioritas Surat' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($surat->is_urgent)
                        <p>Apakah Anda yakin ingin membatalkan status prioritas (urgent) untuk surat ini?</p>
                        <input type="hidden" name="urgent_reason" value="">
                    @else
                        <div class="mb-3">
                            <label for="urgent_reason" class="form-label">Alasan Prioritas</label>
                            <textarea class="form-control" id="urgent_reason" name="urgent_reason" rows="3" placeholder="Contoh: Dibutuhkan segera untuk beasiswa..." required></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn {{ $surat->is_urgent ? 'btn-danger' : 'btn-primary' }}">
                        {{ $surat->is_urgent ? 'Ya, Batalkan' : 'Simpan Prioritas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
