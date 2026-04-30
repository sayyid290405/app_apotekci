<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat</title>
</head>
<body>

<h2>Laporan Stok Obat</h2>
<p>Total Stok: <b><?= $total_stok ?></b></p>

<!-- ================= SEMUA PRODUK ================= -->
<h3>Semua Data Obat</h3>
<table border="1">
<tr>
    <th>No</th>
    <th>Nama Obat</th>
    <th>Stok</th>
    <th>Tanggal Kadaluarsa</th>
    <th>Supplier</th>
</tr>
<?php $no=1; foreach($produk as $p): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $p->nama_produk ?></td>
    <td><?= $p->stok ?></td>
    <td><?= $p->tanggal_kadaluarsa ?></td>
    <td><?= isset($p->nama_supplier) ? $p->nama_supplier : '-' ?></td>
</tr>
<?php endforeach; ?>
</table>

<br><br>

<!-- ================= STOK MINIM ================= -->
<h3>Stok Menipis ( < 5 )</h3>
<table border="1">
<tr>
    <th>No</th>
    <th>Nama Obat</th>
    <th>Stok</th>
</tr>
<?php $no=1; foreach($stok_minim as $s): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $s->nama_produk ?></td>
    <td><?= $s->stok ?></td>
</tr>
<?php endforeach; ?>
</table>

<br><br>

<!-- ================= KADALUARSA ================= -->
<h3>Obat Mendekati Kadaluarsa (≤ 30 Hari)</h3>
<table border="1">
<tr>
    <th>No</th>
    <th>Nama Obat</th>
    <th>Tanggal Kadaluarsa</th>
    <th>Sisa Hari</th>
</tr>
<?php $no=1; foreach($kadaluarsa as $k): 
    $sisa = (strtotime($k->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $k->nama_produk ?></td>
    <td><?= $k->tanggal_kadaluarsa ?></td>
    <td><?= floor($sisa) ?> hari</td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>