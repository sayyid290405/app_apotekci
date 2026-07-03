<div class="container-fluid">
    <style>
        .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 15px; }
        .upload-area { border: 2px dashed #dee2e6; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; background: #fafbfc; }
        .upload-area:hover { border-color: #0d6efd; background: #f8f9fa; }
        .preview-img { max-height: 60px; border-radius: 8px; }
        .produk-card { cursor: pointer; transition: 0.2s; border: 1px solid #eee; }
        .produk-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .produk-grid { max-height: 380px; overflow-y: auto; }
        .cart-table th { background: #f8f9fa; font-size: 12px; }
        .cart-table td { vertical-align: middle; }
        .btn-pecahan { margin: 2px; font-size: 12px; padding: 4px 8px; }
        .sticky-cart { position: sticky; top: 20px; }
        .aturan-form { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-top: 10px; }
        .aturan-form label { font-size: 11px; font-weight: bold; }
        .total-kebutuhan { font-size: 11px; color: #0c63e4; font-weight: bold; }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <h4 class="mb-3"><i class="fas fa-prescription-bottle-alt text-primary me-2"></i>Resep + Kasir Apotek</h4>

    <div class="row g-3">
        
        <!-- KIRI: Data Pasien + Produk -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="fw-bold small">👤 Nama Pasien <span class="text-danger">*</span></label>
                            <input type="text" id="pasien" class="form-control" placeholder="Nama Pasien">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small">👨‍⚕️ Nama Dokter</label>
                            <input type="text" id="dokter" class="form-control" placeholder="Nama Dokter">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold small">📷 Foto Resep <span class="text-danger">*</span></label>
                            <div class="upload-area" onclick="document.getElementById('gambarResep').click()">
                                <i class="fas fa-cloud-upload-alt"></i> <span class="small">Upload</span>
                                <div id="previewResepContainer" style="display:none;">
                                    <img id="previewResep" class="preview-img mt-1">
                                </div>
                            </div>
                            <input type="file" id="gambarResep" class="d-none" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body py-2">
                    <input type="text" id="search" class="form-control" placeholder="🔍 Cari obat...">
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="row produk-grid g-2">
                        <?php foreach($produk as $p): ?>
                        <div class="col-md-4 col-sm-6 produk-item">
                            <div class="card produk-card p-2">
                                <img src="<?= !empty($p->gambar) ? $p->gambar : 'https://placehold.co/150' ?>" 
                                     class="rounded mb-2" style="height:70px; object-fit:cover; width:100%;">
                                <h6 class="fw-bold small text-truncate"><?= htmlspecialchars($p->nama_produk) ?></h6>
                                <small class="text-muted">Stok: <?= $p->stok ?></small>
                                <select class="form-select form-select-sm mt-1 satuan-select">
                                    <?php foreach($p->satuan as $s): ?>
                                    <option value="<?= $s->konversi ?>" data-harga="<?= $s->harga ?>" data-satuan="<?= $s->nama_satuan ?>">
                                        <?= $s->nama_satuan ?> - Rp <?= number_format($s->harga,0,',','.') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-success mt-2 btn-tambah" 
                                        data-id="<?= $p->id_produk ?>"
                                        data-nama="<?= htmlspecialchars($p->nama_produk) ?>">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KANAN: Keranjang + Aturan Pakai + Pembayaran -->
        <div class="col-lg-6">
            <div class="sticky-cart">
                
                <!-- Keranjang -->
                <div class="card">
                    <div class="card-header bg-white fw-bold">
                        <i class="fas fa-shopping-cart me-2"></i> Keranjang Belanja
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm cart-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Obat</th>
                                        <th width="15%">Qty</th>
                                        <th width="25%">Harga</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <tr><td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Aturan Pakai (FORM DI BAWAH KERANJANG) -->
                <div class="card mt-3" id="aturanCard" style="display:none;">
                    <div class="card-header bg-white fw-bold">
                        <i class="fas fa-pills me-2"></i> Aturan Pakai Obat
                    </div>
                    <div class="card-body" id="aturanBody"></div>
                </div>
                
                <!-- Ringkasan & Pembayaran -->
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal Obat</span>
                            <strong id="subtotalObat">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Resep/Jasa</span>
                            <strong>Rp 20.000</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><input type="checkbox" id="ppn"> PPN 11%</span>
                            <span id="ppnRp">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Diskon (%)</span>
                            <input type="number" id="diskon" class="form-control form-control-sm" style="width:70px" value="0">
                        </div>
                        <hr>
                        <h5 class="text-primary d-flex justify-content-between">
                            <span>TOTAL</span>
                            <span id="total">Rp 0</span>
                        </h5>
                        
                        <hr>
                        
                        <select id="metode" class="form-select mb-3">
                            <option value="tunai">💵 Tunai (Cash)</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                        </select>
                        
                        <div id="cashSection">
                            <label class="small fw-bold">Uang Bayar</label>
                            <input type="number" id="bayar" class="form-control mb-2" placeholder="0">
                            <div class="d-flex flex-wrap">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-pecahan" data-nominal="10000">10rb</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-pecahan" data-nominal="20000">20rb</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-pecahan" data-nominal="50000">50rb</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-pecahan" data-nominal="100000">100rb</button>
                            </div>
                        </div>
                        
                        <div id="transferSection" style="display:none;">
                            <label class="small fw-bold">Upload Bukti Transfer</label>
                            <input type="file" id="buktiTransfer" class="form-control" accept="image/*">
                            <div id="previewBuktiContainer" class="text-center mt-1" style="display:none;">
                                <img id="previewBukti" style="max-height:50px;">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <span class="fw-bold">Kembalian</span>
                            <h5 id="kembalian" class="text-success">Rp 0</h5>
                        </div>
                        
                        <button id="btnSimpan" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> SIMPAN TRANSAKSI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="formSubmit" method="POST" enctype="multipart/form-data" action="<?= base_url('resep/simpan') ?>" style="display:none;">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="produk" id="inputProduk">
        <input type="hidden" name="subtotal" id="inputSubtotal">
        <input type="hidden" name="diskon" id="inputDiskon">
        <input type="hidden" name="ppn" id="inputPpn">
        <input type="hidden" name="total_harga" id="inputTotal">
        <input type="hidden" name="bayar" id="inputBayar">
        <input type="hidden" name="metode_bayar" id="inputMetode">
        <input type="hidden" name="nama_pasien" id="inputPasien">
        <input type="hidden" name="nama_dokter" id="inputDokter">
    </form>

<script>
let cart = [];
let fileResep = null;
const BIAYA_RESEP = 20000;

// Upload Resep
document.getElementById('gambarResep').onchange = function(e) {
    if(e.target.files.length) {
        fileResep = e.target.files[0];
        if(fileResep.size > 2*1024*1024) {
            Swal.fire('Error', 'Max 2MB', 'error');
            this.value = '';
            fileResep = null;
            return;
        }
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewResep').src = ev.target.result;
            document.getElementById('previewResepContainer').style.display = 'block';
        };
        reader.readAsDataURL(fileResep);
    }
};

// Preview Bukti Transfer
document.getElementById('buktiTransfer').onchange = function(e) {
    if(e.target.files.length) {
        let reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewBukti').src = ev.target.result;
            document.getElementById('previewBuktiContainer').style.display = 'block';
        };
        reader.readAsDataURL(e.target.files[0]);
    }
};

// Toggle Metode
document.getElementById('metode').onchange = function() {
    let isTransfer = this.value === 'transfer';
    document.getElementById('cashSection').style.display = isTransfer ? 'none' : 'block';
    document.getElementById('transferSection').style.display = isTransfer ? 'block' : 'none';
    if(isTransfer) {
        document.getElementById('bayar').readOnly = true;
        document.getElementById('bayar').value = '';
    } else {
        document.getElementById('bayar').readOnly = false;
    }
    hitungKembalian();
};

// Pecahan uang
document.querySelectorAll('.btn-pecahan').forEach(btn => {
    btn.onclick = () => {
        document.getElementById('bayar').value = btn.dataset.nominal;
        hitungKembalian();
    };
});

// Tambah Produk
document.querySelectorAll('.btn-tambah').forEach(btn => {
    btn.onclick = function() {
        let card = this.closest('.produk-card');
        let select = card.querySelector('.satuan-select');
        let id = this.dataset.id;
        let nama = this.dataset.nama;
        let harga = parseInt(select.options[select.selectedIndex].dataset.harga);
        let satuan = select.options[select.selectedIndex].dataset.satuan;
        
        let existing = cart.find(i => i.id == id && i.satuan == satuan);
        if(existing) {
            Swal.fire('Info', 'Obat sudah ada di keranjang', 'info');
            return;
        }
        
        cart.push({ 
            id, nama, harga, satuan, 
            sehari: 3,      // default 3x sehari
            jangka: 7       // default 7 hari (1 minggu)
        });
        renderCart();
        
        let img = card.querySelector('img');
        if(img) {
            let clone = img.cloneNode(true);
            clone.style.position = 'fixed';
            clone.style.width = '50px';
            clone.style.left = img.getBoundingClientRect().left + 'px';
            clone.style.top = img.getBoundingClientRect().top + 'px';
            clone.style.zIndex = '9999';
            clone.style.transition = 'all 0.4s';
            document.body.appendChild(clone);
            let target = document.querySelector('#total');
            if(target) {
                let targetRect = target.getBoundingClientRect();
                setTimeout(() => {
                    clone.style.transform = `translate(${targetRect.left - img.getBoundingClientRect().left}px, ${targetRect.top - img.getBoundingClientRect().top}px) scale(0.1)`;
                    clone.style.opacity = '0';
                }, 10);
            }
            setTimeout(() => clone.remove(), 400);
        }
        
        Swal.fire({ icon: 'success', title: 'Ditambahkan!', text: nama, timer: 800, showConfirmButton: false });
    };
});

// Hitung Qty otomatis dari sehari * jangka
function hitungQtyOtomatis(sehari, jangka) {
    return (parseInt(sehari) || 0) * (parseInt(jangka) || 0);
}

// Render Keranjang & Aturan
function renderCart() {
    let html = '';
    let subtotalObat = 0;
    
    if(cart.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-muted py-3">Keranjang kosong</td><\/tr>';
        document.getElementById('aturanCard').style.display = 'none';
    } else {
        cart.forEach((item, index) => {
            let qty = hitungQtyOtomatis(item.sehari, item.jangka);
            let total = qty * item.harga;
            subtotalObat += total;
            
            html += `
            <tr>
                <td>
                    <div class="fw-bold small">${item.nama}</div>
                    <div class="text-muted" style="font-size:10px">${item.satuan}</div>
                  </td>
                <td>
                    <div class="fw-bold text-primary">${qty}</div>
                  </td>
                <td class="text-end">Rp ${rupiah(total)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="hapusItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                  </td>
              </tr>
            `;
        });
    }
    
    document.getElementById('cartBody').innerHTML = html;
    
    // Render form aturan pakai
    let aturanHtml = '';
    cart.forEach((item, index) => {
        let qty = hitungQtyOtomatis(item.sehari, item.jangka);
        aturanHtml += `
            <div class="aturan-form mb-2">
                <div class="fw-bold small mb-2">${item.nama}</div>
                <div class="row g-2">
                    <div class="col-5">
                        <label>Sehari (x)</label>
                        <input type="number" class="form-control form-control-sm" id="sehari_${index}" 
                               value="${item.sehari}" min="1"
                               onchange="updateAturan(${index}, 'sehari', this.value)">
                    </div>
                    <div class="col-5">
                        <label>Jangka waktu (hari)</label>
                        <input type="number" class="form-control form-control-sm" id="jangka_${index}" 
                               value="${item.jangka}" min="1"
                               onchange="updateAturan(${index}, 'jangka', this.value)">
                    </div>
                    <div class="col-2 text-end">
                        <label>&nbsp;</label>
                        <div class="total-kebutuhan mt-1">= ${qty}</div>
                    </div>
                </div>
                <div class="small text-muted mt-1">
                    <i class="fas fa-calculator"></i> ${item.sehari} x ${item.jangka} hari = ${qty} ${item.satuan}
                </div>
            </div>
            <hr class="my-2">
        `;
    });
    
    if(cart.length > 0) {
        document.getElementById('aturanCard').style.display = 'block';
        document.getElementById('aturanBody').innerHTML = aturanHtml;
    } else {
        document.getElementById('aturanCard').style.display = 'none';
    }
    
    hitungTotal(subtotalObat);
}

function updateAturan(index, field, value) {
    if(cart[index]) {
        if(field === 'sehari') cart[index].sehari = parseInt(value) || 1;
        if(field === 'jangka') cart[index].jangka = parseInt(value) || 1;
        renderCart();
    }
}

function hapusItem(index) {
    Swal.fire({
        title: 'Hapus Item?',
        text: `Hapus ${cart[index]?.nama}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if(result.isConfirmed) {
            cart.splice(index, 1);
            renderCart();
            Swal.fire('Terhapus', '', 'success');
        }
    });
}

function hitungTotal(subtotalObat) {
    let diskonPersen = parseFloat(document.getElementById('diskon').value) || 0;
    let pakaiPPN = document.getElementById('ppn').checked;
    
    let totalSebelumDiskon = subtotalObat + BIAYA_RESEP;
    let nilaiDiskon = totalSebelumDiskon * (diskonPersen / 100);
    let setelahDiskon = totalSebelumDiskon - nilaiDiskon;
    let nilaiPPN = pakaiPPN ? setelahDiskon * 0.11 : 0;
    let totalFinal = setelahDiskon + nilaiPPN;
    
    document.getElementById('subtotalObat').innerText = 'Rp ' + rupiah(subtotalObat);
    document.getElementById('ppnRp').innerText = 'Rp ' + rupiah(nilaiPPN);
    document.getElementById('total').innerText = 'Rp ' + rupiah(totalFinal);
    
    document.getElementById('inputSubtotal').value = Math.round(subtotalObat);
    document.getElementById('inputDiskon').value = Math.round(nilaiDiskon);
    document.getElementById('inputPpn').value = Math.round(nilaiPPN);
    document.getElementById('inputTotal').value = Math.round(totalFinal);
    
    hitungKembalian();
}

function hitungKembalian() {
    if(document.getElementById('metode').value === 'transfer') {
        document.getElementById('kembalian').innerText = 'Rp 0';
        return;
    }
    let total = parseFloat(document.getElementById('inputTotal').value) || 0;
    let bayar = parseFloat(document.getElementById('bayar').value) || 0;
    let kembali = bayar - total;
    let kembalianEl = document.getElementById('kembalian');
    kembalianEl.innerText = 'Rp ' + rupiah(kembali);
    kembalianEl.style.color = kembali < 0 ? 'red' : 'green';
}

// Search produk
document.getElementById('search').oninput = function() {
    let keyword = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(item => {
        let nama = item.querySelector('h6')?.innerText.toLowerCase() || '';
        item.style.display = nama.includes(keyword) ? '' : 'none';
    });
};

// Event listeners
document.getElementById('diskon').oninput = () => {
    let subtotal = cart.reduce((sum, i) => sum + (hitungQtyOtomatis(i.sehari, i.jangka) * i.harga), 0);
    hitungTotal(subtotal);
};
document.getElementById('ppn').onchange = () => {
    let subtotal = cart.reduce((sum, i) => sum + (hitungQtyOtomatis(i.sehari, i.jangka) * i.harga), 0);
    hitungTotal(subtotal);
};
document.getElementById('bayar').oninput = () => hitungKembalian();

// Simpan Transaksi
document.getElementById('btnSimpan').onclick = async function() {
    if(cart.length === 0) {
        Swal.fire('Perhatian', 'Keranjang kosong!', 'warning');
        return;
    }
    
    let namaPasien = document.getElementById('pasien').value.trim();
    if(!namaPasien) {
        Swal.fire('Perhatian', 'Nama pasien harus diisi!', 'warning');
        return;
    }
    
    if(!fileResep) {
        Swal.fire('Perhatian', 'Foto resep wajib diupload!', 'warning');
        return;
    }
    
    let total = parseFloat(document.getElementById('inputTotal').value) || 0;
    let metode = document.getElementById('metode').value;
    let bayar = parseFloat(document.getElementById('bayar').value) || 0;
    
    if(metode === 'tunai' && bayar < total) {
        Swal.fire('Pembayaran Kurang', `Total: Rp ${rupiah(total)}`, 'error');
        return;
    }
    
    if(metode === 'transfer') {
        let buktiFile = document.getElementById('buktiTransfer').files[0];
        if(!buktiFile) {
            Swal.fire('Perhatian', 'Upload bukti transfer!', 'warning');
            return;
        }
    }
    
    let confirm = await Swal.fire({
        title: 'Konfirmasi',
        html: `Total: <strong>Rp ${rupiah(total)}</strong><br>Metode: <strong>${metode === 'tunai' ? 'Tunai' : 'Transfer'}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan'
    });
    if(!confirm.isConfirmed) return;
    
    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    
    let produkWithQty = cart.map(item => ({
        id: item.id, 
        nama: item.nama, 
        harga: item.harga, 
        satuan: item.satuan,
        sehari: item.sehari,
        jangka: item.jangka,
        qty: hitungQtyOtomatis(item.sehari, item.jangka)
    }));
    
    document.getElementById('inputProduk').value = JSON.stringify(produkWithQty);
    document.getElementById('inputMetode').value = metode;
    document.getElementById('inputPasien').value = namaPasien;
    document.getElementById('inputDokter').value = document.getElementById('dokter').value || '';
    document.getElementById('inputBayar').value = bayar;
    
    let form = document.getElementById('formSubmit');
    let formData = new FormData(form);
    formData.append('gambar_resep', fileResep);
    formData.append('biaya_resep', BIAYA_RESEP);
    
    if(metode === 'transfer') {
        let buktiTransfer = document.getElementById('buktiTransfer').files[0];
        if(buktiTransfer) formData.append('bukti_pembayaran', buktiTransfer);
    }
    
    try {
        let response = await fetch(form.action, { method: "POST", body: formData });
        let res = await response.json();
        if(res.status === "success") {
            await Swal.fire({ icon: 'success', title: 'Berhasil!', timer: 1500, showConfirmButton: false });
            window.location.href = res.redirect;
        } else {
            Swal.fire('Gagal!', res.message, 'error');
        }
    } catch(err) {
        Swal.fire('Error!', 'Gagal terhubung ke server', 'error');
    }
};

function rupiah(n) {
    if(isNaN(n) || n < 0) return '-' + new Intl.NumberFormat('id-ID').format(Math.abs(n));
    return new Intl.NumberFormat('id-ID').format(n);
}

document.getElementById('metode').dispatchEvent(new Event('change'));
</script>