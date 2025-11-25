/**
 * Pengajuan Surat - JavaScript Module
 * Menangani form magang dan preview surat
 */

document.addEventListener('DOMContentLoaded', function () {

    // === SCRIPT KHUSUS UNTUK SURAT MAGANG === //
    let mahasiswaIndex = 1; // Mulai dari 1 karena 0 sudah ada (mahasiswa yang login)
    let autocompleteTimeout = null;
    let currentDraftId = null;
    let autoSaveTimeout = null;
    const AUTOSAVE_DELAY = 5000; // 5 detik
    let isSubmitting = false; // Flag untuk track form submission

    // Inisialisasi form magang jika ada
    if (document.getElementById('mahasiswa-container')) {
        loadDraft(); // Load draft yang auto-created dari server
        initMagangPreview();

        // AUTO-SAVE setiap 5 detik
        setInterval(function () {
            if (currentDraftId) {
                console.log('[AUTO-SAVE] Triggering periodic save...');
                saveDraft();
            }
        }, AUTOSAVE_DELAY);

        // DELETE DRAFT saat keluar dari halaman
        window.addEventListener('beforeunload', function (e) {
            if (!isSubmitting && currentDraftId) {
                deleteDraftOnExit();
            }
        });

        // Mark as submitting saat form di-submit
        const form = document.querySelector('form[action*="pengajuan.magang.store"]');
        if (form) {
            form.addEventListener('submit', function () {
                isSubmitting = true;
                console.log('[DRAFT] Form submitting, will not delete draft');
            });
        }
    }

    // === LOAD DRAFT DARI SERVER (Auto-created saat buka form) ===
    function loadDraft() {
        console.log('[DRAFT-LOAD] Loading auto-created draft...');
        fetch('/mahasiswa/api/draft/load')
            .then(response => {
                console.log('[DRAFT-LOAD] Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('[DRAFT-LOAD] Draft data:', data);
                if (data.draft) {
                    currentDraftId = data.draft.id_draft;
                    console.log('[DRAFT-LOAD] Draft loaded with ID:', currentDraftId);

                    // Restore form fields
                    if (data.draft.Nama_Instansi) document.getElementById('input-instansi-magang').value = data.draft.Nama_Instansi;
                    if (data.draft.Judul_Penelitian) document.getElementById('input-judul-magang').value = data.draft.Judul_Penelitian;
                    if (data.draft.Tanggal_Mulai) document.getElementById('input-mulai-magang').value = data.draft.Tanggal_Mulai;
                    if (data.draft.Tanggal_Selesai) document.getElementById('input-selesai-magang').value = data.draft.Tanggal_Selesai;
                    if (data.draft.Dosen_Pembimbing_1) document.getElementById('input-dospem1-magang').value = data.draft.Dosen_Pembimbing_1;
                    if (data.draft.Dosen_Pembimbing_2) document.getElementById('input-dospem2-magang').value = data.draft.Dosen_Pembimbing_2;

                    // Restore mahasiswa pending dan confirmed
                    if (data.mahasiswa_pending && data.mahasiswa_pending.length > 0) {
                        console.log('[DRAFT-LOAD] Restoring pending mahasiswa:', data.mahasiswa_pending);
                        data.mahasiswa_pending.forEach(mhs => {
                            addMahasiswaFromDraft(mhs, 'pending');
                        });
                    }

                    if (data.mahasiswa_confirmed && data.mahasiswa_confirmed.length > 1) {
                        console.log('[DRAFT-LOAD] Restoring confirmed mahasiswa (skipping first)');
                        // Skip index 0 (pembuat)
                        for (let i = 1; i < data.mahasiswa_confirmed.length; i++) {
                            addMahasiswaFromDraft(data.mahasiswa_confirmed[i], 'confirmed');
                        }
                    }

                    // Update preview setelah load
                    setTimeout(updateMahasiswaPreviewList, 500);
                } else {
                    console.log('[DRAFT-LOAD] No draft found (this should not happen)');
                }
            })
            .catch(error => console.error('[DRAFT-LOAD ERROR] Loading draft:', error));
    }

    // Tambah mahasiswa dari draft dengan status badge
    function addMahasiswaFromDraft(mhs, status) {
        mahasiswaIndex++;
        const template = document.getElementById('mahasiswa-template');
        if (!template) return;

        const clone = template.content.cloneNode(true);
        const container = clone.querySelector('.mahasiswa-item');
        container.setAttribute('data-index', mahasiswaIndex);
        container.setAttribute('data-mahasiswa-id', mhs.id);

        // Update nomor
        clone.querySelector('.item-number').textContent = mahasiswaIndex;

        // Fill data
        const namaInput = clone.querySelector('.mahasiswa-nama');
        const nimInput = clone.querySelector('.mahasiswa-nim');
        const angkatanInput = clone.querySelector('.mahasiswa-angkatan');
        const jurusanInput = clone.querySelector('.mahasiswa-jurusan');

        namaInput.value = mhs.nama || '';
        namaInput.name = `mahasiswa[${mahasiswaIndex}][nama]`;
        namaInput.setAttribute('data-index', mahasiswaIndex);
        namaInput.classList.remove('autocomplete-mahasiswa');
        namaInput.setAttribute('readonly', true);

        nimInput.value = mhs.nim || '';
        nimInput.name = `mahasiswa[${mahasiswaIndex}][nim]`;
        nimInput.setAttribute('data-index', mahasiswaIndex);

        angkatanInput.value = mhs.angkatan || '';
        angkatanInput.name = `mahasiswa[${mahasiswaIndex}][angkatan]`;
        angkatanInput.setAttribute('data-index', mahasiswaIndex);

        const jurusanMahasiswaLogin = document.querySelector('.mahasiswa-jurusan[data-index="0"]')?.value || '';
        jurusanInput.value = jurusanMahasiswaLogin;
        jurusanInput.name = `mahasiswa[${mahasiswaIndex}][jurusan]`;
        jurusanInput.setAttribute('data-index', mahasiswaIndex);

        // Add status badge
        const headerDiv = clone.querySelector('.d-flex.justify-content-between');
        const badge = document.createElement('span');
        if (status === 'pending') {
            badge.className = 'badge bg-warning text-dark ms-2';
            badge.textContent = 'Menunggu Persetujuan';
            container.style.opacity = '0.7';
        } else {
            badge.className = 'badge bg-success ms-2';
            badge.textContent = 'Terkonfirmasi';
        }
        headerDiv.querySelector('h6').appendChild(badge);

        document.getElementById('mahasiswa-container').appendChild(clone);
    }

    // === SAVE DRAFT (Called every 5 seconds or when data changes) ===
    function saveDraft(mahasiswaIdTambahan = null) {
        console.log('[DRAFT-SAVE] Saving draft... mahasiswa_id:', mahasiswaIdTambahan);
        const formData = new FormData();
        const jenisSuratInput = document.querySelector('input[name="Id_Jenis_Surat"]');
        if (!jenisSuratInput) {
            console.error('[ERROR] Id_Jenis_Surat input not found');
            return;
        }

        formData.append('Id_Jenis_Surat', jenisSuratInput.value);
        formData.append('nama_instansi', document.getElementById('input-instansi-magang')?.value || '');
        formData.append('judul_penelitian', document.getElementById('input-judul-magang')?.value || '');
        formData.append('tanggal_mulai', document.getElementById('input-mulai-magang')?.value || '');
        formData.append('tanggal_selesai', document.getElementById('input-selesai-magang')?.value || '');
        formData.append('dosen_pembimbing_1', document.getElementById('input-dospem1-magang')?.value || '');
        formData.append('dosen_pembimbing_2', document.getElementById('input-dospem2-magang')?.value || '');

        if (mahasiswaIdTambahan) {
            console.log('[DEBUG] Adding mahasiswa_id_tambahan:', mahasiswaIdTambahan);
            formData.append('mahasiswa_id_tambahan', mahasiswaIdTambahan);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('[ERROR] CSRF token not found');
            return;
        }

        fetch('/mahasiswa/api/draft/save', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                console.log('[DEBUG] Save response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('[DEBUG] Save response data:', data);
                if (data.success) {
                    currentDraftId = data.draft_id;
                    console.log('[DEBUG] Draft saved successfully, ID:', data.draft_id);
                } else if (data.error) {
                    console.error('[ERROR] Save failed:', data.error);
                }
            })
            .catch(error => console.error('[ERROR] Saving draft:', error));
    }

    // === DELETE DRAFT saat keluar dari halaman ===
    function deleteDraftOnExit() {
        if (!currentDraftId) return;

        console.log('[DRAFT-DELETE] Deleting draft on exit...');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) return;

        // Use synchronous XHR for page unload (sendBeacon doesn't support custom headers)
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/mahasiswa/api/draft/delete', false); // false = synchronous
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        try {
            xhr.send(JSON.stringify({
                draft_id: currentDraftId
            }));
            console.log('[DRAFT-DELETE] Draft deleted successfully');
        } catch (error) {
            console.error('[DRAFT-DELETE] Error:', error);
        }
    }

    // Trigger auto-save on input change (debounced)
    const autoSaveFields = [
        'input-instansi-magang',
        'input-judul-magang',
        'input-mulai-magang',
        'input-selesai-magang',
        'input-dospem1-magang',
        'input-dospem2-magang'
    ];

    autoSaveFields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', function () {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => saveDraft(), AUTOSAVE_DELAY);
            });
        }
    });

    // Fungsi untuk inisialisasi form magang
    function initMagangPreview() {
        // Set tanggal hari ini di preview
        const previewTanggal = document.getElementById('preview-tanggal-magang');
        if (previewTanggal) {
            previewTanggal.textContent = formatTanggal(new Date().toISOString().split('T')[0]);
        }

        // Tambah event listener untuk tombol tambah mahasiswa
        const btnTambah = document.getElementById('btn-tambah-mahasiswa');
        if (btnTambah) {
            btnTambah.addEventListener('click', tambahMahasiswa);
        }

        // Event listener untuk mahasiswa pertama (yang sudah ada) - dengan multiple approach
        const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
        const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
        const firstMhsAngkatan = document.querySelector('.mahasiswa-angkatan[data-index="0"]');

        if (firstMhsNama) {
            firstMhsNama.addEventListener('input', updateMahasiswaPreviewList);
            firstMhsNama.addEventListener('change', updateMahasiswaPreviewList);
        }
        if (firstMhsNim) {
            firstMhsNim.addEventListener('input', updateMahasiswaPreviewList);
            firstMhsNim.addEventListener('change', updateMahasiswaPreviewList);
        }
        if (firstMhsAngkatan) {
            firstMhsAngkatan.addEventListener('change', updateMahasiswaPreviewList);
            firstMhsAngkatan.addEventListener('input', updateMahasiswaPreviewList);
        }

        // Event delegation untuk semua input mahasiswa (termasuk yang ditambah nanti)
        document.addEventListener('input', function (e) {
            if (e.target.matches('.mahasiswa-nama, .mahasiswa-nim, .mahasiswa-angkatan')) {
                updateMahasiswaPreviewList();
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.matches('.mahasiswa-angkatan')) {
                updateMahasiswaPreviewList();
            }
        });

        // Update preview dari input yang sudah ada
        setupPreviewListeners();

        // Initial update preview mahasiswa - dipanggil multiple kali untuk memastikan
        updateMahasiswaPreviewList();
        setTimeout(() => {
            updateMahasiswaPreviewList();
        }, 100);
        setTimeout(() => {
            updateMahasiswaPreviewList();
        }, 300);
    }

    // Helper function to format date
    function formatTanggal(dateStr) {
        if (!dateStr) return null;
        const date = new Date(dateStr);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Setup listeners untuk update preview
    function setupPreviewListeners() {
        const inputInstansi = document.getElementById('input-instansi-magang');
        const inputJudul = document.getElementById('input-judul-magang');
        const inputDospem1 = document.getElementById('input-dospem1-magang');
        const inputDospem2 = document.getElementById('input-dospem2-magang');
        const inputMulai = document.getElementById('input-mulai-magang');
        const inputSelesai = document.getElementById('input-selesai-magang');

        if (inputInstansi) inputInstansi.addEventListener('input', updatePreview);
        if (inputJudul) inputJudul.addEventListener('input', updatePreview);
        if (inputDospem1) inputDospem1.addEventListener('change', updatePreview);
        if (inputDospem2) inputDospem2.addEventListener('change', updatePreview);
        if (inputMulai) inputMulai.addEventListener('change', updatePreview);
        if (inputSelesai) inputSelesai.addEventListener('change', updatePreview);

        // Event listener untuk upload TTD dengan background removal
        const inputTTD = document.getElementById('input-ttd-magang');
        if (inputTTD) {
            inputTTD.addEventListener('change', handleTTDUpload);
        }

        // Initial preview
        updatePreview();
    }

    // Fungsi untuk update preview
    function updatePreview() {
        const inputInstansi = document.getElementById('input-instansi-magang');
        const inputJudul = document.getElementById('input-judul-magang');
        const inputDospem1 = document.getElementById('input-dospem1-magang');
        const inputDospem2 = document.getElementById('input-dospem2-magang');
        const inputMulai = document.getElementById('input-mulai-magang');
        const inputSelesai = document.getElementById('input-selesai-magang');

        const previewInstansi = document.getElementById('preview-instansi-magang');
        const previewJudul = document.getElementById('preview-judul-magang');
        const previewDospem1 = document.getElementById('preview-dospem1-magang');
        const previewDospem2 = document.getElementById('preview-dospem2-magang');
        const previewJangkaWaktu = document.getElementById('preview-jangka-waktu-magang');

        if (previewInstansi) {
            previewInstansi.innerHTML = inputInstansi?.value || '<span class="preview-placeholder">[Nama Instansi]</span>';
        }
        if (previewJudul) {
            previewJudul.innerHTML = inputJudul?.value || '<span class="preview-placeholder">[Judul Penelitian]</span>';
        }
        if (previewDospem1) {
            previewDospem1.innerHTML = inputDospem1?.value || '<span class="preview-placeholder">[Pilih Dosen]</span>';
        }
        if (previewDospem2) {
            previewDospem2.innerHTML = inputDospem2?.value || '<span class="preview-placeholder">[Opsional]</span>';
        }
        // Note: preview-dospem1-magang-ttd (Koordinator KP/TA) sekarang diambil dari backend (Kaprodi)
        if (previewJangkaWaktu) {
            const mulai = formatTanggal(inputMulai?.value);
            const selesai = formatTanggal(inputSelesai?.value);
            if (mulai && selesai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ${selesai}`;
            } else if (mulai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ...`;
            } else {
                previewJangkaWaktu.innerHTML = '<span class="preview-placeholder">[Tanggal]</span>';
            }
        }

        // Update daftar mahasiswa di preview
        updateMahasiswaPreviewList();

        // Update jurusan di preview (ambil dari mahasiswa pertama)
        const firstMhsJurusan = document.querySelector('.mahasiswa-jurusan[data-index="0"]');
        const previewJurusan = document.getElementById('preview-jurusan-magang');
        if (previewJurusan && firstMhsJurusan) {
            previewJurusan.innerHTML = firstMhsJurusan.value || '<span class="preview-placeholder">[Jurusan]</span>';
        }

        // Update nama dan NIM di bagian tanda tangan (mahasiswa pertama)
        const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
        const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
        const previewNamaTtd = document.getElementById('preview-nama-magang-ttd');
        const previewNIMTtd = document.getElementById('preview-nim-magang-ttd');

        if (previewNamaTtd && firstMhsNama) {
            previewNamaTtd.innerHTML = firstMhsNama.value || '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
        }
        if (previewNIMTtd && firstMhsNim) {
            previewNIMTtd.innerHTML = firstMhsNim.value || '<span class="preview-placeholder">[NIM]</span>';
        }
    }

    // Fungsi untuk update daftar mahasiswa di preview
    function updateMahasiswaPreviewList() {
        const previewList = document.getElementById('preview-mahasiswa-list');
        if (!previewList) {
            console.log('Preview list element not found');
            return;
        }

        previewList.innerHTML = '';

        // Ambil semua mahasiswa item
        const mahasiswaItems = document.querySelectorAll('.mahasiswa-item');
        console.log('Found mahasiswa items:', mahasiswaItems.length);

        mahasiswaItems.forEach((item, index) => {
            const dataIndex = item.getAttribute('data-index');
            const namaInput = item.querySelector(`.mahasiswa-nama[data-index="${dataIndex}"]`);
            const nimInput = item.querySelector(`.mahasiswa-nim[data-index="${dataIndex}"]`);
            const angkatanInput = item.querySelector(`.mahasiswa-angkatan[data-index="${dataIndex}"]`);

            const nama = namaInput?.value || '';
            const nim = nimInput?.value || '';
            const angkatan = angkatanInput?.value || '';

            console.log(`Mahasiswa ${index}:`, { nama, nim, angkatan, dataIndex });

            const previewItem = document.createElement('div');
            previewItem.className = 'preview-mahasiswa-item';
            previewItem.setAttribute('data-preview-index', dataIndex);

            const namaDisplay = nama ? nama : '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
            const nimDisplay = nim ? nim : '<span class="preview-placeholder">[NIM]</span>';
            const angkatanDisplay = angkatan ? angkatan : '<span class="preview-placeholder">-</span>';

            previewItem.innerHTML = `
                <strong>${index + 1}. <span class="preview-mhs-nama">${namaDisplay}</span></strong><br>
                <small>NIM: <span class="preview-mhs-nim">${nimDisplay}</span> | Angkatan: <span class="preview-mhs-angkatan">${angkatanDisplay}</span></small>
            `;

            previewList.appendChild(previewItem);
        });
    }

    // Fungsi tambah mahasiswa
    function tambahMahasiswa() {
        const container = document.getElementById('mahasiswa-container');
        const template = document.getElementById('mahasiswa-template');
        const clone = template.content.cloneNode(true);

        // Update index
        const item = clone.querySelector('.mahasiswa-item');
        item.setAttribute('data-index', mahasiswaIndex);

        // Update nomor item
        clone.querySelector('.item-number').textContent = mahasiswaIndex + 1;

        // Update name attributes
        clone.querySelector('.mahasiswa-nama').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nama]`);
        clone.querySelector('.mahasiswa-nama').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-nim').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nim]`);
        clone.querySelector('.mahasiswa-nim').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-jurusan').setAttribute('name', `mahasiswa[${mahasiswaIndex}][jurusan]`);
        clone.querySelector('.mahasiswa-jurusan').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-angkatan').setAttribute('name', `mahasiswa[${mahasiswaIndex}][angkatan]`);
        clone.querySelector('.mahasiswa-angkatan').setAttribute('data-index', mahasiswaIndex);

        // Event listener untuk tombol hapus
        clone.querySelector('.btn-hapus-mahasiswa').addEventListener('click', function () {
            item.remove();
            updateItemNumbers();
            updateMahasiswaPreviewList(); // Update preview setelah hapus
        });

        // Event listener untuk autocomplete
        const inputNama = clone.querySelector('.autocomplete-mahasiswa');
        inputNama.addEventListener('input', function (e) {
            handleAutocomplete(e.target);
        });

        // Event listener untuk update preview tidak perlu karena sudah pakai delegation

        container.appendChild(clone);
        mahasiswaIndex++;

        // Update preview setelah menambah mahasiswa baru
        setTimeout(updateMahasiswaPreviewList, 50);
    }

    // Fungsi hapus dan update nomor urut
    function updateItemNumbers() {
        document.querySelectorAll('.mahasiswa-item').forEach((item, index) => {
            const number = item.querySelector('.item-number');
            if (number) {
                number.textContent = index + 1;
            }
        });
    }

    // Fungsi autocomplete
    function handleAutocomplete(input) {
        clearTimeout(autocompleteTimeout);

        const query = input.value.trim();
        const resultsDiv = input.nextElementSibling;

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        const searchUrl = window.mahasiswaSearchRoute || '/mahasiswa/api/mahasiswa/search';

        autocompleteTimeout = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="autocomplete-item"><small>Tidak ada hasil</small></div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    resultsDiv.innerHTML = '';
                    data.forEach(mhs => {
                        const div = document.createElement('div');

                        // Cek status KP
                        const isAvailable = mhs.is_available !== false;
                        const statusText = mhs.status_kp === 'Sedang_Melaksanakan' ? ' <span style="color:#dc3545;">(Sedang KP/Magang)</span>' : '';

                        div.className = 'autocomplete-item';
                        if (!isAvailable) {
                            div.classList.add('disabled');
                            div.title = 'Mahasiswa ini sedang melaksanakan KP/Magang dan tidak dapat ditambahkan';
                        }

                        div.innerHTML = `<strong>${mhs.nama}</strong>${statusText}<br><small>NIM: ${mhs.nim} - ${mhs.jurusan}</small>`;

                        if (isAvailable) {
                            div.addEventListener('click', () => {
                                selectMahasiswa(input, mhs);
                                resultsDiv.style.display = 'none';
                            });
                        }

                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching mahasiswa:', error);
                });
        }, 300);
    }

    // Fungsi select mahasiswa dari autocomplete
    function selectMahasiswa(input, mhs) {
        const index = input.getAttribute('data-index');

        // Set nilai
        input.value = mhs.nama;
        const nimInput = document.querySelector(`.mahasiswa-nim[data-index="${index}"]`);
        const jurusanInput = document.querySelector(`.mahasiswa-jurusan[data-index="${index}"]`);
        const angkatanInput = document.querySelector(`.mahasiswa-angkatan[data-index="${index}"]`);

        if (nimInput) nimInput.value = mhs.nim;
        if (jurusanInput) jurusanInput.value = mhs.jurusan;
        if (angkatanInput) angkatanInput.value = mhs.angkatan;

        // Simpan ID mahasiswa di container
        const container = input.closest('.mahasiswa-item');
        if (container) {
            container.setAttribute('data-mahasiswa-id', mhs.id);

            // Tambah badge pending
            const headerDiv = container.querySelector('.d-flex.justify-content-between h6');
            if (headerDiv && !headerDiv.querySelector('.badge')) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-warning text-dark ms-2';
                badge.textContent = 'Menunggu Persetujuan';
                headerDiv.appendChild(badge);
                container.style.opacity = '0.7';
            }
        }

        // Tutup autocomplete
        input.nextElementSibling.style.display = 'none';

        // LANGSUNG KIRIM INVITATION
        saveDraft(mhs.id);

        // Update preview setelah select
        updateMahasiswaPreviewList();
    }

    // Close autocomplete ketika klik di luar
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('autocomplete-mahasiswa')) {
            document.querySelectorAll('.autocomplete-results').forEach(div => {
                div.style.display = 'none';
            });
        }
    });

    // Fungsi untuk handle upload TTD dan preview
    function handleTTDUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const previewImg = document.getElementById('preview-ttd-image');
        const processingDiv = document.getElementById('ttd-processing');

        // Show processing indicator
        if (processingDiv) processingDiv.style.display = 'block';

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                // Create canvas for background removal
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = img.width;
                canvas.height = img.height;

                // Draw image
                ctx.drawImage(img, 0, 0);

                // Get image data
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;

                // Simple background removal - remove white/light colors
                for (let i = 0; i < data.length; i += 4) {
                    const red = data[i];
                    const green = data[i + 1];
                    const blue = data[i + 2];

                    // If pixel is mostly white/light (threshold = 200)
                    if (red > 200 && green > 200 && blue > 200) {
                        data[i + 3] = 0; // Make transparent
                    }
                }

                ctx.putImageData(imageData, 0, 0);

                // Set preview image
                const processedImage = canvas.toDataURL('image/png');
                previewImg.src = processedImage;
                previewImg.style.display = 'block';
                previewImg.style.maxHeight = '80px';
                previewImg.style.margin = '10px auto';

                // Hide processing indicator
                if (processingDiv) processingDiv.style.display = 'none';
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Note: initMagangPreview() dipanggil dari event 'change' dropdown jenis surat
});
