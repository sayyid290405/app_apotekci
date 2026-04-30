<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran</title>

    <style>
        body{
            font-family: monospace;
            width: 320px;
            margin: auto;
            color:#000;
        }

        .center{text-align:center;}
        .line{border-top:1px dashed #000; margin:8px 0;}

        table{
            width:100%;
            font-size:12px;
        }

        td{
            padding:2px 0;
        }

        .right{text-align:right;}
        .bold{font-weight:bold;}

        .btn{
            display:block;
            margin-top:10px;
            padding:8px;
            background:#059669;
            color:white;
            text-align:center;
            text-decoration:none;
            border-radius:5px;
        }

        @media print{
            .no-print{display:none;}
        }
    </style>
</head>

<body>

<?php
// ================= SAFE DATA =================
$pesanan = $pesanan ?? null;
$detail  = $detail ?? [];

$isResep = ($pesanan && $pesanan->tipe_transaksi == 'resep');

// fallback subtotal
$subtotal = $pesanan->subtotal ?? 0;
if($subtotal == 0){
    foreach($detail as $d){
        $subtotal += ($d->harga * $d->jumlah);
    }
}

// invoice
$inv = 'INV-' . str_pad($pesanan->id_pesanan ?? 0, 6, '0', STR_PAD_LEFT);
?>

<!-- ================= HEADER ================= -->
<div class="center">
    <h2>💊 MyApotek</h2>
    <small>Jl. Sehat Selalu No.1</small><br>
    <small>Telp: 0812-xxxx-xxxx</small>
</div>

<div class="line"></div>

<table>
<tr>
    <td>ID</td>
    <td class="right"><?= $inv ?></td>
</tr>
<tr>
    <td>Tanggal</td>
    <td class="right">
        <?= !empty($pesanan->tanggal_pesan)
            ? date('d/m/Y H:i', strtotime($pesanan->tanggal_pesan))
            : '-' ?>
    </td>
</tr>
<tr>
    <td>Kasir</td>
    <td class="right">
        <?= $pesanan->nama_kasir ?? $pesanan->nama ?? 'Admin' ?>
    </td>
</tr>
</table>

<div class="line"></div>

<!-- ================= LABEL TRANSAKSI ================= -->
<div class="center">
    <strong>
        <?= $isResep ? '*** RESEP DOKTER ***' : 'TRANSAKSI UMUM' ?>
    </strong>
</div>

<!-- ================= DATA RESEP ================= -->
<?php if($isResep): ?>
<div class="line"></div>
<table>
<tr>
    <td>Pasien</td>
    <td class="right"><?= $pesanan->pasien_nama ?? '-' ?></td>
</tr>
<tr>
    <td>Dokter</td>
    <td class="right"><?= $pesanan->dokter ?? '-' ?></td>
</tr>
</table>
<?php endif; ?>

<div class="line"></div>

<!-- ================= BODY ================= -->
<table>
<?php foreach($detail as $d): ?>
<tr>
    <td colspan="2"><?= $d->nama_produk ?></td>
</tr>

<?php if($isResep && !empty($d->dosis)): ?>
<tr>
    <td colspan="2">
        <small>Dosis: <?= $d->dosis ?></small>
    </td>
</tr>
<?php endif; ?>

<tr>
    <td><?= $d->jumlah ?> x <?= number_format($d->harga) ?></td>
    <td class="right">
        <?= number_format($d->subtotal ?? ($d->harga * $d->jumlah)) ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div class="line"></div>

<!-- ================= FOOTER ================= -->
<table>
<tr>
    <td>Subtotal</td>
    <td class="right">Rp <?= number_format($subtotal) ?></td>
</tr>

<tr>
    <td>Diskon</td>
    <td class="right">- Rp <?= number_format($pesanan->diskon ?? 0) ?></td>
</tr>

<tr>
    <td>PPN (11%)</td>
    <td class="right">Rp <?= number_format($pesanan->ppn ?? 0) ?></td>
</tr>

<tr>
    <td class="bold">Total</td>
    <td class="right bold">
        Rp <?= number_format($pesanan->total_harga ?? 0) ?>
    </td>
</tr>

<tr>
    <td>Bayar</td>
    <td class="right">Rp <?= number_format($pesanan->bayar ?? 0) ?></td>
</tr>

<tr>
    <td>Kembali</td>
    <td class="right">Rp <?= number_format($pesanan->kembalian ?? 0) ?></td>
</tr>
</table>

<div class="line"></div>

<div class="center">
    <p>Terima kasih, Semoga Lekas Sembuh</p>
    <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
</div>

<!-- ================= BUTTON ================= -->
<div class="no-print">

    <a href="<?= base_url('dashboard') ?>" class="btn">
        ⬅ Kembali ke Dashboard
    </a>

    <a href="<?= base_url('kasir') ?>" class="btn" style="background:#2563eb;">
        ➕ Transaksi Baru
    </a>

    <button onclick="window.print()" class="btn">
        🖨 Print
    </button>

</div>

</body>
</html>