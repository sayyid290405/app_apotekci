<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4"><?= isset($produk) ? '✏️ Edit' : '➕ Tambah' ?> Produk</h4>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" id="formProduk">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>" />

<div class="row">

<!-- NAMA PRODUK -->
<div class="col-md-6 mb-3">
    <label>Nama Produk <span class="text-danger">*</span></label>
    <input type="text" name="nama_produk" class="form-control"
           value="<?= $produk->nama_produk ?? '' ?>" required>
</div>

<!-- METODE GAMBAR -->
<div class="col-md-6 mb-3">
    <label>Metode Gambar</label>
    <select name="mode_gambar" id="mode_gambar" class="form-control">
        <option value="url" <?= isset($produk) && !empty($produk->gambar) && filter_var($produk->gambar, FILTER_VALIDATE_URL) ? 'selected' : '' ?>>Gunakan URL</option>
        <option value="upload" <?= isset($produk) && !empty($produk->gambar) && !filter_var($produk->gambar, FILTER_VALIDATE_URL) ? 'selected' : '' ?>>Upload Gambar</option>
        <?php if(isset($produk) && !empty($produk->gambar)): ?>
        <option value="remove">Hapus Gambar</option>
        <?php endif; ?>
    </select>
</div>

<!-- INPUT URL -->
<div id="input_url" class="col-md-6 mb-3">
    <label>Gambar (URL)</label>
    <input type="text" name="gambar_url" class="form-control"
           value="<?= isset($produk) && filter_var($produk->gambar, FILTER_VALIDATE_URL) ? $produk->gambar : '' ?>" 
           placeholder="https://example.com/gambar.jpg">
    <small class="text-muted">Masukkan URL gambar dari internet</small>
</div>

<!-- INPUT UPLOAD -->
<div class="col-md-6 mb-3" id="input_upload" style="display:none;">
    <label>Upload Gambar</label>
    <div id="drop-area" class="drop-area">
        <p><i class="fas fa-cloud-upload-alt fa-2x"></i></p>
        <p>Drag & Drop gambar di sini</p>
        <p>atau klik untuk memilih</p>
        <input type="file" name="gambar_file" id="fileElem" accept="image/*">
    </div>
    
    <div id="preview-container" class="mt-2" style="display:none;">
        <img id="preview" style="max-height:150px; border-radius:8px; border:1px solid #ddd; padding:5px;">
        <button type="button" class="btn btn-sm btn-danger mt-1" id="removePreview">✖ Hapus</button>
    </div>
    
    <?php if(isset($produk) && !empty($produk->gambar) && !filter_var($produk->gambar, FILTER_VALIDATE_URL)): ?>
    <div class="mt-2">
        <small>Gambar saat ini:</small>
        <img src="<?= base_url('uploads/'.$produk->gambar) ?>" style="max-height:100px; border-radius:5px; display:block; margin-top:5px;">
    </div>
    <?php endif; ?>
    
    <div class="progress mt-2" style="display:none;" id="progressBox">
        <div class="progress-bar bg-success" id="progressBar" style="width:0%">0%</div>
    </div>
</div>

<!-- KATEGORI -->
<div class="col-md-6 mb-3">
    <label>Kategori <span class="text-danger">*</span></label>
    <select name="kategori_id" class="form-control" required>
        <option value="">-- Pilih Kategori --</option>
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
    <label>Supplier <span class="text-danger">*</span></label>
    <select name="supplier_id" class="form-control" required>
        <option value="">-- Pilih Supplier --</option>
        <?php foreach($supplier as $s): ?>
        <option value="<?= $s->id_supplier ?>"
            <?= isset($produk) && $produk->supplier_id == $s->id_supplier ? 'selected' : '' ?>>
            <?= $s->nama_supplier ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

</div>

<hr>

<!-- ==================== BAGIAN KONVERSI ==================== -->
<h5 class="fw-bold mb-3"><i class="fas fa-exchange-alt text-primary"></i> Konversi Satuan</h5>

<div class="row">
    <!-- HARGA BELI DARI SUPPLIER -->
    <div class="col-md-3 mb-3">
        <label class="fw-bold">💰 Harga Beli (1 Pack/Box) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="harga_beli" id="harga_beli" class="form-control"
                   value="<?= $produk->harga_beli ?? '' ?>" required>
        </div>
        <small class="text-muted">Harga 1 Pack/Box dari supplier</small>
    </div>

    <!-- ISI PER PACK -->
    <div class="col-md-3 mb-3">
        <label class="fw-bold">📦 Isi 1 Pack/Box <span class="text-danger">*</span></label>
        <input type="number" name="isi_per_unit" id="isi_per_unit" class="form-control" 
               value="<?= $produk->isi_per_unit ?? 100 ?>" required>
        <small class="text-muted">Jumlah satuan terkecil dalam 1 pack</small>
    </div>

    <!-- SATUAN TERKECIL -->
    <div class="col-md-3 mb-3">
        <label class="fw-bold">💊 Satuan Terkecil <span class="text-danger">*</span></label>
        <input type="text" name="satuan_dasar" id="satuan_dasar" class="form-control"
               value="<?= $produk->satuan_dasar ?? 'Tablet' ?>" required>
        <small class="text-muted">Contoh: Tablet, Kapsul, Pcs, Botol</small>
    </div>

    <!-- STOK AWAL -->
    <div class="col-md-3 mb-3">
        <label class="fw-bold">📊 Stok Awal</label>
        <input type="number" name="stok" id="stok" class="form-control"
               value="<?= $produk->stok ?? 0 ?>">
        <small class="text-muted">Dalam satuan <span id="label_stok">Tablet</span></small>
    </div>
</div>

<!-- HASIL PERHITUNGAN OTOMATIS -->
<div class="alert alert-success mt-2">
    <div class="row">
        <div class="col-md-3">
            <small class="text-muted">Harga per <span id="label_dasar">Tablet</span> (Modal)</small>
            <h5 class="mb-0" id="harga_per_unit">Rp 0</h5>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Harga Jual per <span id="label_dasar2">Tablet</span> (+20%)</small>
            <h5 class="mb-0 text-success" id="harga_jual_dasar">Rp 0</h5>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Harga Jual per Strip (10 <span id="label_dasar3">Tablet</span>)</small>
            <h5 class="mb-0" id="harga_jual_strip">Rp 0</h5>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Harga Jual per Box/Pack</small>
            <h5 class="mb-0" id="harga_jual_pack">Rp 0</h5>
        </div>
    </div>
    <hr class="my-2">
    <small>
        <i class="fas fa-info-circle"></i> 
        <strong>Cara Konversi Stok:</strong> Beli 1 Pack = Stok bertambah <span id="tambah_stok">100</span> <span id="label_dasar4">Tablet</span>
    </small>
</div>

<hr>

<!-- ==================== DAFTAR SATUAN UNTUK DIJUAL ==================== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="fas fa-tags text-success"></i> Satuan untuk Dijual</h5>
    <button type="button" class="btn btn-sm btn-primary" id="add-satuan">
        ➕ Tambah Satuan
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width:30%">Nama Satuan <span class="text-danger">*</span></th>
                <th style="width:25%">Konversi (ke <span id="th_dasar">Tablet</span>) <span class="text-danger">*</span></th>
                <th style="width:30%">Harga Jual <span class="text-danger">*</span></th>
                <th style="width:15%"></th>
            </tr>
        </thead>
        <tbody id="satuan-container">
            <?php if(isset($satuan) && !empty($satuan)): ?>
                <?php 
                $sorted_satuan = $satuan;
                usort($sorted_satuan, function($a, $b) {
                    return $a->konversi - $b->konversi;
                });
                foreach($sorted_satuan as $s): 
                ?>
                <tr>
                    <td><input type="text" name="nama_satuan[]" class="form-control nama-satuan" value="<?= $s->nama_satuan ?>" required></td>
                    <td><input type="number" name="konversi[]" class="form-control konversi" value="<?= $s->konversi ?>" <?= $s->konversi == 1 ? 'readonly style="background:#e9ecef;"' : '' ?> required></td>
                    <td><input type="number" name="harga_satuan[]" class="form-control harga-satuan" value="<?= $s->harga ?>" <?= $s->konversi == 1 ? 'readonly style="background:#e9ecef;"' : '' ?> required></td>
                    <td class="text-center">
                        <?php if($s->konversi != 1): ?>
                            <button type="button" class="btn btn-danger btn-sm hapus-satuan">✖</button>
                        <?php else: ?>
                            <span class="badge bg-secondary">Dasar</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Satuan Dasar -->
                <tr id="row_dasar">
                    <td><input type="text" name="nama_satuan[]" class="form-control nama-satuan" value="Tablet" required></td>
                    <td><input type="number" name="konversi[]" class="form-control konversi" value="1" readonly style="background:#e9ecef;"></td>
                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_dasar" class="form-control harga-satuan" readonly style="background:#e9ecef;"></td>
                    <td class="text-center"><span class="badge bg-secondary">Dasar</span></td>
                </tr>
                <!-- Strip -->
                <tr>
                    <td><input type="text" name="nama_satuan[]" class="form-control nama-satuan" value="Strip" required></td>
                    <td><input type="number" name="konversi[]" class="form-control konversi" value="10" required></td>
                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_strip" class="form-control harga-satuan" readonly></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-satuan">✖</button></td>
                </tr>
                <!-- Box -->
                <tr>
                    <td><input type="text" name="nama_satuan[]" class="form-control nama-satuan" value="Box" required></td>
                    <td><input type="number" name="konversi[]" id="konversi_box" class="form-control konversi" value="100" required></td>
                    <td><input type="number" name="harga_satuan[]" id="harga_satuan_box" class="form-control harga-satuan" readonly></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-satuan">✖</button></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<small class="text-muted">* Baris dengan konversi = 1 adalah satuan dasar (TIDAK BISA DIHAPUS)</small>

<!-- STOK MINIMAL & KADALUARSA -->
<div class="row mt-3">
    <div class="col-md-3">
        <label>Stok Minimal (Peringatan)</label>
        <input type="number" name="stok_minimal" class="form-control" value="<?= $produk->stok_minimal ?? 5 ?>">
    </div>
    <div class="col-md-3">
        <label>Tanggal Kadaluarsa</label>
        <input type="date" name="tanggal_kadaluarsa" class="form-control" value="<?= $produk->tanggal_kadaluarsa ?? '' ?>">
    </div>
    <div class="col-md-3">
        <label>Harga Jual (Satuan Dasar)</label>
        <input type="number" name="harga_jual" id="harga_jual" class="form-control" value="<?= $produk->harga_jual ?? '' ?>">
        <small class="text-muted">Akan dihitung otomatis</small>
    </div>
</div>

<!-- BUTTON -->
<div class="mt-4">
    <button type="submit" class="btn btn-success px-4">💾 Simpan</button>
    <a href="<?= base_url('produk') ?>" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== TOGGLE GAMBAR ====================
    const modeGambar = document.getElementById('mode_gambar');
    const inputUrl = document.getElementById('input_url');
    const inputUpload = document.getElementById('input_upload');
    const fileElem = document.getElementById('fileElem');
    const previewContainer = document.getElementById('preview-container');
    const preview = document.getElementById('preview');
    const removePreview = document.getElementById('removePreview');
    
    function toggleGambar() {
        if (modeGambar.value === 'upload') {
            inputUrl.style.display = 'none';
            inputUpload.style.display = 'block';
        } else if (modeGambar.value === 'url') {
            inputUrl.style.display = 'block';
            inputUpload.style.display = 'none';
        } else if (modeGambar.value === 'remove') {
            inputUrl.style.display = 'none';
            inputUpload.style.display = 'none';
        }
    }
    
    if (modeGambar) {
        modeGambar.addEventListener('change', toggleGambar);
        toggleGambar();
    }
    
    // ==================== DRAG & DROP ====================
    const dropArea = document.getElementById('drop-area');
    
    if (dropArea) {
        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#28a745';
            this.style.background = '#f0fff0';
        });
        
        dropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ccc';
            this.style.background = '#f8f9fa';
        });
        
        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ccc';
            this.style.background = '#f8f9fa';
            
            if (e.dataTransfer.files.length) {
                fileElem.files = e.dataTransfer.files;
                previewFile(fileElem.files[0]);
            }
        });
        
        dropArea.addEventListener('click', function() {
            fileElem.click();
        });
        
        fileElem.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                previewFile(this.files[0]);
            }
        });
        
        if (removePreview) {
            removePreview.addEventListener('click', function() {
                previewContainer.style.display = 'none';
                preview.src = '';
                fileElem.value = '';
            });
        }
    }
    
    function previewFile(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
    
    // ==================== AUTO KALKULASI ====================
    const hargaBeli = document.getElementById('harga_beli');
    const isiPerUnit = document.getElementById('isi_per_unit');
    const satuanDasar = document.getElementById('satuan_dasar');
    const hargaJualInput = document.getElementById('harga_jual');
    
    const labelDasar = document.querySelectorAll('#label_dasar, #label_dasar2, #label_dasar3, #label_dasar4, #th_dasar, #label_stok');
    const hargaPerUnitEl = document.getElementById('harga_per_unit');
    const hargaJualDasarEl = document.getElementById('harga_jual_dasar');
    const hargaJualStripEl = document.getElementById('harga_jual_strip');
    const hargaJualPackEl = document.getElementById('harga_jual_pack');
    const tambahStokEl = document.getElementById('tambah_stok');
    
    function updateSemuaHargaSatuan(hargaDasar) {
        let rows = document.querySelectorAll('#satuan-container tr');
        
        rows.forEach(row => {
            let konversiInput = row.querySelector('.konversi');
            let hargaInput = row.querySelector('.harga-satuan');
            
            if (konversiInput && hargaInput && !hargaInput.readOnly) {
                let konversi = parseFloat(konversiInput.value) || 1;
                let hargaBaru = Math.round(hargaDasar * konversi);
                hargaInput.value = hargaBaru;
            }
        });
    }
    
    function hitungSemua() {
        let beli = parseFloat(hargaBeli.value) || 0;
        let isi = parseFloat(isiPerUnit.value) || 1;
        let dasar = satuanDasar.value || 'Tablet';
        
        labelDasar.forEach(el => { if(el) el.innerText = dasar; });
        
        let hargaPerSatuanTerkecil = beli / isi;
        hargaPerUnitEl.innerText = 'Rp ' + Math.round(hargaPerSatuanTerkecil).toLocaleString('id-ID');
        
        let hargaJual = Math.round(hargaPerSatuanTerkecil * 1.2);
        hargaJualDasarEl.innerText = 'Rp ' + hargaJual.toLocaleString('id-ID');
        
        if (hargaJualInput) hargaJualInput.value = hargaJual;
        
        let hargaStrip = Math.round(hargaJual * 10);
        hargaJualStripEl.innerText = 'Rp ' + hargaStrip.toLocaleString('id-ID');
        
        let hargaPack = Math.round(hargaJual * isi);
        hargaJualPackEl.innerText = 'Rp ' + hargaPack.toLocaleString('id-ID');
        
        tambahStokEl.innerText = isi;
        
        // Update satuan dasar
        let hargaDasarInput = document.getElementById('harga_satuan_dasar');
        if (hargaDasarInput) hargaDasarInput.value = hargaJual;
        
        updateSemuaHargaSatuan(hargaJual);
    }
    
    if (hargaBeli) hargaBeli.addEventListener('input', hitungSemua);
    if (isiPerUnit) isiPerUnit.addEventListener('input', hitungSemua);
    if (satuanDasar) satuanDasar.addEventListener('input', hitungSemua);
    
    hitungSemua();
    
    // ==================== TAMBAH SATUAN ====================
    const addBtn = document.getElementById('add-satuan');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            let tbody = document.getElementById('satuan-container');
            let hargaJualText = document.getElementById('harga_jual_dasar')?.innerText || 'Rp 0';
            let hargaDasar = parseFloat(hargaJualText.replace(/[^0-9]/g, '')) || 0;
            
            let newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="nama_satuan[]" class="form-control" placeholder="Contoh: Dus" required></td>
                <td><input type="number" name="konversi[]" class="form-control konversi" placeholder="Isi" required></td>
                <td><input type="number" name="harga_satuan[]" class="form-control harga-satuan" placeholder="Otomatis" readonly style="background:#e9ecef;"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-satuan">✖</button></td>
            `;
            tbody.appendChild(newRow);
            
            let konversiInput = newRow.querySelector('.konversi');
            let hargaInput = newRow.querySelector('.harga-satuan');
            
            konversiInput.addEventListener('input', function() {
                let hargaJualText = document.getElementById('harga_jual_dasar')?.innerText || 'Rp 0';
                let hargaDasar = parseFloat(hargaJualText.replace(/[^0-9]/g, '')) || 0;
                hargaInput.value = Math.round(hargaDasar * (parseFloat(this.value) || 1));
            });
            
            let event = new Event('input');
            konversiInput.dispatchEvent(event);
        });
    }
    
    // ==================== HAPUS SATUAN ====================
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('hapus-satuan')) {
            let row = e.target.closest('tr');
            let konversiInput = row.querySelector('.konversi');
            let konversi = konversiInput ? parseFloat(konversiInput.value) : 0;
            
            if (konversi == 1) {
                Swal.fire('Peringatan', 'Satuan dasar (konversi = 1) tidak boleh dihapus!', 'warning');
                return;
            }
            row.remove();
        }
    });
    
    // ==================== UPDATE KONVERSI ====================
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('konversi') && !e.target.readOnly) {
            let hargaJualText = document.getElementById('harga_jual_dasar')?.innerText || 'Rp 0';
            let hargaDasar = parseFloat(hargaJualText.replace(/[^0-9]/g, '')) || 0;
            
            let hargaInput = e.target.closest('tr').querySelector('.harga-satuan');
            if (hargaInput) {
                hargaInput.value = Math.round(hargaDasar * (parseFloat(e.target.value) || 1));
            }
        }
    });
    
    // ==================== VALIDASI SUBMIT ====================
    const form = document.getElementById('formProduk');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasDasar = false;
            let konversiInputs = document.querySelectorAll('.konversi');
            
            konversiInputs.forEach(input => {
                if (parseFloat(input.value) == 1) hasDasar = true;
            });
            
            if (!hasDasar) {
                e.preventDefault();
                Swal.fire('Error', 'Harus ada satuan dengan Konversi = 1 (satuan dasar)!', 'error');
                return false;
            }
            
            let btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }
        });
    }
    
    // ==================== INISIALISASI ====================
    setTimeout(function() {
        let hargaJualText = document.getElementById('harga_jual_dasar')?.innerText || 'Rp 0';
        let hargaDasar = parseFloat(hargaJualText.replace(/[^0-9]/g, '')) || 0;
        updateSemuaHargaSatuan(hargaDasar);
    }, 100);
    
});
</script>

<style>
    .konversi:read-only, .harga-satuan:read-only {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }
    
    .drop-area {
        border: 2px dashed #ccc;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .drop-area:hover {
        border-color: #28a745;
        background: #f0fff0;
    }
    
    .drop-area input[type="file"] {
        display: none;
    }
    
    #preview-container {
        position: relative;
        display: inline-block;
    }
    
    #removePreview {
        position: absolute;
        top: -8px;
        right: -8px;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        padding: 0;
        font-size: 12px;
        line-height: 1;
    }
</style>