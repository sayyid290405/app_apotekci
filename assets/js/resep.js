document.addEventListener('DOMContentLoaded', function(){

let items = [];
let fileResep = null;

const search = document.getElementById('search');
const hasil = document.getElementById('hasil');

// ================= PREVIEW GAMBAR
document.getElementById('gambarResep').addEventListener('change', function(e){

    fileResep = e.target.files[0];

    let reader = new FileReader();
    reader.onload = function(e){
        let img = document.getElementById('previewResep');
        img.src = e.target.result;
        img.style.display = 'block';
    }
    reader.readAsDataURL(fileResep);
});

// ================= SEARCH
search.addEventListener('keyup', function(){

    let q = this.value.trim();

    if(q.length < 2){
        hasil.innerHTML = '';
        return;
    }

    fetch(BASE_URL + 'resep/searchProduk?q=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {

        let html = '';

        data.forEach(p=>{
            html += `
            <a href="#" class="list-group-item list-group-item-action"
               onclick='tambah(${JSON.stringify(p)})'>
                ${p.nama_produk}
            </a>`;
        });

        hasil.innerHTML = html;
    });

});

// ================= TAMBAH OBAT + SATUAN
window.tambah = function(p){

    let exist = items.find(i => i.id == p.id_produk);

    if(exist){
        exist.qty++;
        render();
        return;
    }

    // 🔥 ambil satuan dari database
    fetch(BASE_URL + 'produk/getSatuan/' + p.id_produk)
    .then(r => r.json())
    .then(satuan => {

        items.push({
            id: p.id_produk,
            nama: p.nama_produk,
            qty: 1,
            dosis: '',
            satuan: satuan[0]?.nama_satuan || '',
            harga: satuan[0]?.harga || 0,
            satuanList: satuan
        });

        render();
    });

    hasil.innerHTML = '';
    search.value = '';
};

// ================= RENDER TABLE
function render(){

    let html = '';
    let preview = '';

    items.forEach((i,index)=>{

        let opsi = '';

        i.satuanList.forEach(s=>{
            opsi += `
            <option value="${s.nama_satuan}" 
                    data-harga="${s.harga}"
                    ${i.satuan == s.nama_satuan ? 'selected' : ''}>
                ${s.nama_satuan} (Rp ${formatRupiah(s.harga)})
            </option>`;
        });

        html += `
        <tr>
            <td>${i.nama}</td>

            <!-- 🔥 SATUAN -->
            <td>
                <select class="form-control form-control-sm"
                        onchange="setSatuan(${index}, this)">
                    ${opsi}
                </select>
            </td>

            <td>
                <input type="text"
                       value="${i.dosis}"
                       onchange="setDosis(${index}, this.value)"
                       class="form-control form-control-sm"
                       placeholder="Dosis">
            </td>

            <td style="width:100px;">
                <input type="number"
                       min="1"
                       value="${i.qty}"
                       onchange="setQty(${index}, this.value)"
                       class="form-control form-control-sm">
            </td>

            <td>
                <button onclick="hapus(${index})"
                        class="btn btn-danger btn-sm">x</button>
            </td>
        </tr>`;

        preview += `<li>${i.nama} (${i.qty} ${i.satuan})</li>`;
    });

    document.getElementById('resepList').innerHTML = html;
    document.getElementById('preview').innerHTML = preview;
}

// ================= UPDATE
window.setQty = function(i,val){
    let v = parseInt(val);
    items[i].qty = (isNaN(v) || v <= 0) ? 1 : v;
    render();
};

window.setDosis = function(i,val){
    items[i].dosis = val;
    render();
};

// 🔥 SET SATUAN
window.setSatuan = function(i, select){

    let selected = select.selectedOptions[0];

    items[i].satuan = selected.value;
    items[i].harga = parseInt(selected.dataset.harga);
    render();
};

// ================= HAPUS
window.hapus = function(i){
    items.splice(i,1);
    render();
};

// ================= FORMAT
function formatRupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka || 0);
}

// ================= SIMPAN (PRODUCTION READY)
document.getElementById('btnSimpan').addEventListener('click', async function(){

    try {

        // ================= VALIDASI
        if(items.length === 0){
            Swal.fire({
                icon:'warning',
                title:'Validasi',
                text:'Obat belum ditambahkan'
            });
            return;
        }

        // ================= BUILD DATA
        const formData = new FormData();

        // hanya kirim jika memang dipakai (opsional)
        const pasien = document.getElementById('pasien')?.value.trim();
        const dokter = document.getElementById('dokter')?.value.trim();

        if(pasien) formData.append('pasien', pasien);
        if(dokter) formData.append('dokter', dokter);

        // 🔥 sanitize data (WAJIB)
        const cleanItems = items.map(i => ({
            id: i.id,
            qty: Number(i.qty) > 0 ? Number(i.qty) : 1,
            dosis: i.dosis || '',
            satuan: i.satuan || '',
            harga: i.harga || 0 // 🔥 WAJIB
        }));

        formData.append('items', JSON.stringify(cleanItems));

        // ================= CSRF SAFE
        if(typeof CSRF_NAME !== 'undefined' && typeof CSRF_HASH !== 'undefined'){
            formData.append(CSRF_NAME, CSRF_HASH);
        }

        // ================= FILE
        if(fileResep){
            formData.append('file', fileResep);
        }

        // ================= LOADING UI
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // ================= FETCH
        const response = await fetch(BASE_URL + 'resep/simpan', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();

        // ================= VALIDASI RESPONSE
        let json;
        try {
            json = JSON.parse(text);
        } catch (e) {
            console.error('❌ RESPONSE BUKAN JSON:', text);
            throw new Error('Server tidak merespon JSON');
        }

        // ================= HANDLE RESPONSE
        if(json.status === 'ok'){

            await Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:'Resep berhasil disimpan'
            });

            window.location.href = BASE_URL + 'resep/detail/' + json.id;

        } else {
            throw new Error(json.message || 'Gagal menyimpan');
        }

    } catch (err) {

        console.error('❌ ERROR:', err);

        Swal.fire({
            icon:'error',
            title:'Gagal',
            text: err.message || 'Terjadi kesalahan server'
        });

    }

});
});