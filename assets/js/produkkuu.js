document.addEventListener('DOMContentLoaded', function () {

    console.log('✔ produk.js loaded');

    // ======================
    // HELPERS
    // ======================
    const qs = (s) => document.querySelector(s);
    const byId = (id) => document.getElementById(id);

    function safeInt(v) {
        const n = parseInt(v);
        return isNaN(n) ? 0 : n;
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    function getToday() {
        return new Date().toISOString().split('T')[0];
    }

    function buildImageUrl(gambar) {
        if (!gambar) return 'https://via.placeholder.com/80';

        // sudah full URL
        if (typeof gambar === 'string' && gambar.startsWith('http')) {
            return gambar;
        }

        // relatif → tambah BASE_URL
        return BASE_URL + gambar;
    }

    // ======================
    // TOGGLE METODE GAMBAR
    // ======================
    const mode = byId('mode_gambar');
    const inputUpload = byId('input_upload');
    const inputUrl = byId('input_url');

    function toggleGambar() {
        if (!mode || !inputUpload || !inputUrl) return;

        if (mode.value === 'upload') {
            inputUpload.style.display = 'block';
            inputUrl.style.display = 'none';
        } else {
            inputUpload.style.display = 'none';
            inputUrl.style.display = 'block';
        }
    }

    if (mode) {
        mode.addEventListener('change', toggleGambar);
        toggleGambar(); // default saat load
    }

    // ======================
    // DRAG & DROP UPLOAD
    // ======================
    const dropArea = byId('drop-area');
    const fileInput = byId('fileElem');
    const preview = byId('preview');

    if (dropArea && fileInput) {

        dropArea.addEventListener('click', () => fileInput.click());

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');

            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
            handleFile(file);
        });

        fileInput.addEventListener('change', function () {
            handleFile(this.files[0]);
        });

        function handleFile(file) {
            if (!file) return;

            const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            const maxSize = 2 * 1024 * 1024;

            if (!allowed.includes(file.type)) {
                alert('Hanya file JPG, PNG, WEBP');
                fileInput.value = '';
                return;
            }

            if (file.size > maxSize) {
                alert('Maksimal 2MB');
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // ======================
    // PREVIEW DARI URL
    // ======================
    const inputUrlField = byId('gambar_url');
    const previewUrl = byId('preview_url');

    if (inputUrlField && previewUrl) {
        inputUrlField.addEventListener('input', function () {
            const url = this.value.trim();

            if (url.length > 5) {
                previewUrl.src = url;
                previewUrl.style.display = 'block';
            } else {
                previewUrl.style.display = 'none';
            }
        });
    }

    // ======================
    // SUBMIT AJAX (FORM)
    // ======================
    const form = qs('form');
    const btn = byId('btnSubmit');
    const loading = byId('loading');
    const progressBox = byId('progressBox');
    const progressBar = byId('progressBar');

    if (form) {
        form.addEventListener('submit', function (e) {

            e.preventDefault();

            if (btn) btn.disabled = true;
            if (loading) loading.style.display = 'block';

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            if (progressBox) progressBox.style.display = 'block';

            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable && progressBar) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressBar.innerText = percent + '%';
                }
            });

            xhr.onload = function () {
                window.location.href = BASE_URL + 'produk';
            };

            xhr.open('POST', form.action, true);
            xhr.send(formData);
        });
    }

    // ======================
    // SEARCH AJAX REALTIME
    // ======================
    const searchInput = byId('searchProduk');
    const tableBody = byId('produkTable');

    let debounce;

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function () {

            clearTimeout(debounce);

            debounce = setTimeout(() => {

                fetch(BASE_URL + 'produk/search?q=' + encodeURIComponent(this.value))
                    .then(res => res.json())
                    .then(data => {

                        let html = '';

                        if (!data || data.length === 0) {
                            html = `<tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>`;
                        }

                        data.forEach((p, i) => {

                            const gambar = buildImageUrl(p.gambar);
                            const stok = safeInt(p.stok);
                            const min = safeInt(p.stok_minimal);

                            html += `
                            <tr>
                                <td>${i + 1}</td>

                                <td>
                                    <img src="${gambar}" 
                                         onerror="this.src='https://via.placeholder.com/80'"
                                         style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                </td>

                                <td><strong>${p.nama_produk || '-'}</strong></td>

                                <td>${p.nama_kategori ? p.nama_kategori : '-'}</td>
                                <td>${p.nama_supplier ? p.nama_supplier : '-'}</td>

                                <td>
                                    <div class="text-success fw-bold">
                                        Rp ${formatRupiah(p.harga_jual)}
                                    </div>
                                    <small class="text-muted">
                                        Beli: Rp ${formatRupiah(p.harga_beli)}
                                    </small>
                                </td>

                                <td>
                                    ${stok <= min
                                        ? `<span class="badge bg-danger">⚠ ${stok}</span>`
                                        : `<span class="badge bg-success">${stok}</span>`
                                    }
                                </td>

                                <td>
                                    ${p.tanggal_kadaluarsa && p.tanggal_kadaluarsa <= getToday()
                                        ? `<span class="badge bg-warning text-dark">Kadaluarsa</span>`
                                        : `<span class="text-muted">${p.tanggal_kadaluarsa || '-'}</span>`
                                    }
                                </td>

                                <td class="text-center">
                                    <a href="${BASE_URL}produk/edit/${p.id_produk}" class="btn btn-sm btn-warning">✏️</a>
                                    <button onclick="hapus('${BASE_URL}produk/hapus/${p.id_produk}')" class="btn btn-danger btn-sm">🗑️</button>
                                </td>
                            </tr>
                            `;
                        });

                        tableBody.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });

            }, 400);
        });
    }

});