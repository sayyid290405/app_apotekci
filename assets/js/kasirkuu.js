const BASE_URL = "<?= base_url() ?>";
let cart = [];

// TAMBAH
function tambah(id,nama,harga){
    let item = cart.find(i => i.id == id);

    if(item){
        item.qty++;
    } else {
        cart.push({id,nama,harga,qty:1});
    }

    render();
}

// KURANG
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

// RENDER
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
                <button class="btn btn-sm btn-success" onclick='tambah(${i.id}, "${i.nama}", ${i.harga})'>+</button>
            </td>
            <td>Rp ${rupiah(sub)}</td>
        </tr>`;
    });

    document.getElementById('cart').innerHTML = html;
    document.getElementById('total').innerText = rupiah(total);

    hitungKembalian();
}

// FORMAT
function rupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}

function checkout(){

    let total = cart.reduce((a,b)=>a + (b.subtotal || 0), 0);
    let bayar = parseInt(document.getElementById('bayar').value) || 0;

    if(cart.length === 0){
        Swal.fire('Error','Keranjang kosong!','error');
        return;
    }

    if(bayar < total){
        Swal.fire('Error','Uang tidak cukup!','error');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: "Total: Rp " + rupiah(total),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Bayar Sekarang'
    }).then((result) => {

        if(result.isConfirmed){

            document.getElementById('produkInput').value = JSON.stringify(cart);
            document.getElementById('totalInput').value = total;
            document.getElementById('bayarInput').value = bayar;

            document.getElementById('formCheckout').submit();
        }

    });
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

    document.getElementById('kembalian').innerText =
        rupiah(kembali > 0 ? kembali : 0);
}

// =========================
// EVENT INPUT BAYAR (REALTIME)
// =========================
document.addEventListener('DOMContentLoaded', function(){

    let inputBayar = document.getElementById('bayar');

    if(inputBayar){
        inputBayar.addEventListener('keyup', hitungKembalian);
        inputBayar.addEventListener('change', hitungKembalian);
    }

});

// DOM READY
document.addEventListener('DOMContentLoaded', function(){

    // SEARCH
    document.getElementById('search').addEventListener('keyup', function(){
        let keyword = this.value.toLowerCase();

        document.querySelectorAll('.produk-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });

    // LOAD RESEP
    if(typeof resepItems !== 'undefined' && resepItems.length > 0){

        resepItems.forEach(r => {

            let existing = cart.find(i => i.id == r.id_produk);

            if(existing){
                existing.qty += parseInt(r.jumlah);
            } else {
                cart.push({
                    id: r.id_produk,
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
            text: 'Keranjang otomatis terisi dari resep'
        });
    }



});