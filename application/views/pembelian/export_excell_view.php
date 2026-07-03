<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            color: #555;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-menunggu {
            color: #856404;
        }
        .status-diproses {
            color: #004085;
        }
        .status-disetujui {
            color: #0c5460;
        }
        .status-selesai {
            color: #155724;
        }
        .status-ditolak {
            color: #721c24;
        }
        .status-dibatalkan {
            color: #721c24;
        }
        .status-diterima {
            color: #155724;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PEMBELIAN BARANG</h1>
    <p><strong>Apotek Bayur Farma</strong></p>
    <p>Jl. Raya Kedaung Barat, Kedaung Bar., Kec. Sepatan Tim., Kabupaten Tangerang, Banten 15520</p>
    <p>Periode: <?= date('d F Y', strtotime($tgl_awal)) ?> - <?= date('d F Y', strtotime($tgl_akhir)) ?></p>
    <?php if($status && $status != 'semua'): ?>
    <p>Status: <strong><?= strtoupper($status) ?></strong></p>
    <?php endif; ?>
    <p>Total Transaksi: <?= count($pembelian) ?> | Total: Rp <?= number_format($total_all, 0, ',', '.') ?></p>
</div>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="12%">Kode Pembelian</th>
            <th width="12%">Tanggal</th>
            <th width="15%">Supplier</th>
            <th width="20%">Produk</th>
            <th width="8%">Total Item</th>
            <th width="12%">Total</th>
            <th width="10%">Status</th>
            <th width="10%">Kasir</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $grand_total = 0;
        if(empty($pembelian)): 
        ?>
            <tr>
                <td colspan="9" style="text-align:center; color:#999; padding:20px;">
                    Tidak ada data pembelian
                </td>
            </tr>
        <?php else: ?>
            <?php foreach($pembelian as $p): 
                $grand_total += (int)$p->total;
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= $p->kode_pembelian ?? '-' ?></strong></td>
                <td><?= date('d/m/Y H:i', strtotime($p->tanggal ?? date('Y-m-d'))) ?></td>
                <td class="text-left"><?= $p->nama_supplier ?? '-' ?></td>
                <td class="text-left" style="font-size:9px;">
                    <?php 
                    if(!empty($p->detail_produk)){
                        // Batasi tampilan produk jika terlalu panjang
                        $produk_list = explode(', ', $p->detail_produk);
                        if(count($produk_list) > 3){
                            echo implode(', ', array_slice($produk_list, 0, 3)) . ', ... (' . count($produk_list) . ' produk)';
                        } else {
                            echo $p->detail_produk;
                        }
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td><?= $p->total_item ?? 0 ?></td>
                <td class="text-right"><strong>Rp <?= number_format($p->total ?? 0, 0, ',', '.') ?></strong></td>
                <td>
                    <span class="status-<?= $p->status ?? 'menunggu' ?>">
                        <?= strtoupper($p->status ?? 'MENUNGGU') ?>
                    </span>
                </td>
                <td><?= $p->kasir ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if(!empty($pembelian)): ?>
        <tr class="total-row">
            <td colspan="6" style="text-align:right; font-size:12px;">
                <strong>GRAND TOTAL</strong>
            </td>
            <td colspan="3" style="text-align:center; font-size:14px; color:#28a745;">
                <strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong>
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <p>Dicetak: <?= date('d F Y H:i:s') ?></p>
    <p>&copy; <?= date('Y') ?> Apotek Bayur Farma - Laporan Pembelian</p>
    <p style="font-size:8px; color:#999;">Dokumen ini dicetak dari sistem</p>
</div>

</body>
</html>