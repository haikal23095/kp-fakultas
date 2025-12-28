@extends('layouts.admin_fakultas')

@section('title', 'Input Pengajuan Legalisir')

@push('styles')
{{-- Library Select2 untuk fitur Pencarian (Ketik Nama/NIM) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .card-input { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .form-label { font-weight: 600; color: #4e73df; }
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none; padding: 12px 35px; border-radius: 50px; font-weight: 600; color: white; transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); color: white; }
    .info-offline { background-color: #f8f9fc; border-left: 4px solid #f6c23e; padding: 15px; border-radius: 8px; margin-bottom: 25px; }
    
    /* Styling khusus agar box pencarian Select2 terlihat modern dan pas di Bootstrap 5 */
    .select2-container--bootstrap-5 .select2-selection { 
        border-radius: 10px; 
        padding-left: 0.5rem; 
        min-height: 48px; 
        display: flex;
        align-items: center;
        border: 1px solid #d1d3e2;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Input Legalisir Baru</h1>
            <p class="text-muted small">Pendaftaran berkas fisik mahasiswa secara luring (offline)</p>
        </div>
        <a href="{{ route('admin_fakultas.surat_legalisir.index') }}" class="btn btn-sm btn-secondary rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Alert Error jika validasi gagal --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-input">
                <div class="card-body p-4">
                    <div class="info-offline">
                        <h6 class="fw-bold text-warning mb-1"><i class="fas fa-search me-2"></i>Pencarian Mahasiswa</h6>
                        <p class="small text-muted mb-0">Ketik <strong>NIM</strong> atau <strong>Nama Lengkap</strong> pada kolom di bawah untuk menarik data mahasiswa.</p>
                    </div>

                    <form action="{{ route('admin_fakultas.surat_legalisir.store') }}" method="POST">
                        @csrf
                        
                        {{-- Dropdown Searchable Mahasiswa --}}
                        <div class="mb-4">
                            <label class="form-label">Cari Mahasiswa / Alumni</label>
                            <select name="id_user_mahasiswa" class="form-select select2-search" required>
                                <option value=""></option>
                                @foreach($daftarMahasiswa as $mhs)
                                    <option value="{{ $mhs->user->Id_User }}" {{ old('id_user_mahasiswa') == $mhs->user->Id_User ? 'selected' : '' }}>
                                        {{ $mhs->NIM }} - {{ $mhs->Nama_Mahasiswa }} ({{ $mhs->prodi->Nama_Prodi ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilihan Jenis Dokumen --}}
                        <div class="mb-4">
                            <label class="form-label d-block">Jenis Dokumen</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="jenis_dokumen" id="ijazah" value="Ijazah" 
                                       autocomplete="off" {{ old('jenis_dokumen', 'Ijazah') == 'Ijazah' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary py-2" for="ijazah">
                                    <i class="fas fa-graduation-cap me-2"></i>Ijazah
                                </label>

                                <input type="radio" class="btn-check" name="jenis_dokumen" id="transkrip" value="Transkrip" 
                                       autocomplete="off" {{ old('jenis_dokumen') == 'Transkrip' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary py-2" for="transkrip">
                                    <i class="fas fa-file-alt me-2"></i>Transkrip
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Jumlah Salinan --}}
                            <div class="col-md-5 mb-4">
                                <label class="form-label">Jumlah Salinan</label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_salinan" class="form-control" min="1" max="50" 
                                           value="{{ old('jumlah_salinan', 1) }}" required>
                                    <span class="input-group-text text-primary fw-bold">Lembar</span>
                                </div>
                            </div>

                            {{-- Input Biaya (Wajib name="biaya") --}}
                            <div class="col-md-7 mb-4">
                                <label class="form-label">Biaya Legalisir</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white fw-bold">Rp</span>
                                    <input type="number" name="biaya" class="form-control" 
                                           placeholder="Input nominal (misal: 10000)" 
                                           value="{{ old('biaya') }}" min="0" required>
                                </div>
                                <small class="text-muted italic">Total biaya yang harus dibayar mahasiswa saat ini.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-end">
                            <button type="reset" class="btn btn-light rounded-pill px-4 me-2">Reset</button>
                            <button type="submit" class="btn btn-save shadow">
                                <i class="fas fa-save me-2"></i>Simpan & Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- JQuery dan Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk fitur ketik cari
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            placeholder: "Ketik NIM atau Nama Mahasiswa...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() { return "Data mahasiswa tidak ditemukan"; }
            }
        });
    });
</script>
@endpush