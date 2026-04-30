<div class="container-fluid">

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
                    <?= $p->nama_kategori ?> | <?= $p->nama_supplier ?>
                </small>

                <!-- HARGA -->
                <div class="text-success fw-bold mt-1">
                    Rp <?= number_format($p->harga_jual,0,',','.') ?>
                </div>

                <!-- BUTTON -->
                <button class="btn btn-success btn-sm mt-2 w-100"
                    onclick="tambah(<?= $p->id_produk ?>, `<?= htmlspecialchars($p->nama_produk) ?>`, <?= $p->harga_jual ?>)">
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

            <!-- TOTAL -->
            <h5>Total: Rp <span id="total">0</span></h5>

            <!-- INPUT BAYAR -->
            <input type="number" id="bayar" class="form-control mt-2" placeholder="Masukkan uang">

            <!-- KEMBALIAN -->
            <h6 class="mt-2">Kembalian: Rp <span id="kembalian">0</span></h6>

            <!-- QUICK BUTTON -->
            <div class="mt-2 d-flex gap-2 flex-wrap">
                <button class="btn btn-light btn-sm" onclick="setBayar(10000)">10K</button>
                <button class="btn btn-light btn-sm" onclick="setBayar(20000)">20K</button>
                <button class="btn btn-light btn-sm" onclick="setBayar(50000)">50K</button>
                <button class="btn btn-light btn-sm" onclick="setBayar(100000)">100K</button>
            </div>

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

    <input type="hidden" name="produk" id="produkInput">
    <input type="hidden" name="total" id="totalInput">
    <input type="hidden" name="bayar" id="bayarInput">

</form>
<!-- ========================= -->
<!-- SCRIPT -->
<!-- ========================= -->
<script>
const BASE_URL = "<?= base_url() ?>";
let cart = [];

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

    render();
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

    render();
}

// =========================
// RENDER CART
// =========================
function render(){
    let html = '';
    let total = 0;

    cart.forEach(i => {
        let sub = i.qty * i.harga;
        i.subtotal = sub;
        total += sub;

        html += `
        <tr>
            <td>${i.nama}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="kurang(${i.id})">-</button>
                <span class="mx-2">${i.qty}</span>
                <button class="btn btn-sm btn-success" onclick="tambah(${i.id}, \`${i.nama}\`, ${i.harga})">+</button>
            </td>
            <td>Rp ${rupiah(sub)}</td>
        </tr>`;
    });

    document.getElementById('cart').innerHTML = html;
    document.getElementById('total').innerText = rupiah(total);

    hitungKembalian();
}                

// =========================
// SET BAYAR CEPAT
// =========================
function setBayar(n){
    document.getElementById('bayar').value = n;
    hitungKembalian();
}

// =========================
// HITUNG KEMBALIAN
// =========================
function hitungKembalian(){
    let total = cart.reduce((a,b)=>a + (b.subtotal || 0), 0);
    let bayar = parseInt(document.getElementById('bayar').value) || 0;

    let kembali = bayar - total;
    document.getElementById('kembalian').innerText = rupiah(kembali > 0 ? kembali : 0);
}

// =========================
// CHECKOUT
// =========================
function checkout(){

    let btn = document.querySelector('.btn-primary');
    btn.innerText = 'Memproses...';
    btn.disabled = true;

    let total = cart.reduce((a,b)=>a + (b.subtotal || 0), 0);
    let bayar = parseInt(document.getElementById('bayar').value) || 0;

    if(cart.length === 0){
        alert('Keranjang kosong!');
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

    document.getElementById('produkInput').value = JSON.stringify(cart);
    document.getElementById('totalInput').value = total;
    document.getElementById('bayarInput').value = bayar;

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
</script>