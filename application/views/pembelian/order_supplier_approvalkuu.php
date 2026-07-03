<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manajemen Pengadaan Barang</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.status-select {
    cursor: pointer;
    border-radius: 20px !important;
    font-weight: 600;
    text-align: center;
}
</style>
</head>

<body class="bg-light">

<div class="container py-5">

<div class="card shadow-sm border-0">
<div class="card-body">

<h3 class="mb-4">📦 List Permohonan Pengadaan Barang</h3>

<!-- 🔍 FILTER -->
<form method="get" class="mb-3 d-flex gap-2 align-items-center">

    <select name="filter" class="form-select w-auto">
        <option value="">-- Semua Status --</option>
        <option value="menunggu" <?= $this->input->get('filter')=='menunggu'?'selected':'' ?>>Menunggu</option>
        <option value="disetujui" <?= $this->input->get('filter')=='disetujui'?'selected':'' ?>>Disetujui</option>
        <option value="diproses" <?= $this->input->get('filter')=='diproses'?'selected':'' ?>>Diproses</option>
        <option value="selesai" <?= $this->input->get('filter')=='selesai'?'selected':'' ?>>Selesai</option>
        <option value="ditolak" <?= $this->input->get('filter')=='ditolak'?'selected':'' ?>>Ditolak</option>
    </select>

    <button class="btn btn-primary btn-sm">Filter</button>

    <a href="<?= base_url('Pembelian/approval_pembelian/') ?>" class="btn btn-secondary btn-sm">Reset</a>

</form>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Kode</th>
    <th>Supplier</th>
    <th>Tanggal</th>
    <th>Produk</th>
    <th>Total</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php if(empty($pembelian)): ?>
<tr>
<td colspan="8" class="text-center">Data kosong</td>
</tr>
<?php endif; ?>

<?php $no=1; foreach($pembelian as $p): ?>

<tr>
<td><?= $no++ ?></td>
<td class="fw-bold text-primary"><?= $p->kode_pembelian ?></td>
<td><?= $p->nama_supplier ?></td>
<td><?= date('d M Y', strtotime($p->tanggal)) ?></td>
<td><?= $p->nama_produk ?></td>
<td>Rp <?= number_format($p->total,0,',','.') ?></td>

<td>
<form method="post" action="<?= base_url('Pembelian/update_status') ?>">

<input type="hidden"
name="<?= $this->security->get_csrf_token_name(); ?>"
value="<?= $this->security->get_csrf_hash(); ?>">

<input type="hidden" name="id_pembelian" value="<?= $p->id_pembelian ?>">

<?php
$isLocked = in_array($p->status, ['diproses','selesai','diterima']);

$class = 'bg-warning text-dark';

if($p->status == 'disetujui') {
    $class = 'bg-success text-white';
}
elseif($p->status == 'ditolak') {
    $class = 'bg-danger text-white';
}
elseif($p->status == 'diproses') {
    $class = 'bg-info text-white';
}
elseif($p->status == 'selesai') {
    $class = 'bg-primary text-white';
}
?>

<?php if($isLocked): ?>

<span class="badge <?= $class ?>">
    <?= ucfirst($p->status) ?>
</span>

<?php else: ?>

<select
name="status"
class="form-select form-select-sm status-select <?= $class ?>"
onchange="this.form.submit()">

<option value="menunggu"
<?= $p->status=='menunggu'?'selected':'' ?>>
Menunggu
</option>

<option value="disetujui"
<?= $p->status=='disetujui'?'selected':'' ?>>
Disetujui
</option>

<option value="ditolak"
<?= $p->status=='ditolak'?'selected':'' ?>>
Ditolak
</option>

</select>

<?php endif; ?>

</form>
</td>

<td>
<a href="<?= base_url('pembelian/detail_manajer/'.$p->id_pembelian) ?>" class="btn btn-info btn-sm text-white">
<i class="fas fa-eye"></i>
</a>

<?php if(!in_array($p->status,['diproses','selesai'])): ?>

<a href="<?= base_url('pembelian/edit_pembelian/'.$p->id_pembelian) ?>" class="btn btn-warning btn-sm">Edit</a>

<a href="<?= base_url('pembelian/delete/'.$p->id_pembelian) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>

<?php else: ?>

<button class="btn btn-secondary btn-sm" disabled>Edit</button>
<button class="btn btn-secondary btn-sm" disabled>Hapus</button>

<?php endif; ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

</div>
</div>
</div>

</body>
</html>