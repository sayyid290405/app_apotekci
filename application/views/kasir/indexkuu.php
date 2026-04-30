<div class="container-fluid">
    <style>
        /* clone yang terbang */
.fly-clone {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
    will-change: transform, opacity;
    transition: transform .6s cubic-bezier(.2,.8,.2,1), opacity .6s ease;
}

/* efek pulse keranjang saat kena */
.cart-bump {
    animation: cartBump .35s ease;
}
@keyframes cartBump {
    0% { transform: scale(1); }
    40% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
    </style>

<h4 class="mb-3">🛒 KASIR</h4>

<div class="row">

    <!-- ========================= -->
    <!-- PRODUK -->
    <!-- ========================= -->
    <div class="col-md-8">

        <input type="text" id="search" class="form-control mb-3" placeholder="🔍 Cari produk...">

        <div class="row">

        <?php foreach($produk as $p): ?>
        <div class="col-md-4 produk-item">
            <div class="card p-3 mb-3 text-center shadow-sm">

                <!-- GAMBAR -->
        <img src="<?= !empty($p->gambar) ? $p->gambar : 'https://via.placeholder.com/150' ?>" 
             class="img-fluid mb-2"
             style="height:120px; object-fit:cover; border-radius:10px;">

        <!-- NAMA -->
        <h6 class="fw-bold"><?= htmlspecialchars($p->nama_produk) ?></h6>

        <!-- INFO -->
        <small class="text-muted">
            Stok: <?= $p->stok ?> <?= $p->satuan_dasar ?>
        </small>

        <!-- PILIH SATUAN -->
        <select class="form-control form-control-sm mt-2 satuan-select">
            <?php foreach($p->satuan as $s): ?>
                <option 
                    value="<?= $s->konversi ?>"
                    data-harga="<?= $s->harga ?>"
                    data-satuan="<?= $s->nama_satuan ?>">
                    
                    <?= $s->nama_satuan ?> 
                    (Rp <?= number_format($s->harga,0,',','.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

                <!-- BUTTON -->
                <button class="btn btn-success"
                onclick="tambah(<?= $p->id_produk ?>, '<?= addslashes($p->nama_produk) ?>', <?= $p->harga_jual ?>); flyToCart(this)">
                + Tambah
                </button>

            </div>
        </div>
        <?php endforeach; ?>

        </div>
    </div>

    <!-- ========================= -->
    <!-- KERANJANG -->
    <!-- ========================= -->
    <div class="col-md-4">

        <div class="card p-3 shadow-sm">

            <h5 class="mb-3">🧾 Keranjang</h5>

            <div style="max-height:300px; overflow-y:auto;">
                <table class="table table-sm">
                    <tbody id="cart"></tbody>
                </table>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
        <span>Subtotal</span>
        <strong id="subtotal">Rp 0</strong>
    </div>

    <div class="mt-2">
    <label>
        <input type="checkbox" id="ppn" checked> Termasuk PPN (11%)
    </label>
</div>

<div class="mt-2">
    <small>PPN: Rp <span id="ppnRp">0</span></small>
</div>

<h5 class="mt-2">Total: Rp <span id="total">0</span></h5>

    <div class="mt-2">
        <label>Diskon (%)</label>
        <input type="number" id="diskon" class="form-control" value="0">
    </div>

    <hr>

    <input type="number" id="bayar" class="form-control mt-2" placeholder="Masukkan uang">

    <div class="mt-2 d-flex gap-2 flex-wrap">
        <button onclick="setBayar(10000)" class="btn btn-light btn-sm">10K</button>
        <button onclick="setBayar(20000)" class="btn btn-light btn-sm">20K</button>
        <button onclick="setBayar(50000)" class="btn btn-light btn-sm">50K</button>
    </div>

    <div class="mt-2">
        Kembalian: <strong id="kembalian">Rp 0</strong>
            <!-- CHECKOUT -->
            <button type="button" class="btn btn-primary w-100 mt-3" onclick="checkout()">
                💳 Bayar
            </button>

        </div>

    </div>

</div>

</div>
<form id="formCheckout" method="POST" action="<?= base_url('kasir/simpan') ?>">

    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
           value="<?= $this->security->get_csrf_hash(); ?>">

    <!-- EXISTING -->
    <input type="hidden" name="produk" id="produkInput">

    <!-- 🔥 TAMBAHAN (BEST PRACTICE) -->
    <input type="hidden" name="subtotal" id="subtotalInput">
    <input type="hidden" name="diskon" id="diskonInput">
    <input type="hidden" name="ppn" id="ppnInput">
    <input type="hidden" name="total" id="totalInput">
    <input type="hidden" name="bayar" id="bayarInput">
    <input type="hidden" name="resep_id" id="resepInput">

</form>

<!-- SCRIPT -->
<!-- ========================= -->
<script>
const BASE_URL = "<?= base_url() ?>";
let cart = [];
let PPN_RATE = 0.11;
let pakaiPPN = false;

// =========================
// TAMBAH PRODUK
// =========================
function tambah(id,nama,harga){
    let item = cart.find(i => i.id == id);

    if(item){
        item.qty++;
    } else {
        cart.push({id,nama,harga,qty:1});
    }

    playBeep();         // 🔊 suara
    render(id);
}

// =========================
// KURANG PRODUK
// =========================
function kurang(id){
    let item = cart.find(i => i.id == id);

    if(item){
        item.qty--;
        if(item.qty <= 0){
            cart = cart.filter(i => i.id != id);
        }
    }

    render(id); 
}

// helper untuk hindari tanada petik
function escapeQuotes(str){
    return String(str).replace(/'/g, "\\'");
}

// untuk tambah multi satuan
function tambahMulti(el, id, nama){

    let select = el.parentElement.querySelector('.satuan-select');

    let harga = parseInt(select.options[select.selectedIndex].dataset.harga);
    let konversi = parseInt(select.value);
    let satuan = select.options[select.selectedIndex].dataset.satuan;

    let item = cart.find(i => i.id == id && i.konversi == konversi);

    if(item){
        item.qty++;
    } else {
        cart.push({
            id,
            nama,
            harga,
            konversi,
            satuan,
            qty:1
        });
    }

    render(id);
}

function tambahMultiDirect(id, nama, harga, konversi, satuan){

    let item = cart.find(i => i.id == id && i.konversi == konversi);

    if(item){
        item.qty++;
    }

    render(id);
}

// dom klik ppn
document.addEventListener('DOMContentLoaded', function(){

    const ppn = document.getElementById('ppn');
    const diskon = document.getElementById('diskon');

    if(ppn){
        ppn.addEventListener('change', function(){
            render(); // 🔥 trigger ulang total
        });
    }

    if(diskon){
        diskon.addEventListener('keyup', function(){
            render();
        });
    }

});

// =========================
// RENDER CART
// =========================
// tambahkan class dinamis jika baru ditambah
function render(lastUpdatedId = null){

    let html = '';
    let subtotal = 0;

    cart.forEach(i => {
        let sub = i.qty * i.harga;
        subtotal += sub;

        const isUpdated = (lastUpdatedId && i.id == lastUpdatedId) ? 'cart-item-added' : '';

        html += `
        <div class="d-flex justify-content-between align-items-center border-bottom py-2 ${isUpdated}">

            <div>
                <strong>${i.nama}</strong><br>
                <small>Rp ${rupiah(i.harga)}</small>
            </div>

            <div class="d-flex align-items-center">
                <button onclick="kurang(${i.id})" class="btn btn-sm btn-outline-danger">-</button>
                <span class="mx-2">${i.qty}</span>
                <button onclick="tambah(${i.id}, '${escapeQuotes(i.nama)}', ${i.harga})" class="btn btn-sm btn-outline-success">+</button>
            </div>

            <div>
                Rp ${rupiah(sub)}
            </div>

        </div>`;
    });

    document.getElementById('cart').innerHTML = html;

    hitungTotal(subtotal);

    // hilangkan class animasi setelah selesai (opsional)
    setTimeout(()=>{
        document.querySelectorAll('.cart-item-added').forEach(el=>{
            el.classList.remove('cart-item-added');
        });
    }, 450);
}

// =========================
// SET BAYAR CEPAT
// =========================
function setBayar(n){
    document.getElementById('bayar').value = n;
    hitungKembalian();
}

// HITUNG TOTAL BAYAR

function hitungTotal(subtotal = 0){

    subtotal = parseFloat(subtotal) || 0;

    const diskonEl = document.getElementById('diskon');
    const ppnEl    = document.getElementById('ppn');

    // ✅ deklarasi SEKALI saja
    const diskon = diskonEl ? parseFloat(diskonEl.value) || 0 : 0;
    const pakaiPPN = ppnEl ? ppnEl.checked : false;

    
    // ================= DISKON
    const nilaiDiskon = subtotal * (diskon / 100);
    const setelahDiskon = subtotal - nilaiDiskon;

    // ================= PPN
    const nilaiPPN = pakaiPPN ? setelahDiskon * 0.11 : 0;

    // ================= TOTAL
    const total = setelahDiskon + nilaiPPN;

    // // ================= DEBUG
    // console.log({
    //     subtotal,
    //     diskon,
    //     pakaiPPN,
    //     nilaiDiskon,
    //     setelahDiskon,
    //     nilaiPPN,
    //     total
    // });

    // ================= UPDATE UI
    const elSubtotal = document.getElementById('subtotal');
    const elDiskon   = document.getElementById('diskonRp');
    const elPPN      = document.getElementById('ppnRp');
    const elTotal    = document.getElementById('total');

    if(elSubtotal) elSubtotal.innerText = rupiah(subtotal);
    if(elDiskon)   elDiskon.innerText   = rupiah(nilaiDiskon);
    if(elPPN)      elPPN.innerText      = rupiah(nilaiPPN);
    if(elTotal)    elTotal.innerText    = rupiah(total);

    // ================= SIMPAN
    const totalInput  = document.getElementById('totalInput');
    const ppnInput    = document.getElementById('ppnInput');
    const diskonInput = document.getElementById('diskonInput');

    if(totalInput)  totalInput.value  = Math.round(total);
    if(ppnInput)    ppnInput.value    = Math.round(nilaiPPN);
    if(diskonInput) diskonInput.value = Math.round(nilaiDiskon);

    // ================= KEMBALIAN
    hitungKembalian(total);
}

// =========================
// HITUNG KEMBALIAN
// =========================
function hitungKembalian(total = null){

    // ================= AMBIL TOTAL FINAL (PRIORITAS)
    if(total === null){

        let totalEl = document.getElementById('totalInput');

        if(totalEl && totalEl.value){
            total = parseFloat(totalEl.value);
        } else {
            // fallback (jarang dipakai)
            total = cart.reduce((a,b)=>{
                let harga = parseFloat(b.harga) || 0;
                let qty   = parseInt(b.qty) || 0;
                return a + (harga * qty);
            }, 0);
        }
    }

    total = parseFloat(total) || 0;

    // ================= AMBIL BAYAR
    let bayarEl = document.getElementById('bayar');
    let bayar = bayarEl ? parseInt(bayarEl.value) || 0 : 0;

    // ================= HITUNG
    let kembali = bayar - total;

    // ================= UPDATE UI
    let el = document.getElementById('kembalian');

    if(el){
        el.innerText = rupiah(kembali);

        if(kembali < 0){
            el.style.color = 'red';
        } else {
            el.style.color = 'green';
        }
    }

    // ================= SIMPAN KE FORM
    let kembaliInput = document.getElementById('kembalianInput');
    if(kembaliInput){
        kembaliInput.value = kembali;
    }

    // ================= DEBUG
    console.log({
        total,
        bayar,
        kembali
    });

    return kembali;
}

// =========================
// CHECKOUT
// =========================
function checkout(){

    let btn = document.querySelector('.btn-primary');
    btn.innerText = 'Memproses...';
    btn.disabled = true;

    // ================= HITUNG SUBTOTAL DARI CART
    let subtotal = cart.reduce((a,b)=>a + (b.qty * b.harga), 0);

    // ================= PAKSA HITUNG TOTAL
    hitungTotal(subtotal);

    // ================= AMBIL SEMUA NILAI (FAIL SAFE)
    let subtotalVal = parseInt(document.getElementById('subtotalInput')?.value) || subtotal;
    let diskonVal   = parseInt(document.getElementById('diskonInput')?.value) || 0;
    let ppnVal      = parseInt(document.getElementById('ppnInput')?.value) || 0;
    let total       = parseInt(document.getElementById('totalInput')?.value) || subtotalVal;

    let bayar = parseInt(document.getElementById('bayar').value) || 0;

    // ================= VALIDASI
    if(cart.length === 0){
        alert('Keranjang kosong!');
        btn.disabled = false;
        btn.innerText = 'Bayar';
        return;
    }

    if(total <= 0){
        alert('Total tidak valid!');
        btn.disabled = false;
        btn.innerText = 'Bayar';
        return;
    }

    if(bayar < total){
        alert('Uang tidak cukup!');
        btn.disabled = false;
        btn.innerText = 'Bayar';
        return;
    }

    // ================= SET KE FORM (PASTIKAN TERISI)
    document.getElementById('produkInput').value = JSON.stringify(cart);
    document.getElementById('bayarInput').value = bayar;

    document.getElementById('subtotalInput').value = subtotalVal;
    document.getElementById('diskonInput').value   = diskonVal;
    document.getElementById('ppnInput').value      = ppnVal;
    document.getElementById('totalInput').value    = total;

    // ================= DEBUG (opsional)
    console.log({
        subtotal: subtotalVal,
        diskon: diskonVal,
        ppn: ppnVal,
        total: total,
        bayar: bayar
    });

    // ================= SUBMIT
    document.getElementById('formCheckout').submit();
}

// =========================
// FORMAT RUPIAH
// =========================
function rupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}

// =========================
// SEARCH
// =========================
document.addEventListener('DOMContentLoaded', function(){

    document.getElementById('search').addEventListener('keyup', function(){
        let keyword = this.value.toLowerCase();

        document.querySelectorAll('.produk-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });

});

document.addEventListener('DOMContentLoaded', function(){

    if(typeof resepItems !== 'undefined' && resepItems.length > 0){

        resepItems.forEach(r => {

            let existing = cart.find(i => i.id == r.produk_id);

            if(existing){
                existing.qty += parseInt(r.jumlah);
            } else {
                cart.push({
                    id: r.produk_id,
                    nama: r.nama_produk,
                    harga: parseInt(r.harga_jual),
                    qty: parseInt(r.jumlah)
                });
            }

        });

        render();

        Swal.fire({
            icon: 'success',
            title: 'Resep dimuat',
            
        });
    }

});

document.getElementById('bayar').addEventListener('keyup', function(){
    hitungKembalian();
});

document.getElementById('bayar').addEventListener('change', function(){
    hitungKembalian();
});

document.getElementById('diskon').addEventListener('keyup', () => render());
document.getElementById('ppn').addEventListener('change', () => render());
document.getElementById('bayar').addEventListener('keyup', () => render());

// 🔊 beep kasir
function playBeep(){
    try{
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'sine';      // suara halus
        osc.frequency.value = 880; // nada (Hz)
        gain.gain.value = 0.05; // volume

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start();
        setTimeout(() => {
            osc.stop();
            ctx.close();
        }, 120); // durasi beep
    }catch(e){
        console.warn('Audio blocked:', e);
    }
}

// animasi fly to chart
function flyToCart(btn){
    const card = btn.closest('.card');
    const img  = card?.querySelector('img');

    if(!img) return;

    const rect = img.getBoundingClientRect();

    // clone image
    const clone = img.cloneNode(true);
    clone.classList.add('fly-clone');

    // set posisi awal (pakai transform biar smooth)
    clone.style.width  = rect.width + 'px';
    clone.style.height = rect.height + 'px';
    clone.style.left   = rect.left + 'px';
    clone.style.top    = rect.top + 'px';

    // supaya transform start dari posisi awal
    clone.style.transform = 'translate(0, 0) scale(1)';
    clone.style.opacity = '1';

    document.body.appendChild(clone);

    // target = keranjang (pilih container keranjangmu)
    const cartBox = document.querySelector('.card:has(#cart)') || document.querySelector('#cart');
    const target  = cartBox.getBoundingClientRect();

    // hitung delta posisi
    const dx = (target.left + target.width/2) - (rect.left + rect.width/2);
    const dy = (target.top  + target.height/2) - (rect.top  + rect.height/2);

    // trigger animasi di frame berikutnya
    requestAnimationFrame(() => {
        clone.style.transform = `translate(${dx}px, ${dy}px) scale(.2)`;
        clone.style.opacity = '0';
    });

    // efek bump di keranjang
    cartBox.classList.add('cart-bump');

    setTimeout(() => {
        clone.remove();
        cartBox.classList.remove('cart-bump');
    }, 650);
}

document.addEventListener('DOMContentLoaded', function(){

    const params = new URLSearchParams(window.location.search);
    const resepId = params.get('resep');

    if(resepId){
        document.getElementById('resepInput').value = resepId;
    }

});

</script>

<script>
const resepItems = <?= json_encode($resep_items ?? []) ?>;
</script>