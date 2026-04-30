document.addEventListener('DOMContentLoaded', function(){

let cart = [];

const supplier = document.getElementById('supplier');
const produkList = document.getElementById('produkList');
const searchInput = document.getElementById('searchProduk');
const btnSimpan = document.getElementById('btnSimpan');

// ======================
// FORMAT RUPIAH
// ======================
function rupiah(angka){
    angka = parseInt(angka) || 0;
    return 'Rp ' + angka.toLocaleString('id-ID');
}

// ======================
// RENDER CART
// ======================
function renderCart(){
    let html = '';
    let total = 0;

    cart.forEach((item,i)=>{
        total += item.subtotal;

        html += `
        <tr>
            <td>${item.nama}</td>

            <td>
                <div class="d-flex align-items-center gap-1">

                    <button onclick="minusQty(${i})" 
                        class="btn btn-sm btn-danger">-</button>

                    <input type="number" min="1"
                        value="${item.qty}"
                        onchange="ubahQty(${i}, this.value)"
                        style="width:60px; text-align:center"
                        class="form-control form-control-sm">

                    <button onclick="plusQty(${i})" 
                        class="btn btn-sm btn-success">+</button>

                </div>
            </td>

            <td>${rupiah(item.subtotal)}</td>

            <td>
                <button onclick="hapus(${i})" 
                    class="btn btn-sm btn-danger">x</button>
            </td>
        </tr>`;
    });

    document.getElementById('cart').innerHTML = html;
    document.getElementById('total').innerText = total.toLocaleString('id-ID');
}

// ======================
// TAMBAH PRODUK
// ======================
window.tambahProduk = function(p){

    const stok = parseInt(p.stok) || 0;
    const minimal = parseInt(p.stok_minimal) || 0;
    const harga = parseInt(p.harga_beli) || 0;

    // 🔥 fix qty biar tidak minus
    let qty = minimal > stok ? (minimal - stok) : 1;

    let existing = cart.find(item => item.id === p.id_produk);

    if(existing){
        existing.qty += qty;
        existing.subtotal = existing.qty * existing.harga;
    } else {
        cart.push({
            id: p.id_produk,
            nama: p.nama_produk,
            harga: harga,
            qty: qty,
            subtotal: qty * harga
        });
    }

    animasi(p.id_produk);
    renderCart();
};

// ======================
// HAPUS
// ======================
window.hapus = function(i){
    cart.splice(i,1);
    renderCart();
};

// ======================
// LOAD PRODUK
// ======================
if(supplier){
supplier.addEventListener('change', function(){

    let id = this.value;

    if(!id){
        produkList.innerHTML = '';
        return;
    }

    fetch(BASE_URL + 'pembelian/getProdukBySupplier?supplier_id='+id)
    .then(res => res.json())
    .then(data => {

        let html = '';

        if(data.length === 0){
            html = `<div class="text-muted">Tidak ada produk</div>`;
        }

        data.forEach(p => {

            const stok = parseInt(p.stok) || 0;
            const minimal = parseInt(p.stok_minimal) || 0;

            // 🔥 FIX WARNING
            let warning = '';
            if(minimal > 0 && stok <= minimal){
                warning = '<span class="badge bg-danger">⚠️ Stok Minim</span>';
            }

            html += `
            <div class="col-md-4 mb-3">
                <div class="card p-2 produk-card"
                     onclick='tambahProduk(${JSON.stringify(p)})'
                     id="card-${p.id_produk}"
                     style="cursor:pointer">

                    <img src="${p.gambar || 'https://via.placeholder.com/150'}"
                         style="height:120px; object-fit:cover; border-radius:8px">

                    <div class="mt-2">
                        <b>${p.nama_produk}</b>
                        <div class="text-success">
                            ${rupiah(p.harga_beli)}
                        </div>
                        <small>Stok: ${stok}</small><br>
                        ${warning}
                    </div>

                </div>
            </div>`;
        });

        produkList.innerHTML = html;

    })
    .catch(err => {
        console.error("ERROR LOAD PRODUK:", err);
    });

});
}

// ======================
// SEARCH
// ======================
if(searchInput){
searchInput.addEventListener('keyup', function(){

    let keyword = this.value.toLowerCase();

    document.querySelectorAll('.produk-card').forEach(card => {
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(keyword) ? 'block' : 'none';
    });

});
}

// ======================
// ANIMASI
// ======================
function animasi(id){
    let el = document.getElementById('card-'+id);
    if(!el) return;

    el.style.transform = 'scale(0.95)';
    setTimeout(()=> el.style.transform = 'scale(1)', 150);
}

// ======================
// SIMPAN
// ======================
if(btnSimpan){
btnSimpan.addEventListener('click', function(){

    if(cart.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Keranjang Kosong',
            text: 'Silakan pilih produk terlebih dahulu'
        });
        return;
    }

    if(!supplier.value){
        Swal.fire({
            icon: 'warning',
            title: 'Supplier Belum Dipilih',
            text: 'Silakan pilih supplier terlebih dahulu'
        });
        return;
    }

    // 🔄 LOADING
    Swal.fire({
        title: 'Menyimpan Order...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    let formData = new URLSearchParams();
    formData.append('supplier', supplier.value);
    formData.append('cart', JSON.stringify(cart));

    // 🔥 CSRF
    formData.append(CSRF_NAME, CSRF_HASH);

    fetch(BASE_URL + 'pembelian/simpan', {
        method:'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {

        Swal.close();

        if(res.status === 'ok'){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Order berhasil disimpan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = BASE_URL + 'pembelian/detail/' + res.id;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: res.message || 'Terjadi kesalahan'
            });
        }

    })
    .catch(err => {
        Swal.close();

        console.error(err);

        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Gagal terhubung ke server'
        });
    });

});
}

window.plusQty = function(i){
    cart[i].qty++;
    cart[i].subtotal = cart[i].qty * cart[i].harga;
    renderCart();
};

window.minusQty = function(i){
    if(cart[i].qty > 1){
        cart[i].qty--;
        cart[i].subtotal = cart[i].qty * cart[i].harga;
        renderCart();
    }
};

window.ubahQty = function(i, val){
    let qty = parseInt(val);

    if(qty < 1 || isNaN(qty)){
        qty = 1;
    }

    cart[i].qty = qty;
    cart[i].subtotal = qty * cart[i].harga;

    renderCart();
};

});