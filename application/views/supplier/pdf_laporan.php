<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Supplier - <?= $no_dokumen ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #555;
        }
        .doc-info {
            margin-bottom: 15px;
            font-size: 10px;
            border: 1px solid #ddd;
            padding: 8px;
            background: #f9f9f9;
        }
        .doc-info table {
            width: 100%;
            border: none;
        }
        .doc-info td {
            border: none;
            padding: 2px 5px;
        }
        .supplier-box {
            margin-bottom: 15px;
            border: 1px solid #3498db;
            background: #eef6fc;
            padding: 8px;
            font-size: 10px;
        }
        .supplier-box strong {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #34495e;
            color: white;
            padding: 6px 4px;
            font-size: 10px;
            text-align: center;
            border: 1px solid #2c3e50;
        }
        td {
            padding: 5px 4px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            text-align: center;
            border-top: 1px solid #aaa;
            padding-top: 8px;
        }
        .page-break {
            page-break-before: always;
        }
        .grand-total {
            background: #ecf0f1;
            font-weight: bold;
        }
        .stats-box {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .stats-box h4 {
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .stats-table td {
            border: none;
            padding: 3px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PEMBELIAN SUPPLIER</h1>
    <p>Apotek Gempas Farma | Jl. Gempol Sari Sepatan Timur, Banten 15520 | Telp: 0895-4176-48792</p>
    <p>Email: apotek@gempas.com | Website: www.apotekgempas.com</p>
</div>

<div class="doc-info">
    <table>
        <tr>
            <td width="25%"><strong>No. Dokumen</strong></td>
            <td width="25%">: <?= $no_dokumen ?></td>
            <td width="25%"><strong>Tanggal Cetak</strong></td>
            <td width="25%">: <?= date('d/m/Y H:i:s') ?></td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>: <?= ($tgl_awal != '-' ? date('d/m/Y', strtotime($tgl_awal)) : 'Semua') ?> s/d <?= ($tgl_akhir != '-' ? date('d/m/Y', strtotime($tgl_akhir)) : 'Semua') ?></td>
            <td><strong>Filter</strong></td>
            <td>: <?= htmlspecialchars($search != '-' ? $search : '-') ?></td>
        </tr>
        <tr>
            <td><strong>Limit</strong></td>
            <td>: <?= $limit == 'all' ? 'Semua Data' : $limit . ' Data Terakhir' ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

<div class="supplier-box">
    <strong>Informasi Supplier:</strong><br>
    Nama: <?= htmlspecialchars($supplier->nama_supplier ?? '-') ?><br>
    Alamat: <?= nl2br(htmlspecialchars($supplier->alamat ?? '-')) ?><br>
    Kontak: <?= htmlspecialchars($supplier->kontak ?? '-') ?> | Legalitas: <?= htmlspecialchars($supplier->legalitas ?? '-') ?>
</div>

<!-- Tabel Data Pembelian -->
<h4 style="margin: 10px 0 5px 0;">Data Pembelian / Restock</h4>
<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Kode Order</th>
            <th width="12%">Tanggal</th>
            <th width="10%">Status</th>
            <th width="8%">Item</th>
            <th width="15%">Total (Rp)</th>
            <th width="15%">User Pembuat</th>
            <th width="10%">Approved</th>
            <th width="10%">Tgl Approve</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pembelian)): ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data pembelian untuk periode ini</td>
            </tr>
        <?php else: ?>
            <?php 
            $no = 1; 
            $grand_total = 0;
            foreach ($pembelian as $p): 
                $grand_total += $p->total;
                // Warna status (hanya teks, karena PDF tidak selalu support warna background)
                $status_text = ucfirst($p->status);
                if ($p->status == 'selesai') $status_text = '✓ Selesai';
                elseif ($p->status == 'ditolak') $status_text = '✗ Ditolak';
                elseif ($p->status == 'dibatalkan') $status_text = '✗ Dibatalkan';
            ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($p->kode_pembelian) ?></td>
                    <td class="text-center"><?= date('d/m/Y H:i', strtotime($p->tanggal)) ?></td>
                    <td class="text-center"><?= $status_text ?></td>
                    <td class="text-center"><?= $p->total_item ?? 0 ?></td>
                    <td class="text-right"><?= number_format($p->total, 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($p->user_name ?? '-') ?></td>
                    <td><?= htmlspecialchars($p->approved_by_name ?? '-') ?></td>
                    <td class="text-center"><?= $p->tanggal_approve ? date('d/m/Y', strtotime($p->tanggal_approve)) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="grand-total">
            <td colspan="5" class="text-right"><strong>GRAND TOTAL</strong></td>
            <td class="text-right"><strong><?= number_format($grand_total, 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

<!-- Statistik Ringkasan -->
<?php if (isset($stats) && $stats): ?>
<div class="stats-box">
    <h4>Ringkasan Statistik</h4>
    <table class="stats-table" style="width: 100%; border: none;">
        <tr>
            <td width="33%"><strong>Total Pembelian (Rp):</strong> <?= number_format($stats->total_pembelian ?? 0, 0, ',', '.') ?></td>
            <td width="33%"><strong>Jumlah Transaksi:</strong> <?= $stats->jumlah_transaksi ?? 0 ?></td>
            <td width="33%"><strong>Produk Terlaris:</strong> 
                <?php 
                if (!empty($stats->produk_terlaris)) {
                    $top = $stats->produk_terlaris[0];
                    echo htmlspecialchars($top->nama_produk) . ' (' . number_format($top->total_dibeli) . ' pcs)';
                } else {
                    echo '-';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php if (!empty($stats->produk_terlaris) && count($stats->produk_terlaris) > 1): ?>
        <div style="margin-top: 5px; font-size: 9px;">
            <strong>Top 5 Produk:</strong>
            <?php 
            $top5 = array_slice($stats->produk_terlaris, 0, 5);
            $names = array_map(function($item) { return $item->nama_produk . ' (' . $item->total_dibeli . ')'; }, $top5);
            echo implode(' | ', $names);
            ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="footer">
    <table style="width: 100%; border: none; margin-top: 15px;">
        <tr>
            <td style="border: none; text-align: left;">Dicetak oleh: <?= $this->session->userdata('nama') ?? 'Supplier' ?></td>
            <td style="border: none; text-align: right;">Tangerang, <?= date('d F Y') ?></td>
        </tr>
        <tr>
            <td style="border: none; text-align: left;">&nbsp;</td>
            <td style="border: none; text-align: right; padding-top: 30px;">( _____________________ )<br>Admin Apotek</td>
        </tr>
    </table>
    <p style="margin-top: 10px;">Dokumen ini sah dan ditandatangani secara elektronik. <br>Laporan #<?= $no_dokumen ?> - <?= date('Y-m-d H:i:s') ?></p>
</div>

</body>
</html>