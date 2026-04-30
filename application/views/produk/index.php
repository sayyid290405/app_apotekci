<div class="container-fluid">
    <?php if($this->session->flashdata('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $this->session->flashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
<<<<<<< HEAD
    text: '<?= $this->session->flashdata('error') ?>'
=======
    text: '<?= $this->session->flashdata('error') ?>',
    confirmButtonText: 'OK'
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
});
</script>
<?php endif; ?>

<<<<<<< HEAD
<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">📦 Data Produk</h4>

    <a href="<?= base_url('produk/tambah') ?>" class="btn btn-success">
        ➕ Tambah Produk
    </a>
</div>

<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text" id="searchProduk" name="q" value="<?= $keyword ?? '' ?>" 
               class="form-control" placeholder="🔍 Cari Produk...">
        <button class="btn btn-success">Cari</button>
    </div>
</form>

<!-- TABLE -->
<div class="card shadow-sm border-0">
<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-success">
<tr>
    <th>#</th>
    <th>Gambar</th>
    <th>Nama Produk</th>
    <th>Kategori</th>
    <th>Supplier</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Kadaluarsa</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody id="produkTable">

<?php $no=1; foreach($produk as $p): ?>

<tr>

<td><?= $no++ ?></td>

<td>
    <img src="<?= !empty($p->gambar) ? $p->gambar : 'https://via.placeholder.com/80' ?>"
         style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
</td>

<td>
    <strong><?= $p->nama_produk ?></strong>
</td>

<td><?= $p->nama_kategori ?></td>

<td><?= $p->nama_supplier ?></td>

<td>
    <div class="text-success fw-bold">
        Rp <?= number_format($p->harga_jual,0,',','.') ?>
    </div>
    <small class="text-muted">
        Beli: Rp <?= number_format($p->harga_beli,0,',','.') ?>
    </small>
</td>

<td>
    <?php if($p->stok <= $p->stok_minimal): ?>
        <span class="badge bg-danger">⚠ <?= $p->stok ?></span>
    <?php else: ?>
        <span class="badge bg-success"><?= $p->stok ?></span>
    <?php endif; ?>
</td>

<td>
    <?php if($p->tanggal_kadaluarsa && $p->tanggal_kadaluarsa <= date('Y-m-d')): ?>
        <span class="badge bg-warning text-dark">Kadaluarsa</span>
    <?php else: ?>
        <span class="text-muted">
            <?= $p->tanggal_kadaluarsa ?? '-' ?>
        </span>
    <?php endif; ?>
</td>

<td class="text-center">

    <a href="<?= base_url('produk/edit/'.$p->id_produk) ?>"
       class="btn btn-sm btn-warning">
       ✏️
    </a>

    <button class="btn btn-danger btn-sm"
onclick="hapus('<?= base_url('produk/hapus/'.$p->id_produk) ?>')">
🗑️
</button>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
</div>
</div>

</div>
=======
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">📦 Data Produk</h4>
        <a href="<?= base_url('produk/tambah') ?>" class="btn btn-success">
            ➕ Tambah Produk
        </a>
    </div>

    <form onsubmit="return false;" class="mb-3">
        <div class="input-group">
           <input type="text" id="searchProduk" class="form-control" placeholder="Cari produk...">
            <button class="btn btn-success">Cari</button>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Supplier</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Kadaluarsa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="produkTable">
                        <?php $no=1; foreach($produk as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <img src="<?= get_gambar($p->gambar) ?>"
     onerror="this.onerror=null; this.src='<?= base_url('assets/no-image.png') ?>';"
     width="80">
                            </td>
                            <td>
                                <strong><?= $p->nama_produk ?></strong><br>
                                <small class="text-muted">ID: #<?= $p->id_produk ?></small>
                            </td>
                            <td><?= $p->nama_kategori ?></td>
                            <td><?= $p->nama_supplier ?></td>
                            <td>
                                <div class="text-success fw-bold">
                                    Rp <?= number_format($p->harga_tampil ?? $p->harga_jual, 0, ',', '.') ?>
                                    <small class="text-muted">
                                    <?php if(!empty($p->satuan)): ?>
                                        <?php foreach($p->satuan as $s): ?>
                                            <?= $s->nama_satuan ?> (Rp <?= number_format($s->harga) ?>),
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </small>
                                </div>
                                <small class="text-muted">Beli: Rp <?= number_format($p->harga_beli, 0, ',', '.') ?></small>
                            </td>
                            <td>
                                <?php if($p->stok <= $p->stok_minimal): ?>
                                    <span class="badge bg-danger">⚠ <?= $p->stok ?> <?= $p->satuan_tampil ?? $p->satuan_dasar ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $p->stok ?> <?= $p->satuan_tampil ?? $p->satuan_dasar ?></span>
                                <?php endif; ?>
                                <br><small class="text-muted">Min: <?= $p->stok_minimal ?></small>
                            </td>
                            <td>
                                <?php 
                                $today = date('Y-m-d');
                                if($p->tanggal_kadaluarsa && $p->tanggal_kadaluarsa <= $today): ?>
                                    <span class="badge bg-danger">Kadaluarsa</span>
                                <?php elseif($p->tanggal_kadaluarsa && $p->tanggal_kadaluarsa <= date('Y-m-d', strtotime('+3 months'))): ?>
                                    <span class="badge bg-warning text-dark"><?= $p->tanggal_kadaluarsa ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><?= $p->tanggal_kadaluarsa ?? '-' ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?= base_url('produk/edit/'.$p->id_produk) ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <button class="btn btn-danger btn-sm" onclick="hapus('<?= base_url('produk/hapus/'.$p->id_produk) ?>')">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/produk.js') ?>"></script>
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
