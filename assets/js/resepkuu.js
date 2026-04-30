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

// ================= TAMBAH OBAT
window.tambah = function(p){

    let exist = items.find(i => i.id == p.id_produk);

    if(exist){
        exist.qty++;
    } else {
        items.push({
            id: p.id_produk,
            nama: p.nama_produk,
            qty: 1,
            dosis: ''
        });
    }

    hasil.innerHTML = '';
    search.value = '';

    render();
};

// ================= RENDER TABLE
function render(){

    let html = '';
    let preview = '';

    items.forEach((i,index)=>{

        html += `
        <tr>
            <td>${i.nama}</td>

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

        preview += `<li>${i.nama} (${i.qty})</li>`;
    });

    document.getElementById('resepList').innerHTML = html;
    document.getElementById('preview').innerHTML = preview;
}

// ================= UPDATE
window.setQty = function(i,val){
    let v = parseInt(val);
    items[i].qty = (isNaN(v) || v <= 0) ? 1 : v;
};

window.setDosis = function(i,val){
    items[i].dosis = val;
};

// ================= HAPUS
window.hapus = function(i){
    items.splice(i,1);
    render();
};

// ================= SIMPAN
document.getElementById('btnSimpan').addEventListener('click', function(){

    let pasien = document.getElementById('pasien').value.trim();
    let dokter = document.getElementById('dokter').value.trim();

    if(!pasien){
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Nama pasien wajib diisi'
        });
        return;
    }

    if(items.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Validasi',
            text: 'Obat belum ditambahkan'
        });
        return;
    }

    let formData = new FormData();

    formData.append('pasien', pasien);
    formData.append('dokter', dokter);
    formData.append('items', JSON.stringify(items));

    // 🔥 CSRF WAJIB
    formData.append(CSRF_NAME, CSRF_HASH);

    // 🔥 FILE (OPSIONAL)
    let fileInput = document.getElementById('file');
    if(fileInput && fileInput.files.length > 0){
        formData.append('file', fileInput.files[0]);
    }

    // 🔥 LOADING
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(BASE_URL + 'resep/simpan', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(res => {

        console.log("RESPONSE:", res);

        let json;

        try {
            json = JSON.parse(res);
        } catch(e){
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Response bukan JSON (kemungkinan CSRF / server error)'
            });
            return;
        }

        if(json.status === 'ok'){

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Resep berhasil disimpan',
                confirmButtonText: 'Lihat Preview'
            }).then(() => {

                // 🔥 REDIRECT KE PREVIEW
                window.location.href = BASE_URL + 'resep/detail/' + json.id;

            });

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: json.message || 'Terjadi kesalahan'
            });

        }

    })
    .catch(err => {
        console.error(err);

        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Gagal terhubung ke server'
        });
    });

});


});