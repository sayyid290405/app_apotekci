<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4"><?= isset($produk) ? '✏️ Edit' : '➕ Tambah' ?> Produk</h4>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>" />

<div class="row">

<!-- NAMA -->
<div class="col-md-6 mb-3">
    <label>Nama Produk</label>
    <input type="text" name="nama_produk" class="form-control"
           value="<?= $produk->nama_produk ?? '' ?>" required>
</div>

<!-- METODE -->
<div class="col-md-6 mb-3">
    <label>Metode Gambar</label>
    <select name="mode_gambar" id="mode_gambar" class="form-control">
        <option value="url">Gunakan URL</option>
        <option value="upload">Upload Gambar</option>
    </select>
</div>

<!-- INPUT URL -->
<div id="input_url" class="col-md-6 mb-3">
    <label>Gambar (URL)</label>
    <input type="text" id="gambar_url" name="gambar_url" class="form-control">
</div>

<!-- INPUT UPLOAD -->
<div class="col-md-6 mb-3" id="input_upload" style="display:none;">
    <label>Upload Gambar</label>

    <div id="drop-area" class="drop-area">
        <p>Drag & Drop gambar di sini</p>
        <p>atau klik untuk memilih</p>
        <input type="file" name="gambar_file" id="fileElem" accept="image/*">
    </div>

    <img id="preview" style="max-height:150px; display:none;" class="mt-2">
    

    <div class="progress mt-2" style="display:none;" id="progressBox">
        <div class="progress-bar bg-success" id="progressBar" style="width:0%">0%</div>
    </div>
</div>

<!-- KATEGORI -->
<div class="col-md-6 mb-3">
    <label>Kategori</label>
    <select name="kategori_id" class="form-control" required>
        <?php foreach($kategori as $k): ?>
        <option value="<?= $k->id_kategori ?>"
            <?= isset($produk) && $produk->kategori_id == $k->id_kategori ? 'selected' : '' ?>>
            <?= $k->nama_kategori ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- SUPPLIER -->
<div class="col-md-6 mb-3">
    <label>Supplier</label>
    <select name="supplier_id" class="form-control" required>
        <?php foreach($supplier as $s): ?>
        <option value="<?= $s->id_supplier ?>"
            <?= isset($produk) && $produk->supplier_id == $s->id_supplier ? 'selected' : '' ?>>
            <?= $s->nama_supplier ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- HARGA -->
<div class="col-md-3 mb-3">
    <label>Harga Beli</label>
    <input type="number" name="harga_beli" class="form-control"
           value="<?= $produk->harga_beli ?? '' ?>" required>
</div>

<div class="row">
    
    <div class="col-md-4 mb-3">
        <label class="fw-bold">Stok (Satuan Terkecil)</label>
        <input type="number" name="stok" class="form-control"
               value="<?= $produk->stok ?? 0 ?>" required>
        <small class="text-muted">Misal: Total tablet/pcs.</small>
    </div>

    <div class="col-md-4 mb-3">
        <label class="fw-bold">Tanggal Kadaluarsa</label>
        <input type="date" name="tanggal_kadaluarsa" class="form-control"
               value="<?= $produk->tanggal_kadaluarsa ?? '' ?>">
    </div>
</div>

<hr>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-success"><i class="fas fa-tags"></i> Pengaturan Satuan & Harga Jual</h5>
        <button type="button" class="btn btn-sm btn-primary" id="add-satuan">
            ➕ Tambah Satuan (Strip/Box)
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-light">
            <thead class="table-dark">
                <tr>
                    <th>Nama Satuan</th>
                    <th>Konversi (Isi)</th>
                    <th>Harga Jual</th>
                    <th width="50px"></th>
                </tr>
            </thead>
            <tbody id="satuan-container">
                <?php if(isset($satuan) && !empty($satuan)): ?>
                    <?php foreach($satuan as $s): ?>
                        <tr>
                            <td><input type="text" name="nama_satuan[]" class="form-control" value="<?= $s->nama_satuan ?>" placeholder="Contoh: Tablet" required></td>
                            <td><input type="number" name="konversi[]" class="form-control" value="<?= $s->konversi ?>" placeholder="Isi" required></td>
                            <td><input type="number" name="harga_satuan[]" class="form-control" value="<?= $s->harga ?>" placeholder="Harga Jual" required></td>
                            <td><button type="button" class="btn btn-danger remove-row">❌</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td><input type="text" name="nama_satuan[]" class="form-control" placeholder="Contoh: Tablet" required></td>
                        <td><input type="number" name="konversi[]" class="form-control" value="1" placeholder="Isi" readonly></td>
                        <td><input type="number" name="harga_satuan[]" class="form-control" placeholder="Harga Jual" required></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <small class="text-danger">* Baris pertama harus satuan terkecil (Konversi = 1).</small>
    </div>
</div>

<!-- STOK -->
<div class="col-md-3 mb-3">
    <label>Stok</label>
    <input type="number" name="stok" class="form-control"
           value="<?= $produk->stok ?? 0 ?>">
</div>

<div class="col-md-3 mb-3">
    <label>Stok Minimal</label>
    <input type="number" name="stok_minimal" class="form-control"
           value="<?= $produk->stok_minimal ?? 5 ?>">
</div>

<!-- KADALUARSA -->
<div class="col-md-6 mb-3">
    <label>Tanggal Kadaluarsa</label>
    <input type="date" name="tanggal_kadaluarsa" class="form-control"
           value="<?= $produk->tanggal_kadaluarsa ?? '' ?>">
</div>

</div>

<div id="loading" style="display:none;" class="text-center mt-3">
    <div class="spinner-border text-success"></div>
    <p class="mt-2">Menyimpan data...</p>
</div>

<!-- BUTTON -->
<div class="mt-3">
    <button type="submit" class="btn btn-success">
        💾 Simpan
    </button>

    <a href="<?= base_url('produk') ?>" class="btn btn-secondary">
        Batal
    </a>
</div>

</form>

<script>
document.addEventListener('click', function (e) {
    // 1. Sesuaikan dengan ID "add-satuan" yang ada di tombol kamu
    if (e.target && (e.target.id === 'add-satuan' || e.target.closest('#add-satuan'))) {
        
        // 2. Sesuaikan dengan ID "satuan-container" yang ada di tbody kamu
        const tabelBody = document.getElementById('satuan-container');
        
        const barisBaru = `
            <tr>
                <td><input type="text" name="nama_satuan[]" class="form-control" placeholder="Strip/Box" required></td>
                <td><input type="number" name="konversi[]" class="form-control" placeholder="Isi" required></td>
                <td><input type="number" name="harga_satuan[]" class="form-control" placeholder="Harga Jual" required></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-hapus-satuan">❌</button></td>
            </tr>`;
        
        tabelBody.insertAdjacentHTML('beforeend', barisBaru);
    }

    // 3. Logika Tombol Hapus (Targetkan class btn-hapus-satuan atau remove-row)
    if (e.target && (e.target.classList.contains('btn-hapus-satuan') || e.target.classList.contains('remove-row'))) {
        e.target.closest('tr').remove();
    }
});

// Script untuk Toggle Gambar
document.getElementById('mode_gambar').onchange = function(){
    let upload = document.getElementById('input_upload');
    let url = document.getElementById('input_url');

    if(this.value === 'upload'){
        upload.style.display = 'block';
        url.style.display = 'none';
    } else {
        upload.style.display = 'none';
        url.style.display = 'block';
    }
};
</script>


<script>
document.getElementById('mode_gambar').onchange = function(){

    let upload = document.getElementById('input_upload');
    let url = document.getElementById('input_url');

    if(this.value === 'upload'){
        upload.style.display = 'block';
        url.style.display = 'none';
    } else {
        upload.style.display = 'none';
        url.style.display = 'block';
    }

};
</script>

</div>
</div>

</div>