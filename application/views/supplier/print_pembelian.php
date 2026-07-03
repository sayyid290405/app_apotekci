<!-- file: views/laporan_supplier/print_pembelian.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembelian - <?= $pembelian->kode_pembelian ?></title>
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
            padding: 20px;
            background: #fff;
        }
        .invoice-container {
            max-width: 100%;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 4px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h1 {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #555;
            margin: 2px 0;
        }
        .info-row {
            margin-bottom: 20px;
            width: 100%;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            border: none;
            vertical-align: top;
        }
        .info-table td.label {
            width: 25%;
            font-weight: bold;
        }
        .supplier-box {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
            font-size: 10px;
        }
        .supplier-box strong {
            color: #2c3e50;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.items th {
            background: #34495e;
            color: white;
            padding: 8px 5px;
            font-size: 10px;
            text-align: center;
            border: 1px solid #2c3e50;
        }
        table.items td {
            padding: 6px 5px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background: #ecf0f1;
            font-weight: bold;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px dashed #aaa;
            padding-top: 15px;
            text-align: center;
            font-size: 9px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            background-color: #6c757d;
        }
        .status-selesai { background-color: #28a745; }
        .status-disetujui { background-color: #007bff; }
        .status-diproses { background-color: #ffc107; color: #000; }
        .status-diterima { background-color: #17a2b8; }
        .status-ditolak, .status-dibatalkan { background-color: #dc3545; }
        
        /* Tanda tangan dengan tabel agar kompatibel PDF */
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            padding-top: 20px;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <h1>NOTA PEMBELIAN</h1>
        <p>Apotek Gempas Farma</p>
        <p>Jl. Gempol Sari Sepatan Timur, Banten 15520 | Telp: 0895-4176-48792</p>
        <p>Email: apotek@gempas.com</p>
    </div>

    <!-- Informasi Transaksi -->
    <div class="info-row">
        <table class="info-table">
            <tr>
                <td class="label">Kode Order</td>
                <td>: <?= htmlspecialchars($pembelian->kode_pembelian) ?></td>
                <td class="label">Tanggal Order</td>
                <td>: <?= date('d/m/Y H:i:s', strtotime($pembelian->tanggal)) ?></td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>: 
                    <?php 
                    $status_class = '';
                    if ($pembelian->status == 'selesai') $status_class = 'status-selesai';
                    elseif ($pembelian->status == 'disetujui') $status_class = 'status-disetujui';
                    elseif ($pembelian->status == 'diproses') $status_class = 'status-diproses';
                    elseif ($pembelian->status == 'diterima') $status_class = 'status-diterima';
                    elseif ($pembelian->status == 'ditolak' || $pembelian->status == 'dibatalkan') $status_class = 'status-ditolak';
                    ?>
                    <span class="status-badge <?= $status_class ?>"><?= ucfirst($pembelian->status) ?></span>
                </td>
                <td class="label">Total</td>
                <td>: <strong>Rp <?= number_format($pembelian->total, 0, ',', '.') ?></strong></td>
            </tr>
            <tr>
                <td class="label">User Pembuat</td>
                <td>: <?= htmlspecialchars($pembelian->user_name ?? '-') ?></td>
                <td class="label">Disetujui Oleh</td>
                <td>: <?= htmlspecialchars($pembelian->approved_by_name ?? '-') ?> 
                    <?= $pembelian->tanggal_approve ? '(' . date('d/m/Y', strtotime($pembelian->tanggal_approve)) . ')' : '' ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi Supplier -->
    <div class="supplier-box">
        <strong>Data Supplier:</strong><br>
        Nama: <?= htmlspecialchars($pembelian->nama_supplier) ?><br>
        Alamat: <?= nl2br(htmlspecialchars($pembelian->alamat ?? '-')) ?><br>
        Kontak: <?= htmlspecialchars($pembelian->kontak ?? '-') ?> | Legalitas: <?= htmlspecialchars($pembelian->legalitas ?? '-') ?>
    </div>

    <!-- Daftar Produk -->
    <h4 style="margin: 10px 0 5px 0;">Detail Produk</h4>
    <table class="items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Produk</th>
                <th width="15%">Jumlah</th>
                <th width="17%">Harga Satuan</th>
                <th width="18%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detail)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data produk</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($detail as $item): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($item->nama_produk) ?>
                        <?php if ($item->nama_satuan): ?>
                            <small>(<?= $item->nama_satuan ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= number_format($item->jumlah) ?> pcs</td>
                    <td class="text-right">Rp <?= number_format($item->harga, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
                <td class="text-right"><strong>Rp <?= number_format($pembelian->total, 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Catatan -->
    <?php if (!empty($pembelian->catatan)): ?>
    <div style="margin-top: 15px; padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107;">
        <strong>Catatan:</strong> <?= nl2br(htmlspecialchars($pembelian->catatan)) ?>
    </div>
    <?php endif; ?>

    <!-- Tanda Tangan dengan tabel, tampil ke samping (horizontal) -->
    <table class="signature-table">
        <tr>
            <td>
                Diterima oleh,<br><br><br>
                <u>(_________________)</u><br>
                <small>Penerima Barang</small>
            </td>
            <td>
                Hormat kami,<br><br><br>
                <u>(_________________)</u><br>
                <small>Admin Apotek</small>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara elektronik pada <?= date('d/m/Y H:i:s') ?> dan merupakan bukti transaksi yang sah.</p>
        <p>Terima kasih atas kerja samanya.</p>
    </div>
</div>
</body>
</html>