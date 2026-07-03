document.addEventListener('DOMContentLoaded', function() {

    let cart = [];

    const supplier = document.getElementById('supplier');
    const produkList = document.getElementById('produkList');
    const searchInput = document.getElementById('searchProduk');
    const btnSimpan = document.getElementById('btnSimpan');

    // ======================
    // FORMAT RUPIAH
    // ======================
    function rupiah(angka) {
        angka = parseInt(angka) || 0;
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    // ======================
    // PARSE SATUAN dari list_satuan
    // ======================
    function parseSatuan(list_satuan) {
        if (!list_satuan) return [];
        
        let satuanArray = [];
        let items = list_satuan.split('||');
        
        for (let item of items) {
            let parts = item.split('::');
            if (parts.length >= 5) {
                satuanArray.push({
                    id: parseInt(parts[0]),
                    nama: parts[1],
                    konversi: parseInt(parts[2]),
                    harga: parseInt(parts[3]),
                    level: parseInt(parts[4])
                });
            }
        }
        return satuanArray;
    }

    // ======================
    // GET SATUAN TERBESAR (BOX)
    // ======================
    function getSatuanTerbesar(satuanList) {
        if (!satuanList || satuanList.length === 0) return null;
        return satuanList.reduce((max, s) => s.konversi > max.konversi ? s : max, satuanList[0]);
    }

    // ======================
    // GET SATUAN DASAR
    // ======================
    function getSatuanDasar(satuanList) {
        let dasar = satuanList.find(s => s.konversi === 1);
        if (!dasar && satuanList.length > 0) {
            dasar = satuanList.reduce((min, s) => s.konversi < min.konversi ? s : min, satuanList[0]);
        }
        return dasar;
    }
    
    // ======================
    // FORMAT INFO KONVERSI
    // ======================
    function formatInfoKonversi(satuanList) {
        if (!satuanList || satuanList.length <= 1) return '';
        
        let satuanDasar = getSatuanDasar(satuanList);
        if (!satuanDasar) return '';
        
        let infoParts = [];
        for (let satuan of satuanList) {
            if (satuan.konversi === 1) continue;
            infoParts.push(`1 ${satuan.nama} = ${satuan.konversi} ${satuanDasar.nama}`);
        }
        
        return infoParts.join(' | ');
    }

    // ======================
    // RENDER CART
    // ======================
    function renderCart() {
        let html = '';
        let total = 0;

        if (cart.length === 0) {
            html = '<tr><td colspan="4" class="text-center text-muted">Keranjang masih kosong</td><tr>';
        } else {
            cart.forEach((item, i) => {
                total += item.subtotal;
                
                let totalDalamDasar = item.qty * item.konversi;
                let satuanDasarNama = item.satuan_dasar_nama || 'unit';
                
                let konversiInfo = '';
                if (item.konversi > 1) {
                    konversiInfo = `<br><small class="text-info">↺ 1 ${item.satuan_nama} = ${item.konversi} ${satuanDasarNama}</small>`;
                    konversiInfo += `<br><small class="text-muted">(= ${totalDalamDasar} ${satuanDasarNama})</small>`;
                }

                html += `
                <tr>
                    <td style="min-width: 150px;">
                        <strong>${item.nama}</strong>
                        <br>
                        <small class="text-muted">${item.satuan_nama}</small>
                        ${konversiInfo}
                    </td>
                    <td class="text-center" style="width: 120px;">
                        <div class="d-flex align-items-center gap-1">
                            <button onclick="window.minusQty(${i})" class="btn btn-sm btn-danger">-</button>
                            <input type="number" min="1" value="${item.qty}"
                                onchange="window.ubahQty(${i}, this.value)"
                                style="width:60px; text-align:center"
                                class="form-control form-control-sm">
                            <button onclick="window.plusQty(${i})" class="btn btn-sm btn-success">+</button>
                        </div>
                    </td>
                    <td class="text-end">${rupiah(item.subtotal)}</td>
                    <td class="text-center">
                        <button onclick="window.hapus(${i})" class="btn btn-sm btn-danger">x</button>
                    </td>
                </tr>`;
            });
        }

        document.getElementById('cart').innerHTML = html;
        document.getElementById('total').innerText = total.toLocaleString('id-ID');
    }

    // ======================
    // TAMBAH PRODUK KE CART
    // ======================
    window.tambahProduk = function(produk, satuanId = null) {
        let satuanList = parseSatuan(produk.list_satuan);
        
        if (satuanList.length === 0) {
            Swal.fire('Error', 'Produk ini tidak memiliki satuan yang valid!', 'error');
            return;
        }

        let selectedSatuan = null;
        if (satuanId) {
            selectedSatuan = satuanList.find(s => s.id === satuanId);
        }
        if (!selectedSatuan) {
            selectedSatuan = getSatuanTerbesar(satuanList);
        }

        const harga = selectedSatuan.harga;
        const satuanDasar = getSatuanDasar(satuanList);
        let qty = 1;

        let existingIndex = cart.findIndex(item => 
            item.id === produk.id_produk && item.satuan_id === selectedSatuan.id
        );

        if (existingIndex !== -1) {
            cart[existingIndex].qty += qty;
            cart[existingIndex].subtotal = cart[existingIndex].qty * cart[existingIndex].harga;
        } else {
            cart.push({
                id: produk.id_produk,
                nama: produk.nama_produk,
                satuan_id: selectedSatuan.id,      // 🔥 INI PENTING!
                satuan_nama: selectedSatuan.nama,
                satuan_dasar_nama: satuanDasar?.nama || 'unit',
                harga: harga,
                konversi: selectedSatuan.konversi,
                qty: qty,
                subtotal: qty * harga
            });
        }

        animasi(produk.id_produk);
        renderCart();
        
        // Feedback
        Swal.fire({
            icon: 'success',
            title: 'Ditambahkan!',
            text: `${produk.nama_produk} - ${selectedSatuan.nama} berhasil ditambahkan`,
            timer: 1000,
            showConfirmButton: false
        });
    };

    // ======================
    // TAMBAH DENGAN MODAL PILIH SATUAN
    // ======================
    window.tambahProdukWithSatuan = function(produk) {
        let satuanList = parseSatuan(produk.list_satuan);
        
        if (satuanList.length === 0) {
            Swal.fire('Error', 'Produk tidak memiliki satuan!', 'error');
            return;
        }

        let satuanDasar = getSatuanDasar(satuanList);
        let satuanTerbesar = getSatuanTerbesar(satuanList);
        
        let inputOptions = {};
        satuanList.forEach(satuan => {
            let infoText = `${satuan.nama} - ${rupiah(satuan.harga)}`;
            if (satuan.konversi > 1) {
                infoText += ` (1 ${satuan.nama} = ${satuan.konversi} ${satuanDasar.nama})`;
            }
            inputOptions[satuan.id] = infoText;
        });

        Swal.fire({
            title: `Pilih Satuan untuk ${produk.nama_produk}`,
            html: `<div style="text-align:left;">
                    <p>📦 <strong>Info Konversi:</strong><br>
                    <small class="text-info">${formatInfoKonversi(satuanList)}</small></p>
                    <hr>
                    <p>Pilih satuan pembelian:</p>
                   </div>`,
            input: 'select',
            inputOptions: inputOptions,
            inputValue: satuanTerbesar ? satuanTerbesar.id.toString() : null,
            showCancelButton: true,
            confirmButtonText: 'Tambah ke Keranjang',
            cancelButtonText: 'Batal',
            preConfirm: (selectedId) => {
                if (!selectedId) {
                    Swal.showValidationMessage('Silakan pilih satuan');
                }
                return selectedId;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let satuanId = parseInt(result.value);
                window.tambahProduk(produk, satuanId);
            }
        });
    };

    // ======================
    // HAPUS ITEM CART
    // ======================
    window.hapus = function(i) {
        cart.splice(i, 1);
        renderCart();
    };

    // ======================
    // LOAD PRODUK
    // ======================
    if (supplier) {
        supplier.addEventListener('change', function() {
            let id = this.value;

            if (!id) {
                produkList.innerHTML = '';
                return;
            }

            produkList.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary"></div><br>Memuat produk...</div>';

            fetch(BASE_URL + 'pembelian/getProdukBySupplier?supplier_id=' + id)
                .then(res => res.json())
                .then(data => {
                    let html = '';

                    if (data.length === 0) {
                        html = `<div class="col-12"><div class="alert alert-warning">Tidak ada produk dari supplier ini</div></div>`;
                    }

                   data.forEach(p => {
    const stok = parseInt(p.stok) || 0;
    const minimal = parseInt(p.stok_minimal) || 0;
    const satuanList = parseSatuan(p.list_satuan);
    const satuanDasar = getSatuanDasar(satuanList);
    const satuanTerbesar = getSatuanTerbesar(satuanList);
    
    let hargaTampil = satuanTerbesar ? satuanTerbesar.harga : p.harga_beli;
    let satuanTampil = satuanTerbesar ? satuanTerbesar.nama : (satuanDasar?.nama || 'unit');
    
    let tabelKonversiHtml = '';
    if (satuanList.length > 1) {
        let barisTabel = '';
        for (let satuan of satuanList) {
            let isDasar = (satuan.konversi === 1);
            barisTabel += `
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 4px 0;"><strong>${satuan.nama}</strong>${isDasar ? ' <span class="badge bg-secondary" style="font-size:8px;">Dasar</span>' : ''}</td>
                    <td style="padding: 4px 0; text-align: right;">${rupiah(satuan.harga)}</td>
                    <td style="padding: 4px 0; text-align: center; font-size: 11px;">${!isDasar ? `↺ 1 ${satuan.nama} = ${satuan.konversi} ${satuanDasar.nama}` : 'Satuan terkecil'}</td>
                </tr>
            `;
        }
        tabelKonversiHtml = `
            <div class="mt-2 mb-2" style="background:#f8f9fa; border-radius:6px; padding:8px;">
                <small class="fw-bold text-muted">📊 Detail Satuan & Konversi:</small>
                <table style="width:100%; font-size:11px; margin-top:6px;">
                    <thead><tr><th style="text-align:left;">Satuan</th><th style="text-align:right;">Harga</th><th style="text-align:center;">Konversi</th></tr></thead>
                    <tbody>${barisTabel}</tbody>
                </table>
            </div>
        `;
    }
    
    let infoIsiPerUnit = '';
    if (satuanList.length > 1 && satuanDasar) {
        let box = satuanList.find(s => s.nama === 'Box');
        let strip = satuanList.find(s => s.nama === 'Strip');
        if (box && strip) {
            infoIsiPerUnit = `
                <div class="alert alert-secondary alert-sm py-1 px-2 mt-1 mb-2" style="font-size:10px; background:#e9ecef;">
                    <small>📦 <strong>Info Isi:</strong><br>• 1 Box = ${box.konversi} ${satuanDasar.nama}<br>• 1 Strip = ${strip.konversi} ${satuanDasar.nama}<br>• 1 Box = ${box.konversi / strip.konversi} Strip</small>
                </div>
            `;
        }
    }
    
    let infoKonversiHtml = '';
    if (satuanList.length > 1) {
        infoKonversiHtml = `<div class="alert alert-info alert-sm py-1 px-2 mt-1 mb-2" style="font-size:10px;"><small>📦 ${formatInfoKonversi(satuanList)}</small></div>`;
    }
    
    let satuanTersediaHtml = satuanList.length > 0 ? `<small class="text-muted d-block mt-1">📦 Tersedia: ${satuanList.map(s => s.nama).join(' · ')}</small>` : '';
    
    let warning = (minimal > 0 && stok <= minimal) ? '<span class="badge bg-danger mt-1">⚠️ Stok Minim</span>' : '';
    
    let hargaListHtml = satuanList.length > 1 ? `<div class="small text-muted mt-1">${satuanList.map(s => `${s.nama}: ${rupiah(s.harga)}`).join(' · ')}</div>` : '';
    
    html += `
    <div class="col-md-4 mb-3">
        <div class="card p-2 produk-card h-100 shadow-sm" id="card-${p.id_produk}" style="cursor:pointer">
            <img src="${p.gambar || 'https://via.placeholder.com/150'}" style="height:120px; object-fit:cover; border-radius:8px" onerror="this.src='https://via.placeholder.com/150'">
            <div class="mt-2">
                <b>${p.nama_produk}</b>
                ${tabelKonversiHtml}
                ${infoIsiPerUnit}
                ${infoKonversiHtml}
                <div class="text-success mt-1"><strong>${rupiah(hargaTampil)}</strong> <small class="text-muted">/${satuanTampil}</small></div>
                ${hargaListHtml}
                ${satuanTersediaHtml}
                <small class="text-muted">Stok: ${stok} ${satuanDasar?.nama || 'unit'}</small>
                ${warning}
                <button class="btn btn-sm btn-primary w-100 mt-2 btn-beli-produk" data-produk='${JSON.stringify(p)}'>🛒 Beli (Pilih Satuan)</button>
            </div>
        </div>
    </div>`;
});  // ← TUTUP data.forEach

produkList.innerHTML = html;  // ← INI HARUS DI LUAR
                    document.querySelectorAll('.btn-beli-produk').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            let produk = JSON.parse(this.dataset.produk);
                            window.tambahProdukWithSatuan(produk);
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    produkList.innerHTML = '<div class="col-12"><div class="alert alert-danger">Gagal memuat produk</div></div>';
                });
        });
    }

    // ======================
    // SEARCH
    // ======================
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase();
            document.querySelectorAll('.produk-card').forEach(card => {
                let text = card.innerText.toLowerCase();
                card.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    // ======================
    // ANIMASI
    // ======================
    function animasi(id) {
        let el = document.getElementById('card-' + id);
        if (!el) return;
        el.style.transform = 'scale(0.95)';
        setTimeout(() => el.style.transform = 'scale(1)', 150);
    }

    // ======================
    // SIMPAN PEMBELIAN - 🔥 UPDATE TERPENTING
    // ======================
    if (btnSimpan) {
        btnSimpan.addEventListener('click', function() {
            if (cart.length === 0) {
                Swal.fire('Warning', 'Keranjang Kosong', 'warning');
                return;
            }

            if (!supplier.value) {
                Swal.fire('Warning', 'Supplier Belum Dipilih', 'warning');
                return;
            }

            // 🔥 PASTIKAN DATA YANG DIKIRIM LENGKAP
            let itemsForSave = cart.map(item => ({
                id: item.id,
                satuan_id: item.satuan_id,      // 🔥 INI PENTING UNTUK DETAIL
                satuan_nama: item.satuan_nama,
                qty: item.qty,
                harga: item.harga,
                konversi: item.konversi,
                subtotal: item.subtotal
            }));

            console.log('Data yang dikirim:', itemsForSave); // Debug

            Swal.fire({
                title: 'Menyimpan Order...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            let formData = new URLSearchParams();
            formData.append('supplier', supplier.value);
            formData.append('cart', JSON.stringify(itemsForSave));
            
            if (typeof CSRF_NAME !== 'undefined' && typeof CSRF_HASH !== 'undefined') {
                formData.append(CSRF_NAME, CSRF_HASH);
            }

            fetch(BASE_URL + 'pembelian/simpan', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if (res.status === 'ok') {
                    Swal.fire('Berhasil!', 'Order disimpan', 'success')
                        .then(() => {
                            if (res.id) {
                                window.location.href = BASE_URL + 'pembelian/detail/' + res.id;
                            } else {
                                window.location.reload();
                            }
                        });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(err => {
                Swal.close();
                console.error(err);
                Swal.fire('Error', 'Gagal terhubung ke server', 'error');
            });
        });
    }

    // ======================
    // CART QUANTITY FUNCTIONS
    // ======================
    window.plusQty = function(i) {
        cart[i].qty++;
        cart[i].subtotal = cart[i].qty * cart[i].harga;
        renderCart();
    };

    window.minusQty = function(i) {
        if (cart[i].qty > 1) {
            cart[i].qty--;
            cart[i].subtotal = cart[i].qty * cart[i].harga;
            renderCart();
        }
    };

    window.ubahQty = function(i, val) {
        let qty = parseInt(val);
        if (qty < 1 || isNaN(qty)) qty = 1;
        cart[i].qty = qty;
        cart[i].subtotal = qty * cart[i].harga;
        renderCart();
    };

});