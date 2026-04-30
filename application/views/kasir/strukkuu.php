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

        .total{
            font-size:14px;
            font-weight:bold;
        }

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

$subtotal = $pesanan->subtotal ?? 0;

// fallback subtotal jika 0
if($subtotal == 0){
    foreach($detail as $d){
        $subtotal += ($d->harga * $d->jumlah);
    }
}

// generate kode invoice
$inv = 'INV-' . str_pad($pesanan->id_pesanan ?? 0, 6, '0', STR_PAD_LEFT);
?>

<!-- ================= HEADER ================= -->
<div class="center">
    <h2>Apotek Bayur Farma</h2>
    <small>Jl. Raya Kedaung Barat 15520 Sepatan Timur Banten</small><br>
    <small>Telp: +62 899-8243-363</small>
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

<!-- ================= BODY ================= -->
<table>
<?php foreach($detail as $d): ?>
<tr>
    <td colspan="2"><?= $d->nama_produk ?></td>
</tr>
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