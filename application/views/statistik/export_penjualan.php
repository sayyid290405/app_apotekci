<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Apotek</title>
    <style>
        * {
            font-family: 'Arial', sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #555;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background: #f5f5f5;
            font-size: 11px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
        }
        th {
            background-color: #4e73df;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #fff;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .grand-total {
            background: #f8f9fc;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>APOTEK GEMPAS FARMA</h1>
    <p>Jalan Gempol Sari 15520 Sepatan Timur Banten</p>
    <p>Telp: 0895-4176-48792 | Email: apotek@gempasfarma.com</p>
    <h3>LAPORAN PENJUALAN</h3>
</div>

<div class="filter-info">
    <strong>Periode:</strong> <?= date('d/m/Y', strtotime($tgl_awal ?? date('Y-m-01'))) ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir ?? date('Y-m-d'))) ?>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i:s') ?>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Total Transaksi:</strong> <?= count($penjualan ?? []) ?> transaksi
</div>

<table>
    <thead>
        <tr>
            <th width="30">No</th>
            <th width="100">No. Invoice</th>
            <th width="80">Tanggal</th>
            <th width="50">Jam</th>
            <th width="150">Nama Produk</th>
            <th width="40">Qty</th>
            <th width="50">Satuan</th>
            <th width="80">Harga</th>
            <th width="100">Subtotal</th>
            <th width="80">Metode Bayar</th>
            <th width="80">Kasir</th>
            <th width="80">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; 
        $grand_total = 0;
        $total_transaksi = 0;
        $total_tunai = 0;
        $total_transfer = 0;
        $total_qris = 0;
        ?>
        
        <?php if(!empty($penjualan)): ?>
            <?php foreach($penjualan as $p): 
                $invoice = 'INV-' . str_pad($p->id_pesanan, 6, '0', STR_PAD_LEFT);
                $total_transaksi++;
                $grand_total += $p->total_harga;
                
                // Hitung per metode pembayaran
                if($p->metode_bayar == 'tunai') $total_tunai += $p->total_harga;
                elseif($p->metode_bayar == 'transfer') $total_transfer += $p->total_harga;
                elseif($p->metode_bayar == 'qris') $total_qris += $p->total_harga;
                
                // Format metode bayar
                $metode_display = '';
                if($p->metode_bayar == 'tunai') $metode_display = 'TUNAI';
                elseif($p->metode_bayar == 'transfer') $metode_display = 'TRANSFER';
                elseif($p->metode_bayar == 'qris') $metode_display = 'QRIS';
                else $metode_display = strtoupper($p->metode_bayar ?? 'TUNAI');
                
                // Format status
                $status_display = '';
                if($p->status == 'selesai') $status_display = 'SELESAI';
                elseif($p->status == 'pending') $status_display = 'PENDING';
                elseif($p->status == 'dibatalkan') $status_display = 'DIBATALKAN';
                else $status_display = strtoupper($p->status ?? 'SELESAI');
                
                $detail_produk = $p->detail ?? [];
                if(empty($detail_produk)){
                    // Backup ambil dari database
                    $detail_produk = $this->db->select('detail_pesanan.*, produk.nama_produk')
                        ->from('detail_pesanan')
                        ->join('produk', 'produk.id_produk = detail_pesanan.produk_id')
                        ->where('pesanan_id', $p->id_pesanan)
                        ->get()
                        ->result();
                }
                
                $rowspan = max(1, count($detail_produk));
                $first_row = true;
                
                if(count($detail_produk) > 0):
                    foreach($detail_produk as $idx => $d):
        ?>
        <tr>
            <?php if($first_row): ?>
                <td rowspan="<?= $rowspan ?>" class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                <td rowspan="<?= $rowspan ?>" style="vertical-align: middle;">
                    <strong><?= $invoice ?></strong>
                    <?php if($p->tipe_transaksi == 'resep'): ?>
                        <br><small style="color:#8b5cf6;">(RESEP)</small>
                    <?php endif; ?>
                </td>
                <td rowspan="<?= $rowspan ?>" class="text-center" style="vertical-align: middle;">
                    <?= date('d/m/Y', strtotime($p->tanggal_pesan)) ?>
                </td>
                <td rowspan="<?= $rowspan ?>" class="text-center" style="vertical-align: middle;">
                    <?= date('H:i', strtotime($p->tanggal_pesan)) ?>
                </td>
            <?php endif; ?>
            
            <td><?= htmlspecialchars($d->nama_produk ?? '-') ?></td>
            <td class="text-center"><?= $d->jumlah ?? 0 ?></td>
            <td class="text-center"><?= $d->satuan ?? '-' ?></td>
            <td class="text-right">Rp <?= number_format($d->harga ?? 0, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format(($d->subtotal ?? ($d->harga * $d->jumlah ?? 0)), 0, ',', '.') ?></td>
            
            <?php if($first_row): ?>
                <td rowspan="<?= $rowspan ?>" class="text-center" style="vertical-align: middle;">
                    <?= $metode_display ?>
                </td>
                <td rowspan="<?= $rowspan ?>" style="vertical-align: middle;">
                    <?= $p->kasir ?? 'Admin' ?>
                </td>
                <td rowspan="<?= $rowspan ?>" class="text-center" style="vertical-align: middle;">
                    <?= $status_display ?>
                </td>
            <?php endif; ?>
        </tr>
        <?php 
                $first_row = false;
                endforeach;
                else:
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><strong><?= $invoice ?></strong></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($p->tanggal_pesan)) ?></td>
            <td class="text-center"><?= date('H:i', strtotime($p->tanggal_pesan)) ?></td>
            <td colspan="5" class="text-center">- Tidak ada detail produk -</td>
            <td class="text-center"><?= $metode_display ?></td>
            <td><?= $p->kasir ?? 'Admin' ?></td>
            <td class="text-center"><?= $status_display ?></td>
        </tr>
        <?php 
                endif;
            endforeach; 
        else: 
        ?>
        <tr>
            <td colspan="12" class="text-center">Tidak ada data penjualan</td>
        </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>JUMLAH TRANSAKSI</strong></td>
            <td class="text-right"><strong><?= $total_transaksi ?> transaksi</strong></td>
            <td colspan="3"></td>
        </tr>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>RATA-RATA PER TRANSAKSI</strong></td>
            <td class="text-right"><strong>Rp <?= number_format(($total_transaksi > 0 ? $grand_total / $total_transaksi : 0), 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>TUNAI (CASH)</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($total_tunai, 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>TRANSFER BANK</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($total_transfer, 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
        <tr class="grand-total">
            <td colspan="8" class="text-right"><strong>QRIS</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($total_qris, 0, ',', '.') ?></strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <p>Dicetak oleh: <?= $this->session->userdata('nama') ?? 'Administrator' ?> | <?= date('d/m/Y H:i:s') ?></p>
    <p>Laporan ini sah sebagai bukti transaksi apotek</p>
    <p style="font-size: 9px; color: #999;">* Laporan ini dihasilkan secara otomatis oleh sistem</p>
</div>

</body>
</html>