// =========================
// BASE URL (ANTI DOUBLE DECLARE)
// =========================
// if (typeof BASE_URL === 'undefined') {
//     var BASE_URL = window.BASE_URL || '';
// }

// =========================
// STATE
// =========================
let cart = [];

// =========================
// TAMBAH PRODUK
// =========================
function tambah(id, nama, harga, satuan = '-') {

    let item = cart.find(i => i.id == id);

    if (item) {
        item.qty++;
    } else {
        cart.push({
            id,
            nama,
            harga: parseInt(harga),
            qty: 1,
            satuan // ✅ TAMBAHAN
        });
    }

    render(id);
}

// =========================
// KURANG PRODUK
// =========================
function kurang(id) {
    let item = cart.find(i => i.id == id);

    if (item) {
        item.qty--;

        if (item.qty <= 0) {
            cart = cart.filter(i => i.id != id);
        }
    }

    render();
}

// =========================
// RENDER KERANJANG
// =========================
function render(lastUpdatedId = null) {

    let html = '';
    let subtotal = 0;

    cart.forEach(i => {

        let sub = i.qty * i.harga;
        subtotal += sub;

        const highlight = (lastUpdatedId && i.id == lastUpdatedId) ? 'cart-item-added' : '';

        html += `
        <div class="d-flex justify-content-between align-items-center border-bottom py-2 ${highlight}">

            <div>
                <strong>${i.nama}</strong><br>
                <small>Rp ${rupiah(i.harga)} / ${i.satuan ?? '-'}</small>
            </div>

            <div class="d-flex align-items-center">
                <button onclick="kurang(${i.id})" class="btn btn-sm btn-outline-danger">-</button>
                <span class="mx-2">${i.qty}</span>
                <button onclick="tambah(${i.id}, '${escapeQuotes(i.nama)}', ${i.harga}, '${i.satuan}')" class="btn btn-sm btn-outline-success">+</button>
            </div>

            <div>
                Rp ${rupiah(sub)}
            </div>

        </div>`;
    });

    document.getElementById('cart').innerHTML = html;

    hitungTotal(subtotal);

    // animasi hilang
    setTimeout(() => {
        document.querySelectorAll('.cart-item-added').forEach(el => {
            el.classList.remove('cart-item-added');
        });
    }, 400);
}

// =========================
// HITUNG TOTAL + DISKON + PPN
// =========================
function hitungTotal(subtotal = 0) {

    subtotal = parseFloat(subtotal) || 0;

    const diskonEl = document.getElementById('diskon');
    const ppnEl    = document.getElementById('ppn');

    const diskon = diskonEl ? parseFloat(diskonEl.value) || 0 : 0;
    const pakaiPPN = ppnEl ? ppnEl.checked : false;

    // ================= DISKON
    const nilaiDiskon = subtotal * (diskon / 100);
    const setelahDiskon = subtotal - nilaiDiskon;

    // ================= PPN
    const nilaiPPN = pakaiPPN ? setelahDiskon * 0.11 : 0;

    // ================= TOTAL
    const total = setelahDiskon + nilaiPPN;

    // ================= UPDATE UI
    updateText('subtotal', rupiah(subtotal));
    updateText('diskonRp', rupiah(nilaiDiskon));
    updateText('ppnRp', rupiah(nilaiPPN));
    updateText('total', rupiah(total));

    // ================= HIDDEN INPUT (WAJIB UNTUK BACKEND)
    setValue('totalInput', Math.round(total));
    setValue('ppnInput', Math.round(nilaiPPN));
    setValue('diskonInput', Math.round(nilaiDiskon));
    setValue('subtotalInput', Math.round(subtotal));

    // // ================= DEBUG (optional)
    // console.log({
    //     subtotal,
    //     diskon,
    //     nilaiDiskon,
    //     setelahDiskon,
    //     pakaiPPN,
    //     nilaiPPN,
    //     total
    // });

    hitungKembalian(total);
}
// =========================
// HITUNG KEMBALIAN
// =========================
function hitungKembalian(total = 0) {

    let bayar = parseInt(document.getElementById('bayar')?.value) || 0;
    let kembali = bayar - total;

    let el = document.getElementById('kembalian');

    if (el) {
        el.innerText = rupiah(kembali);

        if (kembali < 0) {
            el.style.color = 'red';
        } else {
            el.style.color = 'green';
        }
    }

    return kembali;
}

// =========================
// SET BAYAR CEPAT
// =========================
function setBayar(nominal) {
    document.getElementById('bayar').value = nominal;
    hitungKembalian();
}

// =========================
// CHECKOUT
// =========================
function checkout() {

    let total = parseInt(document.getElementById('totalInput')?.value) || 0;
    let bayar = parseInt(document.getElementById('bayar')?.value) || 0;

    if (cart.length === 0) {
        Swal.fire('Error', 'Keranjang kosong!', 'error');
        return;
    }

    if (bayar < total) {
        Swal.fire('Error', 'Uang tidak cukup!', 'error');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: "Total: Rp " + rupiah(total),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Bayar'
    }).then((result) => {

        if (result.isConfirmed) {

            document.getElementById('produkInput').value = JSON.stringify(cart);
            document.getElementById('bayarInput').value = bayar;

            document.getElementById('formCheckout').submit();
        }
    });
}

// =========================
// UTIL
// =========================
function rupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka || 0);
}

function updateText(id, value) {
    const el = document.getElementById(id);
    if (el) el.innerText = value;
}

function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value;
}

function escapeQuotes(str) {
    return str.replace(/'/g, "\\'");
}

// =========================
// INIT
// =========================
document.addEventListener('DOMContentLoaded', function () {

    // SEARCH
    const search = document.getElementById('search');
    if (search) {
        search.addEventListener('keyup', function () {
            let keyword = this.value.toLowerCase();

            document.querySelectorAll('.produk-item').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(keyword) ? '' : 'none';
            });
        });
    }

    // INPUT BAYAR REALTIME
    const bayar = document.getElementById('bayar');
    if (bayar) {
        bayar.addEventListener('keyup', () => hitungKembalian());
        bayar.addEventListener('change', () => hitungKembalian());
    }

    // DISKON REALTIME
    const diskon = document.getElementById('diskon');
    if (diskon) {
        diskon.addEventListener('keyup', () => render());
    }

    // PPN TOGGLE
    const ppn = document.getElementById('ppn');
    if (ppn) {
        ppn.addEventListener('change', () => render());
    }

    // LOAD RESEP (AUTO KERANJANG)
    if (typeof resepItems !== 'undefined' && resepItems.length > 0) {

        resepItems.forEach(r => {

            let existing = cart.find(i => i.id == r.produk_id);

            if (existing) {
                existing.qty += parseInt(r.jumlah);
            } else {
                cart.push({
                    id: r.produk_id,
                    nama: r.nama_produk,
                    qty: parseInt(r.jumlah),
                    harga: parseFloat(r.harga),
                    satuan: r.satuan || '-' // ✅ FIX
                });
            }


        });

        render();

        Swal.fire({
            icon: 'success',
            title: 'Resep dimuat',
            text: 'Keranjang otomatis terisi'
        });
    }

});