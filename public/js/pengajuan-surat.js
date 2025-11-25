// ==========================================
// PENGAJUAN SURAT - AUTOCOMPLETE & FORM HANDLING
// No more draft system - simple form submission
// ==========================================

console.log('[MAGANG] Script loaded');

// ==========================================
// AUTOCOMPLETE MAHASISWA
// ==========================================

let mahasiswaIndex = 1; // Start from 1 karena 0 = pembuat
const maxMahasiswa = 5;

// Setup autocomplete untuk setiap input mahasiswa
function setupAutocomplete(inputElement) {
    const container = inputElement.closest('.mahasiswa-item');
    const resultsDiv = container.querySelector('.autocomplete-results');
    const nimInput = container.querySelector('.mahasiswa-nim');
    const angkatanInput = container.querySelector('.mahasiswa-angkatan');
    const jurusanInput = container.querySelector('.mahasiswa-jurusan');

    let selectedMahasiswaIds = getSelectedMahasiswaIds();

    inputElement.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        // Search mahasiswa via AJAX
        fetch(window.mahasiswaSearchRoute + '?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                console.log('[AUTOCOMPLETE] Results:', data);

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="autocomplete-item disabled">Tidak ditemukan</div>';
                    resultsDiv.style.display = 'block';
                    return;
                }

                resultsDiv.innerHTML = '';

                data.forEach(mhs => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';

                    // Check if already selected
                    if (selectedMahasiswaIds.includes(mhs.id)) {
                        item.classList.add('disabled');
                        item.innerHTML = `
                            <strong>${mhs.nama}</strong> (${mhs.nim})<br>
                            <small>Sudah ditambahkan</small>
                        `;
                    } else {
                        item.innerHTML = `
                            <strong>${mhs.nama}</strong> (${mhs.nim})<br>
                            <small>Angkatan ${mhs.angkatan} - ${mhs.jurusan}</small>
                        `;

                        item.addEventListener('click', function () {
                            inputElement.value = mhs.nama;
                            nimInput.value = mhs.nim;
                            angkatanInput.value = mhs.angkatan;
                            jurusanInput.value = mhs.jurusan;

                            resultsDiv.style.display = 'none';

                            console.log('[AUTOCOMPLETE] Selected:', mhs.nama);

                            // Update preview
                            updatePreview();
                        });
                    }

                    resultsDiv.appendChild(item);
                });

                resultsDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('[AUTOCOMPLETE] Error:', error);
            });
    });

    // Close autocomplete when clicking outside
    document.addEventListener('click', function (e) {
        if (!container.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
}

// Get list of selected mahasiswa IDs
function getSelectedMahasiswaIds() {
    const ids = [];
    document.querySelectorAll('.mahasiswa-nim').forEach(input => {
        if (input.value) {
            // Try to get mahasiswa ID from data attribute or search by NIM
            const nimValue = input.value;
            ids.push(nimValue); // Use NIM as identifier for now
        }
    });
    return ids;
}

// ==========================================
// TAMBAH/HAPUS MAHASISWA
// ==========================================

document.getElementById('btn-tambah-mahasiswa')?.addEventListener('click', function () {
    const container = document.getElementById('mahasiswa-container');
    const currentCount = container.querySelectorAll('.mahasiswa-item').length;

    if (currentCount >= maxMahasiswa) {
        alert('Maksimal ' + maxMahasiswa + ' mahasiswa dalam satu pengajuan');
        return;
    }

    // Clone template
    const template = document.getElementById('mahasiswa-template');
    const clone = template.content.cloneNode(true);

    // Set index
    const itemDiv = clone.querySelector('.mahasiswa-item');
    itemDiv.dataset.index = mahasiswaIndex;

    // Update item number
    clone.querySelector('.item-number').textContent = mahasiswaIndex + 1;

    // Update name attributes
    clone.querySelector('.mahasiswa-nama').name = `mahasiswa[${mahasiswaIndex}][nama]`;
    clone.querySelector('.mahasiswa-nama').dataset.index = mahasiswaIndex;

    clone.querySelector('.mahasiswa-nim').name = `mahasiswa[${mahasiswaIndex}][nim]`;
    clone.querySelector('.mahasiswa-nim').dataset.index = mahasiswaIndex;

    clone.querySelector('.mahasiswa-angkatan').name = `mahasiswa[${mahasiswaIndex}][angkatan]`;
    clone.querySelector('.mahasiswa-angkatan').dataset.index = mahasiswaIndex;

    clone.querySelector('.mahasiswa-jurusan').name = `mahasiswa[${mahasiswaIndex}][jurusan]`;
    clone.querySelector('.mahasiswa-jurusan').dataset.index = mahasiswaIndex;

    // Append to container
    container.appendChild(clone);

    // Setup autocomplete for new item
    const newItem = container.querySelector(`.mahasiswa-item[data-index="${mahasiswaIndex}"]`);
    const autocompleteInput = newItem.querySelector('.autocomplete-mahasiswa');
    setupAutocomplete(autocompleteInput);

    // Setup hapus button
    newItem.querySelector('.btn-hapus-mahasiswa').addEventListener('click', function () {
        newItem.remove();
        updatePreview();
    });

    mahasiswaIndex++;

    console.log('[MAGANG] Mahasiswa item added, index:', mahasiswaIndex - 1);
});

// ==========================================
// PREVIEW UPDATE
// ==========================================

function updatePreview() {
    // Update mahasiswa list in preview
    const mahasiswaItems = document.querySelectorAll('.mahasiswa-item');
    const previewList = document.getElementById('preview-mahasiswa-list');

    if (!previewList) return;

    previewList.innerHTML = '';

    mahasiswaItems.forEach((item, index) => {
        const nama = item.querySelector('.mahasiswa-nama').value || '[Nama]';
        const nim = item.querySelector('.mahasiswa-nim').value || '[NIM]';
        const angkatan = item.querySelector('.mahasiswa-angkatan').value || '-';

        const previewItem = document.createElement('div');
        previewItem.className = 'preview-mahasiswa-item';
        previewItem.dataset.index = index;

        previewItem.innerHTML = `
            <strong>${index + 1}. <span class="preview-mhs-nama">${nama}</span></strong><br>
            <small>NIM: <span class="preview-mhs-nim">${nim}</span> | Angkatan: <span class="preview-mhs-angkatan">${angkatan}</span></small>
        `;

        previewList.appendChild(previewItem);
    });

    console.log('[PREVIEW] Updated mahasiswa list');
}

// Update preview on form field changes
document.addEventListener('DOMContentLoaded', function () {
    // Initial preview update
    updatePreview();

    // Update preview Program Studi dari mahasiswa pertama
    const firstMahasiswaProdi = document.querySelector('.mahasiswa-item[data-index="0"] .mahasiswa-jurusan');
    if (firstMahasiswaProdi && firstMahasiswaProdi.value) {
        const previewProdi = document.getElementById('preview-jurusan-magang');
        if (previewProdi) {
            previewProdi.textContent = firstMahasiswaProdi.value;
        }

        // Update preview nama dan nim untuk tanda tangan
        const firstMahasiswaNama = document.querySelector('.mahasiswa-item[data-index="0"] .mahasiswa-nama');
        const firstMahasiswaNim = document.querySelector('.mahasiswa-item[data-index="0"] .mahasiswa-nim');

        if (firstMahasiswaNama && firstMahasiswaNama.value) {
            const previewNamaTTD = document.getElementById('preview-nama-magang-ttd');
            if (previewNamaTTD) {
                previewNamaTTD.textContent = firstMahasiswaNama.value;
            }
        }

        if (firstMahasiswaNim && firstMahasiswaNim.value) {
            const previewNimTTD = document.getElementById('preview-nim-magang-ttd');
            if (previewNimTTD) {
                previewNimTTD.textContent = firstMahasiswaNim.value;
            }
        }
    }

    // Dosen Pembimbing 1
    document.getElementById('input-dospem1-magang')?.addEventListener('change', function () {
        const preview = document.getElementById('preview-dospem1-magang');
        if (preview) {
            preview.innerHTML = this.value || '<span class="preview-placeholder">[Pilih Dosen]</span>';
        }
    });

    // Dosen Pembimbing 2
    document.getElementById('input-dospem2-magang')?.addEventListener('change', function () {
        const preview = document.getElementById('preview-dospem2-magang');
        if (preview) {
            preview.innerHTML = this.value || '<span class="preview-placeholder">[Opsional]</span>';
        }
    });

    // Judul Penelitian
    document.getElementById('input-judul-magang')?.addEventListener('input', function () {
        const preview = document.getElementById('preview-judul-magang');
        if (preview) {
            preview.innerHTML = this.value || '<span class="preview-placeholder">[Judul Penelitian]</span>';
        }
    });

    // Nama Instansi
    document.getElementById('input-instansi-magang')?.addEventListener('input', function () {
        const preview = document.getElementById('preview-instansi-magang');
        if (preview) {
            preview.innerHTML = this.value || '<span class="preview-placeholder">[Nama Instansi]</span>';
        }
    });

    // Tanggal Mulai & Selesai
    const tanggalMulai = document.getElementById('input-mulai-magang');
    const tanggalSelesai = document.getElementById('input-selesai-magang');

    function updateJangkaWaktu() {
        const preview = document.getElementById('preview-jangka-waktu-magang');
        if (!preview) return;

        const mulai = tanggalMulai?.value;
        const selesai = tanggalSelesai?.value;

        if (mulai && selesai) {
            preview.textContent = `${mulai} s/d ${selesai}`;
        } else {
            preview.innerHTML = '<span class="preview-placeholder">[Tanggal]</span>';
        }
    }

    tanggalMulai?.addEventListener('change', updateJangkaWaktu);
    tanggalSelesai?.addEventListener('change', updateJangkaWaktu);

    // Preview Tanda Tangan
    document.getElementById('input-ttd-magang')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = document.getElementById('preview-ttd-image');
                if (img) {
                    img.src = event.target.result;
                    img.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Preview tanggal hari ini
    const today = new Date();
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    const tanggalStr = today.toLocaleDateString('id-ID', options);
    const previewTanggal = document.getElementById('preview-tanggal-magang');
    if (previewTanggal) {
        previewTanggal.textContent = tanggalStr;
    }

    console.log('[MAGANG] Preview handlers initialized');
});

console.log('[MAGANG] Script ready');
