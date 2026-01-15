@extends('layouts.kaprodi')

@section('title', 'Ajukan SK Dosen Wali')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Ajukan SK Dosen Wali</h1>
        <p class="mb-0 text-muted">Formulir pengajuan surat keputusan dosen wali</p>
    </div>
    <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<form action="{{ route('kaprodi.sk.dosen-wali.store') }}" method="POST" id="formSKDosenWali">
    @csrf
    
    <!-- Card Identitas Pengajuan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary bg-opacity-10 border-0">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Identitas Pengajuan
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Program Studi -->
                <div class="col-md-12">
                    <label for="id_prodi" class="form-label fw-semibold">
                        Program Studi <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('id_prodi') is-invalid @enderror" 
                            id="id_prodi" 
                            name="id_prodi" 
                            required>
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->Id_Prodi }}" 
                                    {{ old('id_prodi', $prodi ? $prodi->Id_Prodi : '') == $p->Id_Prodi ? 'selected' : '' }}>
                                {{ $p->Nama_Prodi }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_prodi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Semester -->
                <div class="col-md-6">
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
                <div class="col-md-6">
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
                    <small class="form-text text-muted">Format: YYYY/YYYY (contoh: 2023/2024)</small>
                    @error('tahun_akademik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card Daftar Penetapan Dosen Wali -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success bg-opacity-10 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-success">
                <i class="fas fa-users me-2"></i>Daftar Penetapan Dosen Wali
            </h5>
            <button type="button" class="btn btn-success btn-sm" id="btnTambahDosen">
                <i class="fas fa-plus me-1"></i>Tambah Dosen
            </button>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablePenetapan">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="50%">Nama Dosen</th>
                            <th width="30%">Jumlah Anak Wali</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPenetapan">
                        <!-- Rows will be added dynamically -->
                        <tr class="text-center" id="emptyRow">
                            <td colspan="4" class="text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada dosen wali yang ditambahkan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="card border-0 shadow-sm bg-light mb-4">
        <div class="card-body">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-info"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-2">Petunjuk Pengisian</h6>
                    <ul class="mb-0 small text-muted">
                        <li>Pastikan data identitas pengajuan sudah benar</li>
                        <li>Tambahkan dosen wali dengan klik tombol "Tambah Dosen"</li>
                        <li>Isi jumlah anak wali untuk setiap dosen</li>
                        <li>Minimal harus ada 1 dosen wali yang ditambahkan</li>
                        <li>SK akan diproses setelah diajukan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary" id="btnSubmit">
            <i class="fas fa-paper-plane me-2"></i>Ajukan SK Dosen Wali
        </button>
    </div>
</form>

<!-- Modal Tambah Dosen -->
<div class="modal fade" id="modalTambahDosen" tabindex="-1" aria-labelledby="modalTambahDosenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTambahDosenLabel">
                    <i class="fas fa-user-plus me-2"></i>Tambah Dosen Wali
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modal_dosen_input" class="form-label fw-semibold">Pilih Dosen</label>
                    <div class="autocomplete-wrapper">
                        <input type="text" 
                               class="form-control" 
                               id="modal_dosen_input" 
                               placeholder="Ketik nama atau NIP dosen..."
                               autocomplete="off">
                        <input type="hidden" id="modal_dosen" value="">
                        <div class="autocomplete-suggestions" id="modal_dosen_suggestions"></div>
                    </div>
                    <small class="form-text text-muted">Ketik minimal 2 karakter untuk mencari dosen</small>
                </div>
                <div class="mb-3">
                    <label for="modal_jumlah" class="form-label fw-semibold">Jumlah Anak Wali</label>
                    <input type="number" class="form-control" id="modal_jumlah" min="0" value="0">
                    <small class="form-text text-muted">Masukkan jumlah mahasiswa yang akan diampu</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanDosen">
                    <i class="fas fa-check me-1"></i>Tambahkan
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-action {
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
    
    .autocomplete-item .dosen-name {
        font-weight: 500;
    }
    
    .autocomplete-item .dosen-nip {
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
<script>
    let dosenCounter = 0;
    let dosenList = [];
    
    // Data dosen dari server
    const dosenData = @json($dosens);
    
    // Selected dosen untuk modal
    let selectedDosenId = '';
    let selectedDosenNama = '';

    document.addEventListener('DOMContentLoaded', function() {
        // Modal tambah dosen
        const modal = new bootstrap.Modal(document.getElementById('modalTambahDosen'));
        const btnTambahDosen = document.getElementById('btnTambahDosen');
        const btnSimpanDosen = document.getElementById('btnSimpanDosen');
        const modalDosenInput = document.getElementById('modal_dosen_input');
        const modalDosenHidden = document.getElementById('modal_dosen');
        const modalDosenSuggestions = document.getElementById('modal_dosen_suggestions');
        const modalJumlah = document.getElementById('modal_jumlah');
        
        // Autocomplete untuk dosen
        initDosenAutocomplete(modalDosenInput, modalDosenHidden, modalDosenSuggestions);
        
        // Open modal
        btnTambahDosen.addEventListener('click', function() {
            modalDosenInput.value = '';
            modalDosenHidden.value = '';
            selectedDosenId = '';
            selectedDosenNama = '';
            modalJumlah.value = 0;
            modalDosenSuggestions.classList.remove('show');
            modal.show();
        });
        
        // Add dosen to table
        btnSimpanDosen.addEventListener('click', function() {
            const idDosen = modalDosenHidden.value;
            const namaDosen = selectedDosenNama;
            const jumlahAnakWali = modalJumlah.value;
            
            if (!idDosen) {
                alert('Silakan pilih dosen terlebih dahulu!');
                return;
            }
            
            if (jumlahAnakWali < 0) {
                alert('Jumlah anak wali tidak boleh negatif!');
                return;
            }
            
            // Check if dosen already exists
            if (dosenList.includes(idDosen)) {
                alert('Dosen ini sudah ditambahkan!');
                return;
            }
            
            // Add to list
            dosenList.push(idDosen);
            dosenCounter++;
            
            // Remove empty row
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) {
                emptyRow.remove();
            }
            
            // Add new row
            const tbody = document.getElementById('bodyPenetapan');
            const newRow = document.createElement('tr');
            newRow.id = `row-${dosenCounter}`;
            newRow.innerHTML = `
                <td class="text-center">${dosenCounter}</td>
                <td>
                    ${namaDosen}
                    <input type="hidden" name="dosen[${dosenCounter}][id_dosen]" value="${idDosen}">
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm" 
                           name="dosen[${dosenCounter}][jumlah_anak_wali]" 
                           value="${jumlahAnakWali}"
                           min="0"
                           required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-action" onclick="hapusDosen(${dosenCounter}, '${idDosen}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            // Close modal
            modal.hide();
            
            // Update row numbers
            updateRowNumbers();
        });
        
        // Form validation
        document.getElementById('formSKDosenWali').addEventListener('submit', function(e) {
            if (dosenList.length === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 dosen wali yang ditambahkan!');
                return false;
            }
        });
    });
    
    // Initialize autocomplete for dosen
    function initDosenAutocomplete(inputElement, hiddenElement, suggestionsElement) {
        let activeIndex = -1;
        
        inputElement.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            hiddenElement.value = '';
            selectedDosenId = '';
            selectedDosenNama = '';
            
            if (searchTerm.length < 2) {
                suggestionsElement.classList.remove('show');
                suggestionsElement.innerHTML = '';
                return;
            }
            
            // Filter dosen
            const filtered = dosenData.filter(dosen => {
                const nama = dosen.Nama_Dosen.toLowerCase();
                const nip = String(dosen.NIP).toLowerCase();
                return nama.includes(searchTerm) || nip.includes(searchTerm);
            });
            
            // Build suggestions HTML
            if (filtered.length > 0) {
                suggestionsElement.innerHTML = filtered.map((dosen, index) => `
                    <div class="autocomplete-item" data-id="${dosen.Id_Dosen}" data-nama="${dosen.Nama_Dosen}" data-index="${index}">
                        <div class="dosen-name">${dosen.Nama_Dosen}</div>
                        <div class="dosen-nip">NIP: ${dosen.NIP}</div>
                    </div>
                `).join('');
            } else {
                suggestionsElement.innerHTML = '<div class="autocomplete-no-result">Dosen tidak ditemukan</div>';
            }
            
            suggestionsElement.classList.add('show');
            activeIndex = -1;
            
            // Add click handlers to items
            suggestionsElement.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;
                    
                    inputElement.value = nama;
                    hiddenElement.value = id;
                    selectedDosenId = id;
                    selectedDosenNama = nama;
                    suggestionsElement.classList.remove('show');
                });
            });
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsElement.querySelectorAll('.autocomplete-item');
            
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
                suggestionsElement.classList.remove('show');
            }
        });
        
        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!inputElement.contains(e.target) && !suggestionsElement.contains(e.target)) {
                suggestionsElement.classList.remove('show');
            }
        });
    }
    
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
    
    // Function to delete dosen
    function hapusDosen(counter, idDosen) {
        if (confirm('Apakah Anda yakin ingin menghapus dosen ini?')) {
            const row = document.getElementById(`row-${counter}`);
            row.remove();
            
            // Remove from list
            const index = dosenList.indexOf(idDosen);
            if (index > -1) {
                dosenList.splice(index, 1);
            }
            
            // Update row numbers
            updateRowNumbers();
            
            // Show empty row if no data
            const tbody = document.getElementById('bodyPenetapan');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr class="text-center" id="emptyRow">
                        <td colspan="4" class="text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada dosen wali yang ditambahkan
                        </td>
                    </tr>
                `;
            }
        }
    }
    
    // Function to update row numbers
    function updateRowNumbers() {
        const tbody = document.getElementById('bodyPenetapan');
        const rows = tbody.querySelectorAll('tr:not(#emptyRow)');
        rows.forEach((row, index) => {
            const numberCell = row.querySelector('td:first-child');
            if (numberCell) {
                numberCell.textContent = index + 1;
            }
        });
    }
</script>
@endpush

@endsection
