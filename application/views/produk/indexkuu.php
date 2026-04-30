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
    text: '<?= $this->session->flashdata('error') ?>'
});
</script>
<?php endif; ?>

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