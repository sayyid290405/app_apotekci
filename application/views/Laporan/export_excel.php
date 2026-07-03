<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan</title>
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
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 6px;
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
        .section-title {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 12px;
            text-align: left;
            padding: 8px 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .badge-success {
            color: #28a745;
        }
        .badge-info {
            color: #17a2b8;
        }
        .text-muted {
            color: #999;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN <?= strtoupper($jenis == 'pembelian' ? 'PEMBELIAN' : ($jenis == 'resep' ? 'RESEP' : 'PENJUALAN')) ?></h1>
    <p><strong>Apotek Bayur Farma</strong></p>
    <p>Periode: <?= date('d F Y', strtotime($tgl_awal)) ?> - <?= date('d F Y', strtotime($tgl_akhir)) ?></p>
    <p>
        <?php if($jenis == 'semua'): ?>
            Total Penjualan: <?= $total_penjualan ?> | 
            Total Resep: <?= $total_resep ?> | 
            Total Pembelian: <?= $total_pembelian ?>
        <?php elseif($jenis == 'penjualan'): ?>
            Total Transaksi: <?= $total_penjualan ?> | 
            Total Pendapatan: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
        <?php elseif($jenis == 'resep'): ?>
            Total Resep: <?= $total_resep ?> | 
            Total Pendapatan: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
        <?php elseif($jenis == 'pembelian'): ?>
            Total Transaksi: <?= $total_pembelian ?> | 
            Total: Rp <?= number_format($total_pembelian_all, 0, ',', '.') ?>
        <?php endif; ?>
    </p>
</div>

<?php if($jenis == 'pembelian'): ?>
    <!-- ==================== TABEL PEMBELIAN ==================== -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Pembelian</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Supplier</th>
                <th width="20%">Produk</th>
                <th width="8%">Jumlah</th>
                <th width="10%">Harga</th>
                <th width="10%">Subtotal</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            if(empty($pembelian)): 
            ?>
                <tr>
                    <td colspan="9" style="text-align:center; color:#999;">Tidak ada data pembelian</td>
                </tr>
            <?php else: ?>
                <?php foreach($pembelian as $p): 
                    $first_row = true;
                    $total_item = count($p->detail);
                    
                    if(empty($p->detail)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $p->kode_pembelian ?? '-' ?></td>
                        <td><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                        <td><?= $p->nama_supplier ?? '-' ?></td>
                        <td colspan="4" style="text-align:center; color:#999;">Tidak ada detail</td>
                        <td><?= $p->status ?? 'Menunggu' ?></td>
                    </tr>
                <?php 
                    else:
                        foreach($p->detail as $key => $d): 
                            $grand_total += (int)$d->subtotal;
                ?>
                    <tr>
                        <?php if($first_row): ?>
                            <td rowspan="<?= $total_item ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->kode_pembelian ?? '-' ?></td>
                            <td rowspan="<?= $total_item ?>"><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->nama_supplier ?? '-' ?></td>
                        <?php endif; ?>
                        
                        <td class="text-left"><?= $d->nama_produk ?? 'Produk Dihapus' ?></td>
                        <td><?= $d->jumlah ?? 0 ?> <?= $d->nama_satuan ?? '' ?></td>
                        <td>Rp <?= number_format($d->harga ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($d->subtotal ?? 0, 0, ',', '.') ?></td>
                        
                        <?php if($first_row): ?>
                            <td rowspan="<?= $total_item ?>"><?= strtoupper($p->status ?? 'Menunggu') ?></td>
                        <?php endif; ?>
                    </tr>
                <?php 
                        $first_row = false;
                        endforeach; 
                    endif;
                endforeach; 
            endif;
            ?>
            
            <?php if(!empty($pembelian)): ?>
            <tr class="total-row">
                <td colspan="7" style="text-align:right;">
                    <strong>GRAND TOTAL</strong>
                </td>
                <td colspan="2" style="text-align:center; font-size:12px;">
                    <strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif($jenis == 'resep'): ?>
    <!-- ==================== TABEL RESEP ==================== -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode Resep</th>
                <th width="10%">ID Pesanan</th>
                <th width="12%">Tanggal</th>
                <th width="12%">Pasien</th>
                <th width="12%">Dokter</th>
                <th width="15%">Produk</th>
                <th width="8%">Jumlah</th>
                <th width="8%">Harga</th>
                <th width="8%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            if(empty($penjualan_resep)): 
            ?>
                <tr>
                    <td colspan="10" style="text-align:center; color:#999;">Tidak ada data resep</td>
                </tr>
            <?php else: ?>
                <?php foreach($penjualan_resep as $p): 
                    $first_row = true;
                    $total_item = count($p->detail);
                    
                    if(empty($p->detail)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $p->kode_resep ?? '-' ?></td>
                        <td>#<?= $p->id_pesanan ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?></td>
                        <td><?= $p->nama_pasien ?? '-' ?></td>
                        <td><?= $p->nama_dokter ?? '-' ?></td>
                        <td colspan="4" style="text-align:center; color:#999;">Tidak ada detail</td>
                    </tr>
                <?php 
                    else:
                        foreach($p->detail as $key => $d): 
                            $grand_total += (int)$d->subtotal;
                ?>
                    <tr>
                        <?php if($first_row): ?>
                            <td rowspan="<?= $total_item ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->kode_resep ?? '-' ?></td>
                            <td rowspan="<?= $total_item ?>">#<?= $p->id_pesanan ?></td>
                            <td rowspan="<?= $total_item ?>"><?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->nama_pasien ?? '-' ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->nama_dokter ?? '-' ?></td>
                        <?php endif; ?>
                        
                        <td class="text-left"><?= $d->nama_produk ?? 'Produk Dihapus' ?></td>
                        <td><?= $d->jumlah ?? 0 ?> <?= $d->satuan ?? '' ?></td>
                        <td>Rp <?= number_format($d->harga ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($d->subtotal ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php 
                        $first_row = false;
                        endforeach; 
                    endif;
                endforeach; 
            endif;
            ?>
            
            <?php if(!empty($penjualan_resep)): ?>
            <tr class="total-row">
                <td colspan="9" style="text-align:right;">
                    <strong>GRAND TOTAL</strong>
                </td>
                <td style="text-align:center; font-size:12px;">
                    <strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php else: ?>
    <!-- ==================== TABEL PENJUALAN (SEMUA) ==================== -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="8%">ID Pesanan</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Kasir</th>
                <th width="20%">Produk</th>
                <th width="8%">Jumlah</th>
                <th width="10%">Harga/Unit</th>
                <th width="10%">Subtotal</th>
                <th width="8%">Metode</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $grand_total = 0;
            if(empty($penjualan)): 
            ?>
                <tr>
                    <td colspan="10" style="text-align:center; color:#999;">Tidak ada data penjualan</td>
                </tr>
            <?php else: ?>
                <?php foreach($penjualan as $p): 
                    $first_row = true;
                    $total_item = count($p->detail);
                    
                    if(empty($p->detail)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>#<?= $p->id_pesanan ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?></td>
                        <td><?= $p->kasir ?? '-' ?></td>
                        <td colspan="5" style="text-align:center; color:#999;">Tidak ada detail</td>
                        <td>Rp <?= number_format($p->total_harga ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php 
                    else:
                        foreach($p->detail as $key => $d): 
                            $grand_total += (int)$d->subtotal;
                ?>
                    <tr>
                        <?php if($first_row): ?>
                            <td rowspan="<?= $total_item ?>"><?= $no++ ?></td>
                            <td rowspan="<?= $total_item ?>">#<?= $p->id_pesanan ?></td>
                            <td rowspan="<?= $total_item ?>"><?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?></td>
                            <td rowspan="<?= $total_item ?>"><?= $p->kasir ?? '-' ?></td>
                        <?php endif; ?>
                        
                        <td class="text-left"><?= $d->nama_produk ?? 'Produk Dihapus' ?></td>
                        <td><?= $d->jumlah ?? 0 ?> <?= $d->satuan ?? '' ?></td>
                        <td>Rp <?= number_format($d->harga ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($d->subtotal ?? 0, 0, ',', '.') ?></td>
                        
                        <?php if($first_row): ?>
                            <td rowspan="<?= $total_item ?>"><?= strtoupper($p->metode_bayar ?? 'TUNAI') ?></td>
                            <td rowspan="<?= $total_item ?>">
                                <strong>Rp <?= number_format($p->total_harga ?? 0, 0, ',', '.') ?></strong>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php 
                        $first_row = false;
                        endforeach; 
                    endif;
                endforeach; 
            endif;
            ?>
            
            <?php if(!empty($penjualan)): ?>
            <tr class="total-row">
                <td colspan="8" style="text-align:right;">
                    <strong>GRAND TOTAL</strong>
                </td>
                <td colspan="2" style="text-align:center; font-size:12px;">
                    <strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="footer">
    <p>Dicetak: <?= date('d F Y H:i:s') ?></p>
    <p>&copy; <?= date('Y') ?> Apotek Bayur Farma - Laporan <?= strtoupper($jenis) ?></p>
</div>

</body>
</html>