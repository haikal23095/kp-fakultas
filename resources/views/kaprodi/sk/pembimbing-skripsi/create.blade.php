@extends('layouts.kaprodi')

@section('title', 'Ajukan SK Pembimbing Skripsi')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">SK Pembimbing Skripsi</li>
        </ol>
    </nav>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex align-items-center">
                    <i class="fas fa-book-reader fa-lg me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">Ajukan SK Pembimbing Skripsi</h5>
                        <small>Isi data mahasiswa dan dosen pembimbing skripsi</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kaprodi.sk.pembimbing-skripsi.store') }}" method="POST" id="formPembimbingSkripsi">
                    @csrf

                    <!-- Identitas Pengajuan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>Identitas Pengajuan
                        </h6>
                        <div class="row g-3">
                            <!-- Program Studi -->
                            <div class="col-md-4">
                                <label for="prodi_id" class="form-label fw-semibold">
                                    Program Studi <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('prodi_id') is-invalid @enderror" 
                                        id="prodi_id" 
                                        name="prodi_id" 
                                        required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->Id_Prodi }}" {{ (old('prodi_id', $prodi->Id_Prodi ?? '') == $p->Id_Prodi) ? 'selected' : '' }}>
                                            {{ $p->Nama_Prodi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4">
                                <label for="semester" class="form-label fw-semibold">
                                    Semester <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('semester') is-invalid @enderror" 
                                        id="semester" 
                                        name="semester" 
                                        required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tahun Akademik -->
                            <div class="col-md-4">
                                <label for="tahun_akademik" class="form-label fw-semibold">
                                    Tahun Akademik <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('tahun_akademik') is-invalid @enderror" 
                                       id="tahun_akademik" 
                                       name="tahun_akademik" 
                                       placeholder="Contoh: 2023/2024"
                                       value="{{ old('tahun_akademik') }}"
                                       required>
                                @error('tahun_akademik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YY/YY (contoh: 23/24)</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Daftar Penetapan Pembimbing -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-warning mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Penetapan Pembimbing
                            </h6>
                            <button type="button" class="btn btn-warning btn-sm" id="tambahPembimbing">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Mahasiswa
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tabelPembimbing">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="15%">Nama Mahasiswa</th>
                                        <th width="10%">NIM</th>
                                        <th width="25%">Judul Skripsi</th>
                                        <th width="17%">Pembimbing 1</th>
                                        <th width="17%">Pembimbing 2</th>
                                        <th width="8%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyPembimbing">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                <strong>Petunjuk:</strong> Klik tombol "Tambah Mahasiswa" untuk menambahkan data mahasiswa beserta judul skripsi dan dosen pembimbing. Pastikan semua data terisi dengan lengkap.
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-warning" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan SK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        vertical-align: middle;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table select,
    .table input {
        font-size: 0.9rem;
    }
    
    .btn-remove {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery (diperlukan untuk fungsi tambah mahasiswa) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    let rowIndex = 0;
    
    // Data mahasiswa dari backend
    const mahasiswas = @json($mahasiswas);
    
    // Data dosen dari backend
    const dosens = @json($dosens);
    
    // Function to generate mahasiswa options
    function generateMahasiswaOptions(selectedId = '') {
        let options = '<option value="">-- Pilih Mahasiswa --</option>';
        mahasiswas.forEach(mhs => {
            const selected = mhs.Id_Mahasiswa == selectedId ? 'selected' : '';
            options += `<option value="${mhs.Id_Mahasiswa}" data-nim="${mhs.NIM}" ${selected}>${mhs.Nama_Mahasiswa} (${mhs.NIM})</option>`;
        });
        return options;
    }
    
    // Function to generate dosen options
    function generateDosenOptions(selectedId = '') {
        let options = '<option value="">-- Pilih Dosen --</option>';
        dosens.forEach(dosen => {
            const selected = dosen.Id_Dosen == selectedId ? 'selected' : '';
            options += `<option value="${dosen.Id_Dosen}" ${selected}>${dosen.Nama_Dosen} (${dosen.NIP})</option>`;
        });
        return options;
    }
    
    // Wait for document ready
    $(document).ready(function() {
        console.log('Document ready, setting up event handlers...');
        
        // Add new row
        $('#tambahPembimbing').on('click', function() {
            console.log('Tambah Pembimbing clicked');
            rowIndex++;
            
            const newRow = `
                <tr data-index="${rowIndex}">
                    <td class="text-center">${rowIndex}</td>
                    <td>
                        <select class="form-select form-select-sm mahasiswa-select" 
                                name="pembimbing[${rowIndex}][mahasiswa_id]" 
                                required>
                            ${generateMahasiswaOptions()}
                        </select>
                    </td>
                    <td>
                        <input type="text" 
                               class="form-control form-control-sm nim-display" 
                               readonly 
                               placeholder="NIM otomatis">
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm" 
                                  name="pembimbing[${rowIndex}][judul_skripsi]" 
                                  rows="2" 
                                  placeholder="Masukkan judul skripsi"
                                  required></textarea>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" 
                                name="pembimbing[${rowIndex}][pembimbing_1]" 
                                required>
                            ${generateDosenOptions()}
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" 
                                name="pembimbing[${rowIndex}][pembimbing_2]" 
                                required>
                            ${generateDosenOptions()}
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#bodyPembimbing').append(newRow);
            updateRowNumbers();
            console.log('Row added, current count:', $('#bodyPembimbing tr').length);
        });
        
        // Remove row
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });
        
        // Update row numbers
        function updateRowNumbers() {
            $('#bodyPembimbing tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }
        
        // Auto-fill NIM when mahasiswa is selected
        $(document).on('change', '.mahasiswa-select', function() {
            const selectedOption = $(this).find('option:selected');
            const nim = selectedOption.data('nim');
            $(this).closest('tr').find('.nim-display').val(nim || '');
        });
        
        // Form validation
        $('#formPembimbingSkripsi').on('submit', function(e) {
            const rowCount = $('#bodyPembimbing tr').length;
            
            if (rowCount === 0) {
                e.preventDefault();
                alert('Harap tambahkan minimal 1 mahasiswa dengan data pembimbing!');
                return false;
            }
            
            // Validate pembimbing tidak sama
            let hasError = false;
            $('#bodyPembimbing tr').each(function() {
                const pembimbing1 = $(this).find('select[name*="[pembimbing_1]"]').val();
                const pembimbing2 = $(this).find('select[name*="[pembimbing_2]"]').val();
                
                if (pembimbing1 && pembimbing2 && pembimbing1 === pembimbing2) {
                    alert('Pembimbing 1 dan Pembimbing 2 tidak boleh sama!');
                    hasError = true;
                    return false;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                return false;
            }
            
            // Disable submit button to prevent double submission
            $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sedang diproses...');
        });
        
        // Add first row on page load
        console.log('Triggering first row addition...');
        $('#tambahPembimbing').trigger('click');
    });
</script>
@endpush

@endsection
