<div class="container-fluid">

<div class="card shadow-sm border-0 mb-3">
<div class="card-body">

<h4>🏢 <?= $supplier->nama_supplier ?></h4>

<div class="row">
    <div class="col-md-6">
        <p><strong>Legalitas:</strong><br><?= $supplier->legalitas ?></p>
        <p><strong>Kontak:</strong><br><?= $supplier->kontak ?></p>
    </div>

    <div class="col-md-6">
        <p><strong>Alamat:</strong><br><?= $supplier->alamat ?></p>
    </div>
</div>

<a href="<?= base_url('supplier/manajer') ?>" class="btn btn-secondary mt-2">
    ← Kembali
</a>

</div>
</div>

<!-- PRODUK -->
<div class="card shadow-sm border-0">
<div class="card-body">

<h5>📦 Produk dari Supplier ini</h5>

<?php if(empty($produk)): ?>
    <div class="alert alert-warning">Belum ada produk</div>
<?php else: ?>

<table class="table table-hover">
<thead>
<tr>
    <th>Gambar</th>
    <th>Nama</th>
    <th>Harga</th>
    <th>Stok</th>
</tr>
</thead>

<tbody>
<?php foreach($produk as $p): ?>
<tr>
    <td>
        <img src="<?= $p->gambar ?>" style="height:50px;">
    </td>
    <td><?= $p->nama_produk ?></td>

    <!-- HARGA BELI -->
    <td>
        <span class="text-primary fw-bold">
            Rp <?= number_format($p->harga_beli,0,',','.') ?>
        </span>
        <br>
        <small class="text-muted">
            Jual: Rp <?= number_format($p->harga_jual,0,',','.') ?>
        </small>
    </td>

    <td>
        <span class="badge bg-success"><?= $p->stok ?></span>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

</table>

<?php endif; ?>

</div>
</div>

</div>