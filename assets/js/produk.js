document.addEventListener('DOMContentLoaded', function () {

    console.log('✔ produk.js loaded');

    const qs = (s) => document.querySelector(s);
    const byId = (id) => document.getElementById(id);

    // ======================
    // UTIL
    // ======================
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
        if (!gambar) return BASE_URL + 'assets/no-image.png';
        if (gambar.startsWith('http')) return gambar;
        return BASE_URL + gambar;
    }

    // ======================
    // HIGHLIGHT KEYWORD
    // ======================
    function highlight(text, keyword) {
        if (!keyword) return text;
        const safe = text.toString();
        const regex = new RegExp(`(${keyword})`, 'gi');
        return safe.replace(regex, '<mark>$1</mark>');
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

            const keyword = this.value.trim();

            // kalau kosong → reload awal
            if (keyword.length === 0) {
                location.reload();
                return;
            }

            debounce = setTimeout(() => {

                fetch(BASE_URL + 'produk/search?q=' + encodeURIComponent(keyword))
                    .then(res => res.json())
                    .then(data => {

                        console.log('DATA:', data);

                        let html = '';

                        if (!data || data.length === 0) {
                            html = `<tr>
                                <td colspan="9" class="text-center text-muted">
                                    🔍 Data tidak ditemukan
                                </td>
                            </tr>`;
                        } else {

                            data.forEach((p, i) => {

                                const gambar = buildImageUrl(p.gambar);
                                const stok = safeInt(p.stok);
                                const min = safeInt(p.stok_minimal);
                                const isExpired = p.tanggal_kadaluarsa && p.tanggal_kadaluarsa <= getToday();
                                const satuanText = p.satuan_dasar ? ` / ${p.satuan_dasar}` : '';

                                html += `
                                <tr>
                                    <td>${i + 1}</td>

                                    <td>
                                        <img src="${gambar}" 
                                             onerror="this.onerror=null; this.src='${BASE_URL}assets/no-image.png'"
                                             style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                    </td>

                                    <td>
                                        <strong>${highlight(p.nama_produk || '-', keyword)}</strong><br>
                                        <small class="text-muted">ID: #${p.id_produk}</small>
                                    </td>

                                    <td>${highlight(p.nama_kategori || '-', keyword)}</td>
                                    <td>${highlight(p.nama_supplier || '-', keyword)}</td>

                                    <td>
                                        <div class="text-success fw-bold">
                                            Rp ${formatRupiah(p.harga_jual)} 
                                            <small class="text-muted">${satuanText}</small>
                                        </div>
                                        <small class="text-muted">Beli: Rp ${formatRupiah(p.harga_beli)}</small>
                                    </td>

                                    <td>
                                        <span class="badge ${stok <= min ? 'bg-danger' : 'bg-success'}">
                                            ${stok <= min ? '⚠ ' : ''} ${stok} ${p.satuan_dasar || ''}
                                        </span>
                                    </td>

                                    <td>
                                        ${
                                            isExpired 
                                            ? `<span class="badge bg-danger">Kadaluarsa</span>` 
                                            : `<span class="text-muted">${p.tanggal_kadaluarsa || '-'}</span>`
                                        }
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="${BASE_URL}produk/edit/${p.id_produk}" 
                                               class="btn btn-sm btn-outline-warning">✏️</a>

                                            <button onclick="hapus('${BASE_URL}produk/hapus/${p.id_produk}')" 
                                                    class="btn btn-outline-danger btn-sm">🗑️</button>
                                        </div>
                                    </td>
                                </tr>`;
                            });
                        }

                        tableBody.innerHTML = html;
                    })
                    .catch(err => console.error('Search error:', err));

            }, 350);
        });
    }

    // ======================
    // UPLOAD PREVIEW (OPTIONAL)
    // ======================
    const fileInput = byId('fileElem');
    const preview = byId('preview');

    if (fileInput && preview) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

});