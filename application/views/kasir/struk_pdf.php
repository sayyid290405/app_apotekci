<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>INVOICE - <?= str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) ?></title>
<style>
    /* Reset & Base */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
        font-family: 'Segoe UI', Tahoma, sans-serif; 
        font-size: 12px; /* Ukuran standar yang nyaman dibaca */
        background: #fdfdfd; 
        padding: 20px; 
        color: #2d3748;
    }

    .invoice-wrapper {
        max-width: 210mm;
        margin: 0 auto;
        padding: 30px;
        background: #ffffff;
    }

    /* Header */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        padding-bottom: 20px;
        border-bottom: 2px solid #edf2f7;
        margin-bottom: 20px;
    }
    .brand { font-size: 22px; font-weight: 800; color: #1a202c; }
    .invoice-title { font-size: 18px; font-weight: 700; color: #48bb78; }

    /* Info Grid */
    .info-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
    }
    .label { font-size: 10px; text-transform: uppercase; color: #718096; display: block; margin-bottom: 2px; }
    .value { font-size: 12px; font-weight: 600; }

    /* Table */
    .table-section { margin-bottom: 20px; }
    .table-item { width: 100%; border-collapse: collapse; }
    .table-item th { 
        background: #edf2f7; 
        padding: 10px; 
        font-size: 11px; 
        text-align: left;
        color: #4a5568;
    }
    .table-item td { padding: 10px; border-bottom: 1px solid #edf2f7; font-size: 12px; }

    /* Total Section */
    .total-section { display: flex; justify-content: flex-end; }
    .total-box { width: 250px; }
    .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 12px; }
    .grand { border-top: 2px solid #2d3748; margin-top: 8px; padding-top: 8px; font-weight: bold; font-size: 14px; color: #000; }

    /* Footer */
    .invoice-footer { text-align: center; margin-top: 25px; font-size: 11px; color: #718096; }

    @media print {
        body { padding: 0; }
        .invoice-wrapper { box-shadow: none; }
    }
</style>
</head>
<body>

<div class="invoice-wrapper">
    <!-- HEADER -->
    <div class="invoice-header">
        <div class="header-left">
            <div class="brand">APOTEK <span>Gempas Farma</span></div>
            <div class="sub-brand">Jl. Gempol Sari, Sepatan Timur, Banten 15520</div>
            <div class="sub-brand">Telp: 0895-4176-48792</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#TRX-<?= str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
    </div>

    <!-- INFO -->
    <div class="info-section">
        <div class="info-box">
            <span class="label">Tanggal Transaksi</span>
            <span class="value"><?= date('d F Y H:i', strtotime($pesanan->tanggal_pesan ?? date('Y-m-d H:i:s'))) ?></span>
        </div>
        <div class="info-box" style="text-align:right;">
            <span class="label">Kasir</span>
            <span class="value"><?= $pesanan->nama_kasir ?? 'Admin' ?></span>
        </div>
        <div class="info-box">
            <span class="label">Metode Pembayaran</span>
            <span class="value">
                <span class="badge badge-<?= strtolower($pesanan->metode_bayar ?? 'cash') ?>">
                    <?= strtoupper($pesanan->metode_bayar ?? 'CASH') ?>
                </span>
            </span>
        </div>
        <div class="info-box" style="text-align:right;">
            <span class="label">Jenis Transaksi</span>
            <span class="value">
                <span class="badge <?= ($pesanan->tipe_transaksi ?? '') == 'resep' ? 'badge-resep' : 'badge-umum' ?>">
                    <?= ($pesanan->tipe_transaksi ?? '') == 'resep' ? 'RESEP DOKTER' : 'NON RESEP' ?>
                </span>
            </span>
        </div>
        <?php if(isset($pesanan->nama_pasien) && $pesanan->nama_pasien): ?>
        <div class="info-box">
            <span class="label">Nama Pasien</span>
            <span class="value"><?= $pesanan->nama_pasien ?></span>
        </div>
        <?php endif; ?>
        <?php if(isset($pesanan->nama_dokter) && $pesanan->nama_dokter): ?>
        <div class="info-box" style="text-align:right;">
            <span class="label">Dokter</span>
            <span class="value"><?= $pesanan->nama_dokter ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- TABLE PRODUK -->
    <div class="table-section">
        <div class="table-title">📋 Detail Pesanan</div>
        <table class="table-item">
            <thead>
                <tr>
                    <th style="width:40%;">Nama Produk</th>
                    <th class="text-center" style="width:15%;">Qty</th>
                    <th class="text-right" style="width:20%;">Harga</th>
                    <th class="text-right" style="width:25%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $subtotal = 0;
            foreach($detail as $d): 
                $harga_satuan = $d->subtotal / $d->jumlah;
                $subtotal += $d->subtotal;
            ?>
                <tr>
                    <td>
                        <div class="product-name"><?= $d->nama_produk ?></div>
                        <?php if(isset($d->dosis) && $d->dosis): ?>
                            <span class="dosis-text">Dosis: <?= $d->dosis ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $d->jumlah ?></td>
                    <td class="text-right">Rp <?= number_format($harga_satuan, 0, ',', '.') ?></td>
                    <td class="text-right"><strong>Rp <?= number_format($d->subtotal, 0, ',', '.') ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- TOTAL -->
    <div class="total-section">
        <div class="total-box">
            <div class="total-row">
                <span class="label-total">Subtotal</span>
                <span class="value-total">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span class="label-total">Diskon</span>
                <span class="value-total">- Rp <?= number_format($pesanan->diskon ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span class="label-total">PPN 11%</span>
                <span class="value-total">Rp <?= number_format($pesanan->ppn ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="total-row divider"></div>
            <div class="total-row grand">
                <span class="label-total">TOTAL</span>
                <span class="value-total">Rp <?= number_format($pesanan->total_harga, 0, ',', '.') ?></span>
            </div>
            <?php if(isset($pesanan->bayar) && $pesanan->bayar): ?>
            <div class="total-row" style="margin-top:6px;">
                <span class="label-total">Bayar</span>
                <span class="value-total" style="color:#48bb78;">Rp <?= number_format($pesanan->bayar, 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span class="label-total">Kembalian</span>
                <span class="value-total" style="color:#e53e3e;">Rp <?= number_format($pesanan->kembalian ?? 0, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="invoice-footer">
        <div class="thanks-text">Terima Kasih</div>
        <div class="message-text">
            Semoga lekas sembuh <span class="emoji">😊</span>
        </div>
        <div class="footer-note">
            Barang yang sudah dibeli tidak dapat dikembalikan
        </div>
        <div class="footer-note" style="margin-top:4px;color:#718096;">
            Dicetak: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
</div>

</body>
</html>