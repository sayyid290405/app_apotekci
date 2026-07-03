<div class="container-fluid">
    <style>
        .fly-clone { position: fixed; z-index: 9999; pointer-events: none; will-change: transform, opacity; transition: transform .6s cubic-bezier(.2,.8,.2,1), opacity .6s ease; }
        .cart-bump { animation: cartBump .35s ease; }
        @keyframes cartBump { 0% { transform: scale(1); } 40% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .cart-item-added { background-color: #d1e7dd; transition: background-color 0.5s; }
        .preview-img { width: 100%; max-height: 150px; object-fit: cover; border-radius: 10px; border: 2px solid #ddd; margin-top: 10px; }
        .preview-img:hover { border-color: #0d6efd; }
        
        /* 🔥 Tambahan Style Agar Keranjang Menjadi Sticky/Fixed saat Di-scroll */
        .sticky-cart-panel {
            position: -webkit-sticky;
            position: sticky;
            top: 20px; /* Jarak batas atas dari dokumen saat mulai melayang */
            z-index: 10;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <h4 class="mb-3">🛒 KASIR APOTEK</h4>

    <div class="row">
        <div class="col-md-8">
            <input type="text" id="search" class="form-control mb-3" placeholder="🔍 Cari produk...">
            <div class="row">
                <?php foreach($produk as $p): ?>
                <div class="col-md-4 produk-item">
                    <div class="card p-3 mb-3 text-center shadow-sm border-0">
                        <img src="<?= !empty($p->gambar) ? $p->gambar : 'https://via.placeholder.com/150' ?>" 
                             class="img-fluid mb-2" style="height:120px; object-fit:cover; border-radius:10px;">
                        <h6 class="fw-bold"><?= htmlspecialchars($p->nama_produk) ?></h6>
                        <small class="text-muted">Stok: <?= $p->stok ?> <?= $p->satuan_dasar ?></small>

                        <select class="form-control form-control-sm mt-2 satuan-select">
                            <?php foreach($p->satuan as $s): ?>
                                <option value="<?= $s->konversi ?>" data-harga="<?= $s->harga ?>" data-satuan="<?= $s->nama_satuan ?>">
                                    <?= $s->nama_satuan ?> (Rp <?= number_format($s->harga,0,',','.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-success mt-2 w-100" onclick="tambahMulti(this, <?= $p->id_produk ?>, '<?= addslashes($p->nama_produk) ?>'); flyToCart(this)">
                            + Tambah
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 sticky-cart-panel">
                <h5 class="mb-3">🧾 Keranjang</h5>
                <div style="max-height:350px; overflow-y:auto; overflow-x:hidden;">
                    <div id="cart">
                        <p class="text-center text-muted py-3">Keranjang kosong</p>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <strong id="subtotal">Rp 0</strong>
                </div>

                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <label class="mb-0"><input type="checkbox" id="ppn"> PPN (11%)</label>
                    <span id="ppnRp" class="text-muted small">Rp 0</span>
                </div>

                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <label class="mb-0">Diskon (%)</label>
                    <input type="number" id="diskon" class="form-control form-control-sm w-25" value="0" min="0">
                </div>

                <h4 class="mt-3 text-primary d-flex justify-content-between">
                    <span>Total:</span>
                    <span id="total">Rp 0</span>
                </h4>

                <hr>

                <div class="mb-3 p-2 bg-light rounded border">
                    <label class="fw-bold small mb-1">METODE BAYAR</label>
                    <select id="metode_pembayaran_select" class="form-select form-select-sm mb-2" onchange="toggleMetode()">
                        <option value="tunai">💵 Tunai (Cash)</option>
                        <option value="transfer">🏦 Transfer Bank (Upload Bukti)</option>
                    </select>

                    <div id="cash-section">
                        <label class="fw-bold small mb-1">UANG BAYAR</label>
                        <input type="number" id="bayar" class="form-control form-control-lg fw-bold text-success" placeholder="0">
                        <div class="mt-2 d-flex gap-1">
                            <button onclick="setBayar(10000)" type="button" class="btn btn-outline-secondary btn-sm">10k</button>
                            <button onclick="setBayar(50000)" type="button" class="btn btn-outline-secondary btn-sm">50k</button>
                            <button onclick="setBayar(100000)" type="button" class="btn btn-outline-secondary btn-sm">100k</button>
                        </div>
                    </div>

                    <div id="transfer-section" style="display:none;">
                        <label class="fw-bold small mb-1">UPLOAD BUKTI PEMBAYARAN</label>
                        <input type="file" id="bukti_tampilan" class="form-control form-control-sm" accept="image/*" onchange="syncAndPreviewImage(this)">
                        <div id="preview-container" class="text-center" style="display:none;">
                            <img id="preview-img" class="preview-img" src="" alt="Preview Bukti">
                            <p class="small text-success mt-1">✓ Bukti pembayaran terupload</p>
                        </div>
                        <small class="text-muted">* Upload screenshot/foto bukti transfer bank</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-muted">Kembalian:</span>
                    <h5 class="mb-0 fw-bold" id="kembalian">Rp 0</h5>
                </div>

                <button type="button" class="btn btn-primary btn-lg w-100 shadow" onclick="checkout()">
                    🚀 SELESAIKAN BAYAR
                </button>
            </div>
        </div>
    </div>
</div>

<form id="formCheckout" action="<?= base_url('kasir/simpan') ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    
    <input type="hidden" name="produk" id="produkInput">
    <input type="hidden" name="subtotal" id="subtotalInput">
    <input type="hidden" name="diskon" id="diskonInput">
    <input type="hidden" name="ppn" id="ppnInput">
    <input type="hidden" name="total" id="totalInput">
    <input type="hidden" name="bayar" id="bayarInput">
    <input type="hidden" name="metode_pembayaran" id="metodeInput" value="tunai">
    <input type="hidden" name="resep_id" id="resepInput">
    
    <div id="transfer-input-container" style="display:none;">
        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*">
    </div>
</form>

<script>
let cart = [];

function tambahMulti(el, id, nama) {
    let select = el.parentElement.querySelector('.satuan-select');
    let harga = parseInt(select.options[select.selectedIndex].dataset.harga) || 0;
    let konversi = parseInt(select.value) || 1;
    let satuan = select.options[select.selectedIndex].dataset.satuan || '-';
    let key = id + '_' + konversi;

    let item = cart.find(i => (i.id + '_' + i.konversi) === key);
    if(item) {
        item.qty++;
    } else {
        cart.push({ id, nama, harga, qty: 1, satuan, konversi });
    }
    playBeep();
    render(key);
}

function toggleMetode() {
    const metode = document.getElementById('metode_pembayaran_select').value;
    const cashSection = document.getElementById('cash-section');
    const transferSection = document.getElementById('transfer-section');
    const bayarInput = document.getElementById('bayar');
    const kembalianEl = document.getElementById('kembalian');

    if (metode === 'transfer') {
        cashSection.style.display = 'none';
        transferSection.style.display = 'block';
        bayarInput.value = '';
        bayarInput.readOnly = true;
        bayarInput.placeholder = 'Tidak perlu diisi';
        kembalianEl.innerText = 'Rp 0 (Upload Bukti)';
        kembalianEl.className = 'mb-0 fw-bold text-muted';
    } else {
        cashSection.style.display = 'block';
        transferSection.style.display = 'none';
        bayarInput.readOnly = false;
        bayarInput.placeholder = '0';
        hitungKembalian();
    }
}

function syncAndPreviewImage(input) {
    const file = input.files[0];
    if (file) {
        const targetInput = document.getElementById('bukti_pembayaran');
        targetInput.files = input.files;

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('preview-img');
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function render(lastUpdatedKey = null) {
    let html = '';
    let subtotal = 0;

    if(cart.length === 0) {
        html = '<p class="text-center text-muted py-3">Keranjang kosong</p>';
    } else {
        cart.forEach(i => {
            let key = i.id + '_' + i.konversi;
            let sub = i.qty * i.harga;
            subtotal += sub;
            const isUpdated = (lastUpdatedKey && key === lastUpdatedKey) ? 'cart-item-added' : '';

            html += `
            <div class="d-flex justify-content-between align-items-center border-bottom py-2 ${isUpdated}">
                <div style="flex:1">
                    <div class="fw-bold small">${i.nama}</div>
                    <small class="text-muted">Rp ${rupiah(i.harga)} / ${i.satuan}</small>
                </div>
                <div class="d-flex align-items-center mx-2">
                    <button onclick="updateQty('${key}', -1)" class="btn btn-xs btn-outline-danger px-2">-</button>
                    <span class="mx-2 fw-bold">${i.qty}</span>
                    <button onclick="updateQty('${key}', 1)" class="btn btn-xs btn-outline-success px-2">+</button>
                </div>
                <div class="text-end fw-bold" style="min-width:80px;">${rupiah(sub)}</div>
            </div>`;
        });
    }

    document.getElementById('cart').innerHTML = html;
    hitungTotal(subtotal);

    if(lastUpdatedKey) {
        setTimeout(() => {
            document.querySelectorAll('.cart-item-added').forEach(el => el.classList.remove('cart-item-added'));
        }, 500);
    }
}

function updateQty(key, delta) {
    let item = cart.find(i => (i.id + '_' + i.konversi) === key);
    if(item) {
        item.qty += delta;
        if(item.qty <= 0) cart = cart.filter(i => (i.id + '_' + i.konversi) !== key);
    }
    render();
}

function hitungTotal(subtotal) {
    const diskonPersen = parseFloat(document.getElementById('diskon').value) || 0;
    const pakaiPPN = document.getElementById('ppn').checked;

    const nilaiDiskon = subtotal * (diskonPersen / 100);
    const setelahDiskon = subtotal - nilaiDiskon;
    const nilaiPPN = pakaiPPN ? setelahDiskon * 0.11 : 0;
    const totalFinal = setelahDiskon + nilaiPPN;

    document.getElementById('subtotal').innerText = 'Rp ' + rupiah(subtotal);
    document.getElementById('ppnRp').innerText = 'Rp ' + rupiah(nilaiPPN);
    document.getElementById('total').innerText = 'Rp ' + rupiah(totalFinal);

    document.getElementById('subtotalInput').value = Math.round(subtotal);
    document.getElementById('diskonInput').value = Math.round(nilaiDiskon);
    document.getElementById('ppnInput').value = Math.round(nilaiPPN);
    document.getElementById('totalInput').value = Math.round(totalFinal);

    hitungKembalian();
}

function hitungKembalian() {
    const metode = document.getElementById('metode_pembayaran_select').value;
    const total = parseFloat(document.getElementById('totalInput').value) || 0;
    const bayar = parseFloat(document.getElementById('bayar').value) || 0;
    const kembali = bayar - total;
    const el = document.getElementById('kembalian');

    if (metode === 'transfer') {
        el.innerText = 'Rp 0 (Upload Bukti)';
        el.className = 'mb-0 fw-bold text-muted';
    } else {
        el.innerText = 'Rp ' + rupiah(kembali);
        el.className = kembali < 0 ? 'mb-0 fw-bold text-danger' : 'mb-0 fw-bold text-success';
    }
}

async function checkout() {
    if(cart.length === 0) {
        await Swal.fire({
            icon: 'warning',
            title: 'Keranjang Kosong',
            text: 'Silakan tambahkan produk terlebih dahulu',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    const total = parseFloat(document.getElementById('totalInput').value);
    const metode = document.getElementById('metode_pembayaran_select').value;
    
    if (metode === 'tunai') {
        const bayar = parseFloat(document.getElementById('bayar').value);
        if(isNaN(bayar) || bayar < total) {
            await Swal.fire({
                icon: 'error',
                title: 'Pembayaran Kurang',
                text: `Total: Rp ${rupiah(total)}\nUang bayar: Rp ${rupiah(bayar || 0)}`,
                confirmButtonText: 'OK'
            });
            return;
        }
        document.getElementById('bayarInput').value = bayar;
    } 
    else if (metode === 'transfer') {
        const buktiFile = document.getElementById('bukti_pembayaran').files[0];
        if(!buktiFile) {
            await Swal.fire({
                icon: 'warning',
                title: 'Bukti Transfer Diperlukan',
                text: 'Harap upload bukti pembayaran transfer!',
                confirmButtonText: 'OK'
            });
            return;
        }
        document.getElementById('bayarInput').value = total;
    }

    const confirm = await Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Pembayaran',
        html: `Total: <strong>Rp ${rupiah(total)}</strong><br>Metode: <strong>${metode === 'tunai' ? 'Tunai (Cash)' : 'Transfer Bank'}</strong>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar',
        cancelButtonText: 'Batal'
    });

    if (confirm.isConfirmed) {
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        document.getElementById('produkInput').value = JSON.stringify(cart);
        document.getElementById('metodeInput').value = metode;
        document.getElementById('formCheckout').submit();
    }
}

function rupiah(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}

function setBayar(n) {
    document.getElementById('bayar').value = n;
    hitungKembalian();
}

function playBeep(){
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 800; gain.gain.value = 0.1;
        osc.start(); setTimeout(() => { osc.stop(); ctx.close(); }, 100);
    } catch(e) {}
}

function flyToCart(btn) {
    const img = btn.closest('.card').querySelector('img');
    if(!img) return;
    const rect = img.getBoundingClientRect();
    const clone = img.cloneNode(true);
    clone.classList.add('fly-clone');
    clone.style.width = rect.width+'px'; clone.style.left = rect.left+'px'; clone.style.top = rect.top+'px';
    document.body.appendChild(clone);
    const target = document.querySelector('#total').getBoundingClientRect();
    requestAnimationFrame(() => {
        clone.style.transform = `translate(${target.left - rect.left}px, ${target.top - rect.top}px) scale(0.1)`;
        clone.style.opacity = '0';
    });
    setTimeout(() => clone.remove(), 600);
}

// Event Listeners
document.getElementById('search').addEventListener('input', function() {
    let k = this.value.toLowerCase();
    document.querySelectorAll('.produk-item').forEach(i => {
        i.style.display = i.innerText.toLowerCase().includes(k) ? '' : 'none';
    });
});
document.getElementById('diskon').addEventListener('input', () => {
    let subtotal = cart.reduce((sum, i) => sum + (i.qty * i.harga), 0);
    hitungTotal(subtotal);
});
document.getElementById('ppn').addEventListener('change', () => {
    let subtotal = cart.reduce((sum, i) => sum + (i.qty * i.harga), 0);
    hitungTotal(subtotal);
});
document.getElementById('bayar').addEventListener('input', () => hitungKembalian());

// Notifikasi flashdata
<?php if($this->session->flashdata('success')): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= $this->session->flashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?= $this->session->flashdata('error') ?>',
    confirmButtonText: 'OK'
});
<?php endif; ?>
</script>