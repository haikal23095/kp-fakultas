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
    
    /* Autocomplete Styles */
    .autocomplete-wrapper {
        position: relative;
    }
    
    .autocomplete-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        z-index: 9999;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .autocomplete-suggestions.show {
        display: block;
    }
    
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
    }
    
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #e9ecef;
    }
    
    .autocomplete-item .item-name {
        font-weight: 500;
    }
    
    .autocomplete-item .item-info {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    .autocomplete-no-result {
        padding: 8px 12px;
        color: #6c757d;
        font-style: italic;
        font-size: 0.8rem;
    }
    
    .table-responsive {
        overflow: visible;
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
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm mahasiswa-autocomplete" 
                                   placeholder="Ketik nama/NIM..."
                                   autocomplete="off">
                            <input type="hidden" 
                                   name="pembimbing[${rowIndex}][mahasiswa_id]" 
                                   class="mahasiswa-id-hidden">
                            <div class="autocomplete-suggestions mahasiswa-suggestions"></div>
                        </div>
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
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm dosen-autocomplete" 
                                   placeholder="Ketik nama/NIP..."
                                   autocomplete="off"
                                   data-target="pembimbing_1">
                            <input type="hidden" 
                                   name="pembimbing[${rowIndex}][pembimbing_1]" 
                                   class="dosen-id-hidden">
                            <div class="autocomplete-suggestions dosen-suggestions"></div>
                        </div>
                    </td>
                    <td>
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm dosen-autocomplete" 
                                   placeholder="Ketik nama/NIP..."
                                   autocomplete="off"
                                   data-target="pembimbing_2">
                            <input type="hidden" 
                                   name="pembimbing[${rowIndex}][pembimbing_2]" 
                                   class="dosen-id-hidden">
                            <div class="autocomplete-suggestions dosen-suggestions"></div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#bodyPembimbing').append(newRow);
            
            // Initialize autocomplete for new row
            const newRowElement = $(`#bodyPembimbing tr[data-index="${rowIndex}"]`);
            initMahasiswaAutocomplete(newRowElement.find('.mahasiswa-autocomplete')[0]);
            newRowElement.find('.dosen-autocomplete').each(function() {
                initDosenAutocomplete(this);
            });
            
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
        
        // Form validation
        $('#formPembimbingSkripsi').on('submit', function(e) {
            const rowCount = $('#bodyPembimbing tr').length;
            
            if (rowCount === 0) {
                e.preventDefault();
                alert('Harap tambahkan minimal 1 mahasiswa dengan data pembimbing!');
                return false;
            }
            
            // Validate all required fields
            let hasError = false;
            $('#bodyPembimbing tr').each(function() {
                const mahasiswaId = $(this).find('.mahasiswa-id-hidden').val();
                const pembimbing1 = $(this).find('input[name*="[pembimbing_1]"]').val();
                const pembimbing2 = $(this).find('input[name*="[pembimbing_2]"]').val();
                
                if (!mahasiswaId) {
                    alert('Harap pilih mahasiswa dari daftar rekomendasi!');
                    hasError = true;
                    return false;
                }
                
                if (!pembimbing1 || !pembimbing2) {
                    alert('Harap pilih pembimbing 1 dan pembimbing 2 dari daftar rekomendasi!');
                    hasError = true;
                    return false;
                }
                
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
    
    // Initialize autocomplete for mahasiswa
    function initMahasiswaAutocomplete(inputElement) {
        const wrapper = inputElement.closest('.autocomplete-wrapper');
        const hiddenInput = wrapper.querySelector('.mahasiswa-id-hidden');
        const suggestionsDiv = wrapper.querySelector('.mahasiswa-suggestions');
        const row = inputElement.closest('tr');
        let activeIndex = -1;
        
        inputElement.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            hiddenInput.value = '';
            row.querySelector('.nim-display').value = '';
            
            if (searchTerm.length < 2) {
                suggestionsDiv.classList.remove('show');
                suggestionsDiv.innerHTML = '';
                return;
            }
            
            // Filter mahasiswa
            const filtered = mahasiswas.filter(mhs => {
                const nama = mhs.Nama_Mahasiswa.toLowerCase();
                const nim = String(mhs.NIM).toLowerCase();
                return nama.includes(searchTerm) || nim.includes(searchTerm);
            });
            
            // Build suggestions HTML
            if (filtered.length > 0) {
                suggestionsDiv.innerHTML = filtered.slice(0, 10).map((mhs, index) => `
                    <div class="autocomplete-item" data-id="${mhs.Id_Mahasiswa}" data-nama="${mhs.Nama_Mahasiswa}" data-nim="${mhs.NIM}" data-index="${index}">
                        <div class="item-name">${mhs.Nama_Mahasiswa}</div>
                        <div class="item-info">NIM: ${mhs.NIM}</div>
                    </div>
                `).join('');
            } else {
                suggestionsDiv.innerHTML = '<div class="autocomplete-no-result">Mahasiswa tidak ditemukan</div>';
            }
            
            suggestionsDiv.classList.add('show');
            activeIndex = -1;
            
            // Add click handlers
            suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    inputElement.value = this.dataset.nama;
                    hiddenInput.value = this.dataset.id;
                    row.querySelector('.nim-display').value = this.dataset.nim;
                    suggestionsDiv.classList.remove('show');
                });
            });
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    items[activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!inputElement.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
    }
    
    // Initialize autocomplete for dosen
    function initDosenAutocomplete(inputElement) {
        const wrapper = inputElement.closest('.autocomplete-wrapper');
        const hiddenInput = wrapper.querySelector('.dosen-id-hidden');
        const suggestionsDiv = wrapper.querySelector('.dosen-suggestions');
        let activeIndex = -1;
        
        inputElement.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            hiddenInput.value = '';
            
            if (searchTerm.length < 2) {
                suggestionsDiv.classList.remove('show');
                suggestionsDiv.innerHTML = '';
                return;
            }
            
            // Filter dosen
            const filtered = dosens.filter(dosen => {
                const nama = dosen.Nama_Dosen.toLowerCase();
                const nip = String(dosen.NIP).toLowerCase();
                return nama.includes(searchTerm) || nip.includes(searchTerm);
            });
            
            // Build suggestions HTML
            if (filtered.length > 0) {
                suggestionsDiv.innerHTML = filtered.slice(0, 10).map((dosen, index) => `
                    <div class="autocomplete-item" data-id="${dosen.Id_Dosen}" data-nama="${dosen.Nama_Dosen}" data-index="${index}">
                        <div class="item-name">${dosen.Nama_Dosen}</div>
                        <div class="item-info">NIP: ${dosen.NIP}</div>
                    </div>
                `).join('');
            } else {
                suggestionsDiv.innerHTML = '<div class="autocomplete-no-result">Dosen tidak ditemukan</div>';
            }
            
            suggestionsDiv.classList.add('show');
            activeIndex = -1;
            
            // Add click handlers
            suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    inputElement.value = this.dataset.nama;
                    hiddenInput.value = this.dataset.id;
                    suggestionsDiv.classList.remove('show');
                });
            });
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    items[activeIndex].click();
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!inputElement.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
    }
    
    // Helper function to update active item
    function updateActiveItem(items, activeIndex) {
        items.forEach((item, index) => {
            if (index === activeIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }
</script>
@endpush

@endsection
