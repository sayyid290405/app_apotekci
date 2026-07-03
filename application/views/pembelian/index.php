
<div class="container-fluid px-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3"><i class="fas fa-shopping-basket text-primary me-2"></i>Order Obat ke Supplier</h4>

            <div class="mb-3">
                <label class="fw-bold">Supplier</label>
                <select id="supplier" class="form-select">
                    <option value="">-- Pilih Supplier --</option>
                    <?php foreach($supplier as $s): ?>
                        <option value="<?= $s->id_supplier ?>"><?= $s->nama_supplier ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <input type="text" id="searchProduk" class="form-control" placeholder="Cari obat...">
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="row" id="produkList">
                        <div class="col-12"><div class="alert alert-info">Pilih supplier terlebih dahulu</div></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-success border-top">
                        <div class="card-body">
                            <h5><i class="fas fa-shopping-cart me-1"></i> Keranjang</h5>
                            <table class="table table-sm">
                                <thead><tr><th>Produk/Satuan</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                                <tbody id="cart"><tr><td colspan="4" class="text-center text-muted">Kosong</td></tr></tbody>
                            </table>
                            <h5 class="mt-2">Total: Rp <span id="total">0</span></h5>
                            <button id="btnSimpan" class="btn btn-success w-100">Simpan Order</button>
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url(); ?>';
const CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
const CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

// ========== SEMUA KODE JS DI SINI ==========
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    const supplier = document.getElementById('supplier');
    const produkList = document.getElementById('produkList');
    const searchInput = document.getElementById('searchProduk');
    const btnSimpan = document.getElementById('btnSimpan');

    function rupiah(angka) {
        return 'Rp ' + (parseInt(angka) || 0).toLocaleString('id-ID');
    }

    function parseSatuan(list) {
        if (!list) return [];
        let result = [];
        let items = list.split('||');
        for (let item of items) {
            let parts = item.split('::');
            if (parts.length >= 5) {
                result.push({
                    id: parseInt(parts[0]),
                    nama: parts[1],
                    konversi: parseInt(parts[2]),
                    harga: parseInt(parts[3]),
                    level: parseInt(parts[4])
                });
            }
        }
        return result.sort((a,b) => a.konversi - b.konversi);
    }

    function getSatuanDasar(list) {
        return list.find(s => s.konversi === 1) || list[0];
    }

    function getSatuanBesar(list) {
        return list.reduce((max, s) => s.konversi > max.konversi ? s : max, list[0]);
    }

    function renderCart() {
        let html = '', total = 0;
        cart.forEach((item, i) => {
            total += item.subtotal;
            html += `<tr>
                <td><strong>${item.nama}</strong><br><small>${item.satuan_nama}</small><br><small class="text-info">↺ 1 = ${item.konversi} ${item.satuan_dasar_nama}</small></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="window.minusQty(${i})">-</button>
                    <input type="number" min="1" value="${item.qty}" style="width:50px;text-align:center" onchange="window.ubahQty(${i}, this.value)">
                    <button class="btn btn-sm btn-success" onclick="window.plusQty(${i})">+</button>
                    <br><small>${item.qty * item.konversi} ${item.satuan_dasar_nama}</small>
                </td>
                <td class="text-end">${rupiah(item.subtotal)}</td>
                <td><button class="btn btn-sm btn-outline-danger" onclick="window.hapus(${i})"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        });
        if(cart.length === 0) html = '<tr><td colspan="4" class="text-center text-muted">Keranjang masih kosong</td></tr>';
        document.getElementById('cart').innerHTML = html;
        document.getElementById('total').innerText = total.toLocaleString();
    }

    window.hapus = function(i) { cart.splice(i,1); renderCart(); };
    window.plusQty = function(i) { cart[i].qty++; cart[i].subtotal = cart[i].qty * cart[i].harga; renderCart(); };
    window.minusQty = function(i) { if(cart[i].qty>1){ cart[i].qty--; cart[i].subtotal = cart[i].qty * cart[i].harga; renderCart(); } };
    window.ubahQty = function(i, val) { let qty = parseInt(val) || 1; cart[i].qty = qty; cart[i].subtotal = qty * cart[i].harga; renderCart(); };

    window.tambahProduk = function(produk, satuanId) {
        let satuanList = parseSatuan(produk.list_satuan);
        if(!satuanList.length) return;
        let selected = satuanList.find(s => s.id === satuanId) || getSatuanBesar(satuanList);
        let dasar = getSatuanDasar(satuanList);
        let existing = cart.findIndex(item => item.id === produk.id_produk && item.satuan_id === selected.id);
        
        if(existing !== -1) {
            cart[existing].qty++;
            cart[existing].subtotal = cart[existing].qty * cart[existing].harga;
        } else {
            cart.push({
                id: produk.id_produk,
                nama: produk.nama_produk,
                satuan_id: selected.id,
                satuan_nama: selected.nama,
                satuan_dasar_nama: dasar.nama,
                harga: selected.harga,
                konversi: selected.konversi,
                qty: 1,
                subtotal: selected.harga
            });
        }
        renderCart();
    };

    window.tambahProdukWithSatuan = function(produk) {
        let satuanList = parseSatuan(produk.list_satuan);
        if(!satuanList.length) { Swal.fire('Error', 'Data satuan tidak ditemukan!', 'error'); return; }
        let dasar = getSatuanDasar(satuanList);
        let options = {};
        satuanList.forEach(s => { options[s.id] = `${s.nama} - ${rupiah(s.harga)} (1 = ${s.konversi} ${dasar.nama})`; });
        
        Swal.fire({
            title: `Pilih Satuan: ${produk.nama_produk}`,
            input: 'select',
            inputOptions: options,
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            preConfirm: (val) => { if(!val) Swal.showValidationMessage('Pilih satuan'); return val; }
        }).then(res => {
            if(res.isConfirmed) window.tambahProduk(produk, parseInt(res.value));
        });
    };

    // LOAD PRODUK
    supplier.addEventListener('change', function() {
        let id = this.value;
        if(!id) { produkList.innerHTML = '<div class="col-12"><div class="alert alert-info">Pilih supplier</div></div>'; return; }
        
        produkList.innerHTML = '<div class="col-12 text-center"><div class="spinner-border"></div><br>Memuat...</div>';
        
        fetch(BASE_URL + 'pembelian/getProdukBySupplier?supplier_id=' + id)
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(p => {
                    if(!p.list_satuan) return;
                    let satuanList = parseSatuan(p.list_satuan);
                    let dasar = getSatuanDasar(satuanList);
                    let besar = getSatuanBesar(satuanList);
                    
                    // BUAT TABEL KONVERSI
                    let tabelRows = '';
                    for(let s of satuanList) {
                        let isDasar = (s.konversi === 1);
                        tabelRows += `<tr>
                        </tr>`;
                    }
                    
                    let tabelKonversi = satuanList.length > 1 ? `
                        <div class="tabel-konversi">
                            <table><thead><tr><th>Satuan</th><th>Harga</th><th>Konversi</th></tr></thead><tbody>${tabelRows}</tbody></table>
                        </div>
                    ` : '';
                    
                    // INFO ISI
                    let box = satuanList.find(s => s.nama === 'Box');
                    let strip = satuanList.find(s => s.nama === 'Strip');
                    let infoIsi = '';
                    if(box && strip && dasar) {
                        infoIsi = `<div class="info-isi"><i class="fas fa-boxes"></i> <strong>Info Isi:</strong><br>• 1 Box = ${box.konversi} ${dasar.nama}<br>• 1 Strip = ${strip.konversi} ${dasar.nama}<br>• 1 Box = ${box.konversi / strip.konversi} Strip</div>`;
                    } else if(box && dasar) {
                        infoIsi = `<div class="info-isi"><i class="fas fa-boxes"></i> <strong>Info Isi:</strong><br>• 1 Box = ${box.konversi} ${dasar.nama}</div>`;
                    }
                    
                    html += `
                        <div class="col-md-4 mb-3">
                            <div class="card produk-card p-2 h-100" id="card-${p.id_produk}">
                                <img src="${p.gambar || 'https://via.placeholder.com/150'}" style="height:100px;object-fit:cover;border-radius:8px">
                                <div class="mt-2">
                                    <strong>${p.nama_produk}</strong>
                                    ${tabelKonversi}
                                    ${infoIsi}
                                    <div class="text-center mt-2" style="font-size:16px;font-weight:bold;color:#28a745;">
                                        ${rupiah(besar.harga)} <small class="text-muted">/${besar.nama}</small>
                                    </div>
                                    <div class="text-center small text-muted">Stok: ${p.stok} ${dasar.nama}</div>
                                    <button class="btn btn-sm btn-primary w-100 mt-2" onclick='window.tambahProdukWithSatuan(${JSON.stringify(p).replace(/'/g, "&#39;")})'>
                                        🛒 Beli
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                produkList.innerHTML = html || '<div class="col-12"><div class="alert alert-warning">Tidak ada produk</div></div>';
            })
            .catch(err => { console.error(err); produkList.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error</div></div>'; });
    });

    searchInput.addEventListener('keyup', function() {
        let kw = this.value.toLowerCase();
        document.querySelectorAll('.produk-card').forEach(card => {
            let text = card.innerText.toLowerCase();
            card.closest('.col-md-4').style.display = text.includes(kw) ? '' : 'none';
        });
    });

    btnSimpan.addEventListener('click', function() {
        if(cart.length === 0) { Swal.fire('Warning', 'Keranjang kosong!', 'warning'); return; }
        if(!supplier.value) { Swal.fire('Warning', 'Pilih supplier!', 'warning'); return; }
        
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        let itemsForSave = cart.map(item => ({
            id: item.id, satuan_id: item.satuan_id, qty: item.qty, harga: item.harga, konversi: item.konversi, subtotal: item.subtotal
        }));
        
        let formData = new URLSearchParams();
        formData.append('supplier', supplier.value);
        formData.append('cart', JSON.stringify(itemsForSave));
        formData.append(CSRF_NAME, CSRF_HASH);
        
        fetch(BASE_URL + 'pembelian/simpan', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                Swal.close();
                if(res.status === 'ok') {
                    Swal.fire('Berhasil!', 'Order disimpan', 'success').then(() => window.location.href = BASE_URL + 'pembelian/detail/' + res.id);
                } else {
                    Swal.fire('Gagal', res.message || 'Error', 'error');
                }
            })
            .catch(() => { Swal.close(); Swal.fire('Error', 'Gagal menyimpan', 'error'); });
    });
});
</script>
</body>
</html>