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

.info td {
    border: none;
    padding: 4px;
}

/* TOTAL */
.total-row {
    margin-top: 10px;
    text-align: right;
}

/* FOOTER SIGN */
.ttd {
    margin-top: 50px;
    width: 100%;
}

.ttd td {
    text-align: center;
    border: none;
    padding-top: 30px;
}

/* SATUAN BADGE STYLE */
.satuan-badge {
    font-size: 10px;
    color: #666;
}

/* TEXT ALIGN */
.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.text-left {
    text-align: left;
}

</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<div style="text-align:center;">
    <h2 style="margin:0;">APOTEK GEMPAS FARMA</h2>
    <p style="margin:2px;">Jalan Gempol Sari 15520 Sepatan Timur Banten</p>
    <p style="margin:2px;">Telp: 0895-4176-48792</p>
    <p style="margin:2px;">Email: apotekgempas@gmail.com</p>
</div>

<hr style="margin:15px 0;">

<!-- ================= TITLE ================= -->
<div class="title">
    <h3>INVOICE PEMBELIAN OBAT</h3>
</div>

<!-- ================= INFO PEMBELIAN ================= -->
<table class="info" style="width:100%;">
    <tr>
        <td style="width:50%;">
            <strong>No Invoice:</strong> <?= $pembelian->kode_pembelian ?><br>
            <strong>Supplier:</strong> <?= isset($pembelian->nama_supplier) ? $pembelian->nama_supplier : '-' ?>
        </td>
        <td style="width:50%;">
            <strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($pembelian->tanggal)) ?><br>
            <strong>Status:</strong> 
            <?php 
            $status = $pembelian->status;
            $status_text = ucfirst($status);
            if($status == 'selesai') $status_text = '✓ SELESAI';
            if($status == 'menunggu') $status_text = '⏳ MENUNGGU';
            if($status == 'diterima') $status_text = '✓ DITERIMA';
            if($status == 'ditolak') $status_text = '✗ DITOLAK';
            echo $status_text;
            ?>
        </td>
    </tr>
    <?php if(isset($pembelian->tanggal_approve) && $pembelian->tanggal_approve): ?>
    <tr>
        <td>
            <strong>Tanggal Approve:</strong> <?= date('d M Y H:i', strtotime($pembelian->tanggal_approve)) ?>
        </td>
        <td></td>
    </tr>
    <?php endif; ?>
</table>

<br>

<!-- ================= TABLE PRODUK ================= -->
<table style="width:100%;">
    <thead>
        <tr>
            <th style="width:5%;">No</th>
            <th style="width:40%;">Nama Produk</th>
            <th style="width:15%;">Satuan</th>
            <th style="width:10%;">Qty</th>
            <th style="width:15%;">Harga</th>
            <th style="width:15%;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; $total_item = 0; ?>
        <?php foreach($detail as $d): 
            $total_item += $d->jumlah;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-left"><?= $d->nama_produk ?></td>
            <td class="text-center">
                <?= isset($d->nama_satuan) && $d->nama_satuan ? $d->nama_satuan : 'Unit' ?>
                <?php if(isset($d->konversi_ke_dasar) && $d->konversi_ke_dasar > 1): ?>
                    <br><small class="satuan-badge">(1 = <?= $d->konversi_ke_dasar ?> unit)</small>
                <?php endif; ?>
            </td>
            <td class="text-center"><?= $d->jumlah ?>x</td>
            <td class="text-right">Rp <?= number_format($d->harga, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($d->subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        
        <?php if(count($detail) == 0): ?>
        <tr>
            <td colspan="6" class="text-center">Tidak ada data produk</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- ================= RINGKASAN ================= -->
<br>
<table style="width:100%; margin-top:10px;">
    <tr>
        <td style="width:70%; border:none;"></td>
        <td style="width:30%; border:none;">
            <table style="width:100%;">
                <tr>
                    <td style="border:none;"><strong>Total Item:</strong></td>
                    <td style="border:none; text-align:right;"><?= $total_item ?> pcs</td>
                </tr>
                <tr>
                    <td style="border:none;"><strong>Total Harga:</strong></td>
                    <td style="border:none; text-align:right;">
                        <strong>Rp <?= number_format($pembelian->total, 0, ',', '.') ?></strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php if(isset($pembelian->catatan) && $pembelian->catatan): ?>
<br>
<table style="width:100%;">
    <tr>
        <td style="border:none;">
            <strong>📝 Catatan:</strong><br>
            <?= $pembelian->catatan ?>
        </td>
    </tr>
</table>
<?php endif; ?>

<!-- ================= TANDA TANGAN ================= -->
<table class="ttd" style="width:100%; margin-top:50px;">
    <tr>
        <td style="width:33%;">
            Dibuat Oleh,<br><br><br><br><br>
            ( Admin )
        </td>
        <td style="width:33%;">
            Disetujui Oleh,<br><br><br><br><br>
            ( Manager )
        </td>
    </tr>
</table>

<!-- ================= FOOTER ================= -->
<hr style="margin-top:30px;">
<div style="text-align:center; font-size:10px; color:#666;">
    <p>Dokumen ini dicetak secara otomatis oleh sistem.<br>
    Terima kasih atas kerjasamanya.</p>
</div>

</body>
</html>