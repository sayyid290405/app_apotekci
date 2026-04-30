<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>

body {
    font-family: Arial, sans-serif;
    font-size: 12px;
}

/* HEADER */
.header {
    border-bottom: 2px solid #000;
    margin-bottom: 10px;
    padding-bottom: 5px;
}

.header h2 {
    margin: 0;
}

.header small {
    display: block;
}

/* TITLE */
.title {
    text-align: center;
    margin: 15px 0;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
}

th {
    background: #eee;
    text-align: center;
}

/* INFO */
.info {
    margin-bottom: 10px;
}

/* FOOTER SIGN */
.ttd {
    margin-top: 50px;
    width: 100%;
}

.ttd td {
    text-align: center;
    border: none;
}

</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<div style="text-align:center;">
    <h2 style="margin:0;">APOTEK SEHAT SENTOSA</h2>
    <p style="margin:2px;">Jl. Raya Kesehatan No.123, Jakarta</p>
    <p style="margin:2px;">Telp: 0812-3456-7890</p>
</div>

<hr style="margin:15px 0;">

<!-- ================= TITLE ================= -->
<div class="title">
    <h3>INVOICE PEMBELIAN OBAT</h3>
</div>

<!-- ================= INFO ================= -->
<table class="info" border="0">
<tr>
    <td width="50%">
        <b>No Invoice:</b> <?= $pembelian->kode_pembelian ?><br>
        <b>Supplier:</b> <?= isset($pembelian->nama_supplier) ? $pembelian->nama_supplier : '-' ?>
    </td>
    <td width="50%">
        <b>Tanggal:</b> <?= date('d M Y', strtotime($pembelian->tanggal)) ?><br>
        <b>Status:</b> <?= ucfirst($pembelian->status) ?><br>
    </td>
</tr>
</table>

<!-- ================= TABLE ================= -->
<table>
<thead>
<tr>
    <th>No</th>
    <th>Nama Produk</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Subtotal</th>
</tr>
</thead>

<tbody>
<?php $no=1; foreach($detail as $d): ?>
<tr>
    <td align="center"><?= $no++ ?></td>
    <td><?= $d->nama_produk ?></td>
    <td align="center"><?= $d->jumlah ?></td>
    <td align="right">Rp <?= number_format($d->harga,0,',','.') ?></td>
    <td align="right">Rp <?= number_format($d->subtotal,0,',','.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- ================= TOTAL ================= -->
<br>
<table border="0">
<tr>
    <td align="right"><b>Total:</b></td>
    <td width="200" align="right">
        <b>Rp <?= number_format($pembelian->total,0,',','.') ?></b>
    </td>
</tr>
</table>

<!-- ================= TTD ================= -->
<table class="ttd">
<tr>
    <td>
        Dibuat Oleh<br><br><br><br>
        ( Admin )
    </td>

    <td>
        Disetujui<br><br><br><br>
        ( Manager )
    </td>

    <td>
        Supplier<br><br><br><br>
        ( __________ )
    </td>
</tr>
</table>

</body>
</html>