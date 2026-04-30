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

<!-- ================= HEADER ================= -->
<div class="center">
    <h2>💊 MyApotek</h2>
    <small>Jl. Sehat Selalu No.1</small><br>
    <small>Telp: 0812-xxxx-xxxx</small>
</div>

<div class="line"></div>

<table>
<tr>
    <td>Tanggal</td>
    <td class="right"><?= date('d/m/Y H:i', strtotime($pesanan->created_at)) ?></td>
</tr>
<tr>
    <td>Kasir</td>
    <td class="right"><?= $pesanan->nama ?></td>
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
    <td class="right"><?= number_format($d->subtotal) ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="line"></div>

<!-- ================= FOOTER ================= -->
<table>
<tr>
    <td>Total</td>
    <td class="right bold">Rp <?= number_format($pesanan->total_harga) ?></td>
</tr>
<tr>
    <td>Bayar</td>
    <td class="right">Rp <?= number_format($pesanan->bayar) ?></td>
</tr>
<tr>
    <td>Kembali</td>
    <td class="right">Rp <?= number_format($pesanan->kembalian) ?></td>
</tr>
</table>

<div class="line"></div>

<div class="center">
    <p>Terima kasih, Semoga Lekas Sembuh <p>
    <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
</div>

<!-- ================= BUTTON ================= -->
 
 <!-- KEMBALI -->
    <a href="<?= base_url('dashboard') ?>" class="btn">
        ⬅ Kembali ke Dashboard
    </a>

    <a href="<?= base_url('kasir') ?>" class="btn w-100 mt-2" style="background:#2563eb;">
    ➕ Transaksi Baru
    </a>

<!-- <div class="no-print">
    <a href="<?= base_url('kasir/pdf/'.$pesanan->id_pesanan) ?>" class="btn">
        ⬇ Download PDF
    </a> -->

    <button onclick="window.print()" class="btn w-100 mt-2">
        🖨 Print
    </button>
</div>

</body>
</html>