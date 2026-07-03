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

        .small-text{
            font-size:10px;
            color:#555;
        }

        .aturan-minum{
            background:#f0f0f0;
            padding:4px;
            border-radius:4px;
            margin-top:3px;
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
$biaya_resep = $biaya_resep ?? 0;

$isResep = ($pesanan && $pesanan->tipe_transaksi == 'resep');

// fallback subtotal obat
$subtotal_obat = $pesanan->subtotal ?? 0;
if($subtotal_obat == 0){
    foreach($detail as $d){
        $subtotal_obat += ($d->harga * $d->jumlah);
    }
}

// total keseluruhan (obat + biaya resep)
$total_keseluruhan = ($pesanan->total_harga ?? 0);

// invoice
$inv = 'TRX-' . str_pad($pesanan->id_pesanan ?? 0, 6, '0', STR_PAD_LEFT);
?>

<!-- ================= HEADER ================= -->
<div class="center">
    <h2>Apotek Gempas Farma</h2>
    <small>Jalan Gempol Sari 15520 Sepatan Timur Banten</small><br>
    <small>Telp: 0895-4176-48792</small>
</div>

<div class="line"></div>

<table width="100%">
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

<!-- ================= DATA RESEP (jika dari resep) ================= -->
<?php if($isResep): ?>
<div class="line"></div>
<table width="100%">
    <tr>
        <td width="40%">Kode Resep</td>
        <td class="right" width="60%"><?= $pesanan->kode_resep ?? '-' ?></td>
    </tr>
    <tr>
        <td>Nama Pasien</td>
        <td class="right"><strong><?= $pesanan->nama_pasien ?? '-' ?></strong></td>
    </tr>
    <tr>
        <td>Nama Dokter</td>
        <td class="right"><?= $pesanan->nama_dokter ?? '-' ?></td>
    </tr>
</table>
<?php endif; ?>

<div class="line"></div>

<!-- ================= BODY ================= -->
<table width="100%">
    <?php foreach($detail as $d): ?>
    <tr>
        <td colspan="2"><strong><?= $d->nama_produk ?></strong></td>
    </tr>
    
    <?php if($isResep): ?>
        <!-- Tampilkan aturan minum obat -->
        <tr>
            <td colspan="2" class="small-text">
                <div class="aturan-minum">
                    <i class="fas fa-clock"></i> Aturan Minum: 
                    <?php if(!empty($d->aturan_pakai)): ?>
                        <?= $d->aturan_pakai ?>
                    <?php elseif(!empty($d->dosis)): ?>
                        <?= $d->dosis ?>
                    <?php else: ?>
                        Sesuai resep dokter
                    <?php endif; ?>
                    
                    <?php if(!empty($d->sehari) && !empty($d->jangka)): ?>
                        (<?= $d->sehari ?>x sehari selama <?= $d->jangka ?> hari)
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endif; ?>
    
    <tr>
        <td><?= $d->jumlah ?> x Rp <?= number_format($d->harga) ?></td>
        <td class="right">
            Rp <?= number_format($d->subtotal ?? ($d->harga * $d->jumlah)) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="line"></div>

<!-- ================= FOOTER ================= -->
<table width="100%">
    <tr>
        <td>Subtotal Obat</td>
        <td class="right">Rp <?= number_format($subtotal_obat) ?></td>
    </tr>
    
    <?php if($isResep): 
        // Hitung biaya resep = total - subtotal_obat - ppn + diskon
        $biaya_resep = ($total_keseluruhan - $subtotal_obat - ($pesanan->ppn ?? 0) + ($pesanan->diskon ?? 0));
        if($biaya_resep > 0):
    ?>
    <tr>
        <td>Biaya Jasa Resep</td>
        <td class="right">Rp <?= number_format($biaya_resep) ?></td>
    </tr>
    <?php endif; endif; ?>
    
    <tr>
        <td>Diskon</td>
        <td class="right">- Rp <?= number_format($pesanan->diskon ?? 0) ?></td>
    </tr>
    
    <tr>
        <td>PPN (11%)</td>
        <td class="right">Rp <?= number_format($pesanan->ppn ?? 0) ?></td>
    </tr>
    
    <tr class="bold">
        <td class="bold">Total</td>
        <td class="right bold">
            Rp <?= number_format($total_keseluruhan) ?>
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

<!-- ================= CATATAN MINUM OBAT ================= -->
<?php if($isResep): ?>
<div class="center small-text">
    <strong>⚠️ Informasi Penting:</strong><br>
    Minum obat sesuai anjuran dokter.<br>
    Jangan menghentikan pengobatan tanpa konsultasi.<br>
    Baca aturan pakai yang tertera pada kemasan.
</div>
<div class="line"></div>
<?php endif; ?>

<div class="center">
    <p>Terima kasih, Semoga Lekas Sembuh</p>
    <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
</div>

<!-- ================= BUTTON ================= -->
<div class="no-print">
    <a href="<?= base_url('kasir') ?>" class="btn" style="background:#2563eb;">
        🛒 Kembali ke Kasir
    </a>
    <button onclick="window.print()" class="btn">
        🖨 Print
    </button>
</div>

</body>
</html>