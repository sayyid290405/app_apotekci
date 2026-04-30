<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        .title { text-align: center; font-size: 18px; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4e73df; color: white; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="title">LAPORAN PENJUALAN APOTEK</div>
    <div style="text-align: center;">Tanggal Cetak: <?= date('d-m-Y H:i:s') ?></div>
    <br>

    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th>ID Pesanan</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $grand_total = 0; ?>
            <?php if(!empty($penjualan)): ?>
                <?php foreach($penjualan as $p): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $p->id_pesanan; ?></td> 
                    <td><?= date('d-m-Y', strtotime($p->tanggal_pesan)); ?></td>
                    <td class="text-right"><?= number_format($p->total_harga, 0, ',', '.'); ?></td>
                    <td><?= $p->status; ?></td>
                </tr>
                <?php $grand_total += $p->total_harga; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right;">GRAND TOTAL</th>
                <th class="text-right"><?= number_format($grand_total, 0, ',', '.'); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</body>
</html> 