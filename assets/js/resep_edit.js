let items = [];

// LOAD DATA AWAL
if(typeof existingItems !== 'undefined'){
    existingItems.forEach(d => {
        items.push({
            id: d.produk_id,
            nama: d.nama_produk,
            qty: parseInt(d.jumlah),
            dosis: d.dosis || ''
        });
    });

    render();
}

// SEARCH
document.getElementById('search').addEventListener('keyup', function(){

    fetch(BASE_URL+'resep/searchProduk?q='+this.value)
    .then(r=>r.json())
    .then(data=>{

        let html = '';

        data.forEach(p=>{
            html += `
            <a href="#" class="list-group-item"
               onclick='tambah(${JSON.stringify(p)})'>
                ${p.nama_produk}
            </a>`;
        });

        document.getElementById('hasil').innerHTML = html;
    });
});

// TAMBAH
function tambah(p){

    let existing = items.find(i=>i.id==p.id_produk);

    if(existing){
        existing.qty++;
    } else {
        items.push({
            id: p.id_produk,
            nama: p.nama_produk,
            qty: 1,
            dosis: ''
        });
    }

    render();
}

// RENDER
function render(){

    let html = '';

    items.forEach((i,index)=>{

        html += `
        <tr>
            <td>${i.nama}</td>

            <td>
                <input type="text"
                       value="${i.dosis}"
                       onchange="setDosis(${index}, this.value)"
                       class="form-control">
            </td>

            <td>
                <input type="number"
                       value="${i.qty}"
                       onchange="setQty(${index}, this.value)"
                       class="form-control">
            </td>

            <td>
                <button onclick="hapus(${index})"
                        class="btn btn-danger">x</button>
            </td>
        </tr>`;
    });

    document.getElementById('resepList').innerHTML = html;
}

// UPDATE
function setQty(i,val){ items[i].qty = parseInt(val); }
function setDosis(i,val){ items[i].dosis = val; }

// HAPUS
function hapus(i){
    items.splice(i,1);
    render();
}

// UPDATE RESEP
document.getElementById('btnUpdate').addEventListener('click', function(){

    let id = document.getElementById('resep_id').value;

    let formData = new FormData();
    formData.append('id', id);
    formData.append('items', JSON.stringify(items));
    formData.append(CSRF_NAME, CSRF_HASH);

    fetch(BASE_URL+'resep/update',{
        method:'POST',
        body: formData
    })
    .then(r=>r.json())
    .then(res=>{
        if(res.status=='ok'){
            Swal.fire('Berhasil','Resep diupdate','success')
            .then(()=>{
                window.location.href = BASE_URL+'resep/preview/'+id;
            });
        }
    });
});