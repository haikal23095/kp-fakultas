@extends('layouts.kaprodi')

@section('title', 'Ajukan SK Penguji Skripsi')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">SK Penguji Skripsi</li>
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
            <div class="card-header bg-danger text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-check fa-lg me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">Ajukan SK Penguji Skripsi</h5>
                        <small>Isi data mahasiswa dan dosen penguji ujian skripsi</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kaprodi.sk.penguji-skripsi.store') }}" method="POST" id="formPengujiSkripsi">
                    @csrf

                    <!-- Identitas Pengajuan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-danger mb-3">
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
                                <small class="text-muted">Format: YYYY/YYYY (contoh: 2023/2024)</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Daftar Penetapan Penguji -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-danger mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Penetapan Penguji
                            </h6>
                            <button type="button" class="btn btn-danger btn-sm" id="tambahPenguji">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Mahasiswa
                            </button>
                        </div>

                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-bordered table-hover" id="tabelPenguji">
                                <thead class="table-light">
                                    <tr>
                                        <th width="3%" class="text-center">No</th>
                                        <th width="15%">Nama Mahasiswa</th>
                                        <th width="10%">NPM</th>
                                        <th width="20%">Judul Skripsi</th>
                                        <th width="14%">Penguji 1</th>
                                        <th width="14%">Penguji 2</th>
                                        <th width="14%">Penguji 3</th>
                                        <th width="5%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyPenguji">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-5" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                <strong>Petunjuk:</strong> Klik tombol "Tambah Mahasiswa" untuk menambahkan data mahasiswa beserta judul skripsi dan dosen penguji. Pastikan semua data terisi dengan lengkap.
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-danger" id="btnSubmit">
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
        font-size: 0.85rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table select,
    .table input,
    .table textarea {
        font-size: 0.85rem;
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
        font-size: 0.875rem;
    }
    
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #e9ecef;
    }
    
    .autocomplete-item .mhs-name {
        font-weight: 500;
    }
    
    .autocomplete-item .mhs-nim {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .autocomplete-no-result {
        padding: 8px 12px;
        color: #6c757d;
        font-style: italic;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    let rowIndex = 0;
    
    const mahasiswas = @json($mahasiswas);
    const dosens = @json($dosens);
    
    // Debug: Tampilkan data mahasiswa di console
    console.log('Data Mahasiswas:', mahasiswas);
    console.log('Jumlah Mahasiswa:', mahasiswas.length);
    
    function generateDosenOptions(selectedId = '') {
        let options = '<option value="">-- Pilih Dosen --</option>';
        dosens.forEach(dosen => {
            const selected = dosen.Id_Dosen == selectedId ? 'selected' : '';
            options += `<option value="${dosen.Id_Dosen}" ${selected}>${dosen.Nama_Dosen} (${dosen.NIP})</option>`;
        });
        return options;
    }
    
    // Initialize autocomplete untuk input mahasiswa
    function initMahasiswaAutocomplete(inputElement) {
        const wrapper = inputElement.closest('.autocomplete-wrapper');
        const suggestionsDiv = wrapper.querySelector('.autocomplete-suggestions');
        const hiddenInput = wrapper.querySelector('.mahasiswa-id-hidden');
        const nimInput = inputElement.closest('tr').querySelector('.nim-display');
        let activeIndex = -1;
        
        // Filter dan tampilkan suggestions
        function showSuggestions(searchTerm) {
            const filtered = mahasiswas.filter(mhs => 
                mhs.Nama_Mahasiswa.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (mhs.NIM && String(mhs.NIM).includes(searchTerm))
            );
            
            if (filtered.length === 0) {
                suggestionsDiv.innerHTML = '<div class="autocomplete-no-result">Tidak ada mahasiswa ditemukan</div>';
            } else {
                suggestionsDiv.innerHTML = filtered.map((mhs, index) => `
                    <div class="autocomplete-item" data-id="${mhs.Id_Mahasiswa}" data-name="${mhs.Nama_Mahasiswa}" data-nim="${mhs.NIM}" data-index="${index}">
                        <div class="mhs-name">${mhs.Nama_Mahasiswa}</div>
                        <div class="mhs-nim">${mhs.NIM || '-'}</div>
                    </div>
                `).join('');
                
                // Add click handlers
                suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectMahasiswa(this.dataset.id, this.dataset.name, this.dataset.nim);
                    });
                });
            }
            
            suggestionsDiv.classList.add('show');
            activeIndex = -1;
        }
        
        // Select mahasiswa
        function selectMahasiswa(id, name, nim) {
            inputElement.value = name;
            hiddenInput.value = id;
            nimInput.value = nim || '';
            suggestionsDiv.classList.remove('show');
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        }
        
        // Input event
        inputElement.addEventListener('input', function() {
            const value = this.value.trim();
            hiddenInput.value = ''; // Reset hidden input saat user mengetik
            nimInput.value = ''; // Reset NIM
            inputElement.classList.remove('is-valid');
            
            if (value.length >= 1) {
                showSuggestions(value);
            } else {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Focus event - show all if empty
        inputElement.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                showSuggestions(this.value.trim());
            }
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    const item = items[activeIndex];
                    selectMahasiswa(item.dataset.id, item.dataset.name, item.dataset.nim);
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        function updateActiveItem(items) {
            items.forEach((item, index) => {
                item.classList.toggle('active', index === activeIndex);
            });
            if (items[activeIndex]) {
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }
        
        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Validation on blur
        inputElement.addEventListener('blur', function() {
            setTimeout(() => {
                if (!hiddenInput.value && this.value.trim()) {
                    // User typed something but didn't select
                    this.classList.add('is-invalid');
                }
            }, 200);
        });
    }
    
    // Initialize autocomplete untuk input dosen (penguji)
    function initDosenAutocomplete(inputElement) {
        const wrapper = inputElement.closest('.autocomplete-wrapper');
        const suggestionsDiv = wrapper.querySelector('.autocomplete-suggestions');
        const hiddenInput = wrapper.querySelector('.dosen-id-hidden');
        let activeIndex = -1;
        
        // Filter dan tampilkan suggestions
        function showSuggestions(searchTerm) {
            const filtered = dosens.filter(dosen => 
                dosen.Nama_Dosen.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (dosen.NIP && String(dosen.NIP).includes(searchTerm))
            );
            
            if (filtered.length === 0) {
                suggestionsDiv.innerHTML = '<div class="autocomplete-no-result">Tidak ada dosen ditemukan</div>';
            } else {
                suggestionsDiv.innerHTML = filtered.map((dosen, index) => `
                    <div class="autocomplete-item" data-id="${dosen.Id_Dosen}" data-name="${dosen.Nama_Dosen}" data-index="${index}">
                        <div class="mhs-name">${dosen.Nama_Dosen}</div>
                        <div class="mhs-nim">${dosen.NIP || '-'}</div>
                    </div>
                `).join('');
                
                // Add click handlers
                suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectDosen(this.dataset.id, this.dataset.name);
                    });
                });
            }
            
            suggestionsDiv.classList.add('show');
            activeIndex = -1;
        }
        
        // Select dosen
        function selectDosen(id, name) {
            inputElement.value = name;
            hiddenInput.value = id;
            suggestionsDiv.classList.remove('show');
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
        }
        
        // Input event
        inputElement.addEventListener('input', function() {
            const value = this.value.trim();
            hiddenInput.value = ''; // Reset hidden input saat user mengetik
            inputElement.classList.remove('is-valid');
            
            if (value.length >= 1) {
                showSuggestions(value);
            } else {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Focus event - show all if empty
        inputElement.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                showSuggestions(this.value.trim());
            }
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    const item = items[activeIndex];
                    selectDosen(item.dataset.id, item.dataset.name);
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        function updateActiveItem(items) {
            items.forEach((item, index) => {
                item.classList.toggle('active', index === activeIndex);
            });
            if (items[activeIndex]) {
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }
        
        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Validation on blur
        inputElement.addEventListener('blur', function() {
            setTimeout(() => {
                if (!hiddenInput.value && this.value.trim()) {
                    // User typed something but didn't select
                    this.classList.add('is-invalid');
                }
            }, 200);
        });
    }
    
    $(document).ready(function() {
        // Add new row
        $('#tambahPenguji').on('click', function() {
            rowIndex++;
            
            const newRow = `
                <tr data-index="${rowIndex}">
                    <td class="text-center">${rowIndex}</td>
                    <td>
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm mahasiswa-autocomplete" 
                                   data-row="${rowIndex}"
                                   placeholder="Ketik nama mahasiswa..." 
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="penguji[${rowIndex}][mahasiswa_id]" class="mahasiswa-id-hidden" required>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </td>
                    <td>
                        <input type="text" 
                               class="form-control form-control-sm nim-display" 
                               readonly 
                               placeholder="NIM">
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm" 
                                  name="penguji[${rowIndex}][judul_skripsi]" 
                                  rows="2" 
                                  placeholder="Judul skripsi"
                                  required></textarea>
                    </td>
                    <td>
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm dosen-autocomplete" 
                                   data-row="${rowIndex}"
                                   data-penguji="1"
                                   placeholder="Ketik nama dosen..." 
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="penguji[${rowIndex}][penguji_1]" class="dosen-id-hidden" required>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </td>
                    <td>
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm dosen-autocomplete" 
                                   data-row="${rowIndex}"
                                   data-penguji="2"
                                   placeholder="Ketik nama dosen..." 
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="penguji[${rowIndex}][penguji_2]" class="dosen-id-hidden" required>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </td>
                    <td>
                        <div class="autocomplete-wrapper">
                            <input type="text" 
                                   class="form-control form-control-sm dosen-autocomplete" 
                                   data-row="${rowIndex}"
                                   data-penguji="3"
                                   placeholder="Ketik nama dosen..." 
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="penguji[${rowIndex}][penguji_3]" class="dosen-id-hidden" required>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#bodyPenguji').append(newRow);
            
            // Initialize autocomplete untuk mahasiswa baru
            const newRowElement = $('#bodyPenguji tr').last()[0];
            const mahasiswaInput = newRowElement.querySelector('.mahasiswa-autocomplete');
            initMahasiswaAutocomplete(mahasiswaInput);
            
            // Initialize autocomplete untuk semua dosen (penguji 1, 2, 3)
            const dosenInputs = newRowElement.querySelectorAll('.dosen-autocomplete');
            dosenInputs.forEach(input => {
                initDosenAutocomplete(input);
            });
            
            updateRowNumbers();
        });
        
        // Remove row
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });
        
        // Update row numbers
        function updateRowNumbers() {
            $('#bodyPenguji tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }
        
        // Form validation
        $('#formPengujiSkripsi').on('submit', function(e) {
            const rowCount = $('#bodyPenguji tr').length;
            
            if (rowCount === 0) {
                e.preventDefault();
                alert('Harap tambahkan minimal 1 mahasiswa dengan data penguji!');
                return false;
            }
            
            let hasError = false;
            $('#bodyPenguji tr').each(function() {
                const p1 = $(this).find('input[name*="[penguji_1]"]').val();
                const p2 = $(this).find('input[name*="[penguji_2]"]').val();
                const p3 = $(this).find('input[name*="[penguji_3]"]').val();
                
                if (p1 && p2 && p1 === p2) {
                    alert('Penguji 1 dan Penguji 2 tidak boleh sama!');
                    hasError = true;
                    return false;
                }
                if (p1 && p3 && p1 === p3) {
                    alert('Penguji 1 dan Penguji 3 tidak boleh sama!');
                    hasError = true;
                    return false;
                }
                if (p2 && p3 && p2 === p3) {
                    alert('Penguji 2 dan Penguji 3 tidak boleh sama!');
                    hasError = true;
                    return false;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                return false;
            }
            
            $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sedang diproses...');
        });
        
        // Add first row on page load
        $('#tambahPenguji').trigger('click');
    });
</script>
@endpush

@endsection
