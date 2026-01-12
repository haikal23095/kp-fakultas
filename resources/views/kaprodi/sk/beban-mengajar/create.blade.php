@extends('layouts.kaprodi')

@section('title', 'Ajukan SK Beban Mengajar')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">SK Beban Mengajar</li>
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
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chalkboard-teacher fa-lg me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">Ajukan SK Beban Mengajar</h5>
                        <small>Isi data beban mengajar dosen untuk semester aktif</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kaprodi.sk.beban-mengajar.store') }}" method="POST" id="formBebanMengajar">
                    @csrf

                    <!-- Identitas Pengajuan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
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
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
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
                                       placeholder="Contoh: 2025/2026"
                                       value="{{ old('tahun_akademik') }}"
                                       required>
                                @error('tahun_akademik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YYYY/YYYY (contoh: 2025/2026)</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Daftar Beban Mengajar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Beban Mengajar
                            </h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-sm me-2" id="btnKelolaKelas" data-bs-toggle="modal" data-bs-target="#modalKelolaKelas">
                                    <i class="fas fa-cog me-2"></i>Kelola Kelas
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="tambahBeban">
                                    <i class="fas fa-plus-circle me-2"></i>Tambah Beban Mengajar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tabelBebanMengajar">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="25%">Nama Dosen</th>
                                        <th width="25%">Mata Kuliah</th>
                                        <th width="15%">Kelas</th>
                                        <th width="10%" class="text-center">SKS</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyBebanMengajar">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="4" class="text-end">Total SKS:</td>
                                        <td class="text-center" id="totalSKS">0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                <strong>Petunjuk:</strong> Klik tombol "Tambah Beban Mengajar" untuk menambahkan data dosen dan mata kuliah yang diampu. Anda dapat menambahkan beberapa beban mengajar sekaligus.
                            </small>
                        </div>
                    </div>

                    <!-- Catatan (Optional) -->
                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">
                            Catatan (Opsional)
                        </label>
                        <textarea class="form-control" 
                                  id="catatan" 
                                  name="catatan" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan atau keterangan tambahan jika diperlukan">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan SK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Kelas -->
<div class="modal fade" id="modalKelolaKelas" tabindex="-1" aria-labelledby="modalKelolaKelasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalKelolaKelasLabel">
                    <i class="fas fa-cog me-2"></i>Kelola Kelas Mata Kuliah
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Petunjuk:</strong> Atur jumlah kelas yang dibuka untuk setiap mata kuliah. 
                        Minimal 1 kelas. Kelas yang sudah ada akan dipertahankan, kelas tambahan akan diberi nama otomatis.
                    </small>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabelKelolaKelas">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="40%">Nama Mata Kuliah</th>
                                <th width="10%" class="text-center">SKS</th>
                                <th width="20%" class="text-center">Jumlah Kelas</th>
                                <th width="25%">Kelas Yang Dibuka</th>
                            </tr>
                        </thead>
                        <tbody id="bodyKelolaKelas">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanKelas">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .btn-hapus {
        transition: all 0.2s ease;
    }
    
    .btn-hapus:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    let counter = 0;
    let kelasList = []; // Store kelas management data
    
    // Data dari server
    const dosens = @json($dosens);
    const mataKuliahList = @json($mataKuliahList);
    const allMataKuliah = @json($allMataKuliah);
    const prodiId = "{{ $prodi->Id_Prodi ?? '' }}";
    
    // Debug: Tampilkan data di console
    console.log('Dosens:', dosens);
    console.log('Mata Kuliah List:', mataKuliahList);
    console.log('All Mata Kuliah:', allMataKuliah);
    console.log('Prodi ID:', prodiId);
    
    // Fungsi untuk mendapatkan kelas berdasarkan nama mata kuliah
    function getKelasByMataKuliah(namaMataKuliah) {
        return allMataKuliah.filter(mk => mk.Nama_Matakuliah === namaMataKuliah);
    }
    
    // Tambah baris beban mengajar
    document.getElementById('tambahBeban').addEventListener('click', function() {
        counter++;
        const tbody = document.getElementById('bodyBebanMengajar');
        
        const row = document.createElement('tr');
        row.id = `row-${counter}`;
        
        // Build dosen options
        let dosenOptions = '<option value="">-- Pilih Dosen --</option>';
        dosens.forEach(dosen => {
            dosenOptions += `<option value="${dosen.Id_Dosen}">${dosen.Nama_Dosen}</option>`;
        });
        
        // Build mata kuliah options
        let mataKuliahOptions = '<option value="">-- Pilih Mata Kuliah --</option>';
        mataKuliahList.forEach(mk => {
            mataKuliahOptions += `<option value="${mk.Nama_Matakuliah}" data-sks="${mk.SKS}">${mk.Nama_Matakuliah}</option>`;
        });
        
        row.innerHTML = `
            <td class="text-center align-middle">${counter}</td>
            <td>
                <select class="form-select form-select-sm" name="beban[${counter}][dosen_id]" required>
                    ${dosenOptions}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm mata-kuliah-select" 
                        data-row="${counter}" 
                        required>
                    ${mataKuliahOptions}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm kelas-select" 
                        name="beban[${counter}][mata_kuliah_id]" 
                        data-row="${counter}" 
                        required 
                        disabled>
                    <option value="">-- Pilih MK dulu --</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm text-center sks-input" 
                       name="beban[${counter}][sks]" 
                       min="1" 
                       max="6" 
                       value="3"
                       readonly
                       required>
            </td>
            <td class="text-center">
                <button type="button" 
                        class="btn btn-danger btn-sm btn-hapus" 
                        onclick="hapusBaris(${counter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        
        // Add event listener untuk mata kuliah change
        const mataKuliahSelect = row.querySelector('.mata-kuliah-select');
        const kelasSelect = row.querySelector('.kelas-select');
        const sksInput = row.querySelector('.sks-input');
        
        mataKuliahSelect.addEventListener('change', function() {
            const namaMataKuliah = this.value;
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            
            if (namaMataKuliah) {
                const kelasList = getKelasByMataKuliah(namaMataKuliah);
                
                kelasList.forEach(kelas => {
                    const option = document.createElement('option');
                    option.value = kelas.Nomor;
                    option.textContent = kelas.Kelas;
                    option.dataset.sks = kelas.SKS;
                    kelasSelect.appendChild(option);
                });
                
                kelasSelect.disabled = false;
                
                // Set SKS dari mata kuliah yang dipilih
                const selectedOption = this.options[this.selectedIndex];
                sksInput.value = selectedOption.dataset.sks || 3;
            } else {
                kelasSelect.disabled = true;
                sksInput.value = 3;
            }
            
            updateTotalSKS();
        });
        
        // Add event listener untuk kelas change (update SKS jika berbeda)
        kelasSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.sks) {
                sksInput.value = selectedOption.dataset.sks;
                updateTotalSKS();
            }
        });
        
        // Add event listener untuk update total SKS
        sksInput.addEventListener('input', updateTotalSKS);
        
        updateNomor();
        updateTotalSKS();
    });
    
    // Hapus baris
    function hapusBaris(id) {
        const row = document.getElementById(`row-${id}`);
        if (row) {
            row.remove();
            updateNomor();
            updateTotalSKS();
        }
    }
    
    // Update nomor urut
    function updateNomor() {
        const rows = document.querySelectorAll('#bodyBebanMengajar tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }
    
    // Update total SKS
    function updateTotalSKS() {
        const sksInputs = document.querySelectorAll('.sks-input');
        let total = 0;
        
        sksInputs.forEach(input => {
            const value = parseInt(input.value) || 0;
            total += value;
        });
        
        document.getElementById('totalSKS').textContent = total;
    }
    
    // Validasi form sebelum submit
    document.getElementById('formBebanMengajar').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#bodyBebanMengajar tr');
        
        if (rows.length === 0) {
            e.preventDefault();
            alert('Harap tambahkan minimal 1 beban mengajar!');
            return false;
        }
        
        // Konfirmasi submit
        if (!confirm('Apakah Anda yakin data sudah benar dan ingin mengajukan SK ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-add satu baris pertama saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tambahBeban').click();
    });
    
    // ============= KELOLA KELAS FUNCTIONALITY =============
    
    // Load data kelas when modal is opened
    document.getElementById('btnKelolaKelas').addEventListener('click', function() {
        loadKelolaKelas();
    });
    
    // Function to load kelas management data
    function loadKelolaKelas() {
        const tbody = document.getElementById('bodyKelolaKelas');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        
        if (!prodiId) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Program Studi tidak ditemukan</td></tr>';
            return;
        }
        
        console.log('Fetching kelas data for prodi:', prodiId);
        
        // Fetch data from server
        fetch(`/kaprodi/sk/beban-mengajar/kelas?prodi_id=${prodiId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                if (data.success) {
                    kelasList = data.data;
                    console.log('Kelas list:', kelasList);
                    renderKelolaKelas();
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data: ${error.message}</td></tr>`;
            });
    }
    
    // Function to render kelas management table
    function renderKelolaKelas() {
        const tbody = document.getElementById('bodyKelolaKelas');
        
        console.log('Rendering kelas list, total:', kelasList.length);
        
        if (kelasList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data mata kuliah</td></tr>';
            return;
        }
        
        tbody.innerHTML = '';
        kelasList.forEach((mk, index) => {
            console.log(`Processing MK ${index}:`, mk);
            
            try {
                const row = document.createElement('tr');
                const kelasPreview = generateKelasPreview(mk.kelas_list || [], mk.jumlah_kelas);
                
                row.innerHTML = `
                    <td class="text-center align-middle">${index + 1}</td>
                    <td class="align-middle">${mk.nama_matakuliah}</td>
                    <td class="text-center align-middle">${mk.sks}</td>
                    <td class="text-center">
                        <input type="number" 
                               class="form-control form-control-sm text-center jumlah-kelas-input" 
                               data-mk-name="${mk.nama_matakuliah}"
                               data-mk-index="${index}"
                               value="${mk.jumlah_kelas}" 
                               min="1" 
                               max="10"
                               style="max-width: 80px; margin: 0 auto;">
                    </td>
                    <td class="align-middle">
                        <span class="badge bg-secondary kelas-preview" data-mk-name="${mk.nama_matakuliah}">
                            ${kelasPreview}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            } catch (error) {
                console.error(`Error rendering row ${index}:`, error);
            }
        });
        
        // Add event listeners for input changes
        document.querySelectorAll('.jumlah-kelas-input').forEach(input => {
            input.addEventListener('input', function() {
                const mkName = this.dataset.mkName;
                const mkIndex = parseInt(this.dataset.mkIndex);
                const jumlahKelas = parseInt(this.value) || 1;
                
                // Update preview
                const preview = document.querySelector(`.kelas-preview[data-mk-name="${mkName}"]`);
                if (preview && kelasList[mkIndex]) {
                    preview.innerHTML = generateKelasPreview(kelasList[mkIndex].kelas_list, jumlahKelas);
                }
                
                // Update kelasList
                if (kelasList[mkIndex]) {
                    kelasList[mkIndex].jumlah_kelas = jumlahKelas;
                }
            });
        });
    }
    
    // Function to generate kelas preview - simple version
    function generateKelasPreview(existingKelasList, targetJumlahKelas) {
        if (existingKelasList && existingKelasList.length > 0) {
            // Show existing kelas names if available
            return existingKelasList.join(', ');
        } else {
            // Just show count, backend will generate the names when saved
            return `${targetJumlahKelas} kelas akan dibuat`;
        }
    }
    
    // Save kelas changes
    document.getElementById('btnSimpanKelas').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        // Disable button and show loading
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Prepare data to send
        const dataToSend = kelasList.map(mk => ({
            nama_matakuliah: mk.nama_matakuliah,
            sks: mk.sks,
            jumlah_kelas: mk.jumlah_kelas
        }));
        
        // Send data to server
        fetch('/kaprodi/sk/beban-mengajar/kelas/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                prodi_id: prodiId,
                kelas_data: dataToSend
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Perubahan kelas berhasil disimpan!');
                
                // Reload mata kuliah data
                location.reload();
            } else {
                alert('Gagal menyimpan perubahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan');
        })
        .finally(() => {
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
</script>
@endpush

@endsection
