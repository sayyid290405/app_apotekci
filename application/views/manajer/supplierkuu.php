<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between mb-3">
    <h4>📦 Data Supplier</h4>
    

    <!-- <a href="<?= base_url('supplier/tambah') ?>" class="btn btn-success">
        ➕ Tambah Supplier
    </a> -->
</div>

<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" value="<?= $keyword ?? '' ?>" 
               class="form-control" placeholder="🔍 Cari supplier...">
        <button class="btn btn-success">Cari</button>
    </div>
</form>

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>#</th>
    <th>Nama</th>
    <th>Legalitas</th>
    <th>Alamat</th>
    <th>Kontak</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody id="supplierTable">

<?php $no=1; foreach($supplier as $s): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><strong><?= $s->nama_supplier ?></strong></td>
    <td><?= $s->legalitas ?></td>
    <td><?= $s->alamat ?></td>
    <td><?= $s->kontak ?></td>
    <td>
    <!-- DETAIL -->
    <a href="<?= base_url('supplier/detail_manajer/'.$s->id_supplier) ?>" 
       class="btn btn-info btn-sm" title="Detail">
        👁️
    </a>

    <!-- EDIT -->
    <!-- <a href="<?= base_url('supplier/edit/'.$s->id_supplier) ?>" 
       class="btn btn-warning btn-sm">
        ✏️
    </a> -->

    <!-- HAPUS -->
    <!-- <button onclick="hapus('<?= base_url('supplier/hapus/'.$s->id_supplier) ?>')" 
            class="btn btn-danger btn-sm">
        🗑️
    </button> -->
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
<div class="mt-3">
    <?= $pagination ?>
</div>

</div>
</div>

</div>