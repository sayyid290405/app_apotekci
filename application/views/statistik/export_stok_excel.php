<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat - Apotek Gempas Farma</title>
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
            font-size: 18px;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
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
            margin-bottom: 20px;
            font-size: 11px;
        }
        th {
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #fff;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bg-warning { background: #fff3cd; }
        .bg-danger { background: #f8d7da; }
        .bg-success { background: #d4edda; }
        .bg-info { background: #d1ecf1; }
        .sub-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            background: #e9ecef;
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>APOTEK GEMPAS FARMA</h1>
    <p>Jalan Gempol Sari 15520 Sepatan Timur Banten</p>
    <p>Telp: 0895-4176-48792 | Email: apotek@gempasfarma.com</p>
    <h3>LAPORAN STOK OBAT</h3>
</div>

<div class="filter-info">
    <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i:s') ?>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Total Produk:</strong> <?= count($produk) ?> item
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <strong>Total Stok:</strong> <?= number_format($total_stok) ?> unit
</div>

<!-- ================= SEMUA PRODUK ================= -->
<div class="sub-title">A. DAFTAR SEMUA OBAT</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Stok Minimal</th>
            <th>Status</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Supplier</th>
            <th>Tgl Kadaluarsa</th>
            <th>Sisa Hari</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($produk)): ?>
        <?php $no=1; foreach($produk as $p): 
            // Status stok
            if(($p->stok ?? 0) <= 0):
                $status = 'HABIS';
            elseif(($p->stok ?? 0) <= ($p->stok_minimal ?? 5)):
                $status = 'STOK MENIPIS';
            else:
                $status = 'AMAN';
            endif;
            
            // Sisa hari kadaluarsa
            $sisa_hari = '-';
            if(!empty($p->tanggal_kadaluarsa)):
                $sisa = (strtotime($p->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
                if($sisa <= 0):
                    $sisa_hari = 'EXPIRED';
                elseif($sisa <= 30):
                    $sisa_hari = floor($sisa) . ' hari';
                else:
                    $sisa_hari = floor($sisa) . ' hari';
                endif;
            endif;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($p->nama_produk ?? '-') ?></td>
            <td class="text-center"><?= $p->satuan_dasar ?? 'unit' ?></td>
            <td><?= $p->nama_kategori ?? '-' ?></td>
            <td class="text-center"><?= number_format($p->stok ?? 0) ?></td>
            <td class="text-center"><?= number_format($p->stok_minimal ?? 5) ?></td>
            <td class="text-center"><?= $status ?></td>
            <td class="text-right">Rp <?= number_format($p->harga_beli ?? 0, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($p->harga_jual ?? 0, 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($p->nama_supplier ?? '-') ?></td>
            <td class="text-center"><?= !empty($p->tanggal_kadaluarsa) ? date('d/m/Y', strtotime($p->tanggal_kadaluarsa)) : '-' ?></td>
            <td class="text-center"><?= $sisa_hari ?></td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="12" class="text-center">Tidak ada data produk</td><\/tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">TOTAL</th>
            <th class="text-center"><?= number_format($total_stok) ?></th>
            <th colspan="7"></th>
        </tr>
    </tfoot>
</table>

<!-- ================= STOK MENIPIS ================= -->
<div class="sub-title">B. STOK MENIPIS (Stok ≤ Minimal)</div>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Stok Saat Ini</th>
            <th>Stok Minimal</th>
            <th>Kekurangan</th>
            <th>Supplier</th>
            <th>Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($stok_minim)): ?>
        <?php $no=1; foreach($stok_minim as $s): 
            $kekurangan = max(0, ($s->stok_minimal ?? 5) - ($s->stok ?? 0));
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($s->nama_produk ?? '-') ?></td>
            <td class="text-center"><?= $s->satuan_dasar ?? 'unit' ?></td>
            <td><?= $s->nama_kategori ?? '-' ?></td>
            <td class="text-center"><?= number_format($s->stok ?? 0) ?></td>
            <td class="text-center"><?= number_format($s->stok_minimal ?? 5) ?></td>
            <td class="text-center"><?= number_format($kekurangan) ?></td>
            <td><?= htmlspecialchars($s->nama_supplier ?? '-') ?></td>
            <td>Segera order ke supplier</td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="9" class="text-center">Tidak ada stok menipis</td><\/tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <?php 
        $total_kekurangan = 0;
        foreach($stok_minim as $s):
            $total_kekurangan += max(0, ($s->stok_minimal ?? 5) - ($s->stok ?? 0));
        endforeach;
        ?>
        <tr>
            <th colspan="6" class="text-right">TOTAL KEKURANGAN</th>
            <th class="text-center"><?= number_format($total_kekurangan) ?></th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>

<!-- ================= MENDEKATI KADALUARSA ================= -->
<div class="sub-title">C. OBAT MENDEKATI KADALUARSA (≤ 30 Hari)</div>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Tgl Kadaluarsa</th>
            <th>Sisa Hari</th>
            <th>Stok</th>
            <th>Harga Beli</th>
            <th>Estimasi Kerugian</th>
            <th>Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($kadaluarsa)): ?>
        <?php $no=1; $total_estimasi_rugi = 0; foreach($kadaluarsa as $k): 
            $sisa = (strtotime($k->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
            $estimasi_rugi = ($k->harga_beli ?? 0) * ($k->stok ?? 0);
            $total_estimasi_rugi += $estimasi_rugi;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($k->nama_produk ?? '-') ?></td>
            <td class="text-center"><?= $k->satuan_dasar ?? 'unit' ?></td>
            <td><?= $k->nama_kategori ?? '-' ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($k->tanggal_kadaluarsa)) ?></td>
            <td class="text-center"><?= floor($sisa) ?> hari</td>
            <td class="text-center"><?= number_format($k->stok ?? 0) ?></td>
            <td class="text-right">Rp <?= number_format($k->harga_beli ?? 0, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($estimasi_rugi, 0, ',', '.') ?></td>
            <td>Percepat penjualan / beri diskon</td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="8" class="text-right">TOTAL ESTIMASI KERUGIAN</th>
            <th class="text-right">Rp <?= number_format($total_estimasi_rugi, 0, ',', '.') ?></th>
            <th></th>
        </tr>
        <?php else: ?>
        <tr><td colspan="10" class="text-center">Tidak ada obat mendekati kadaluarsa</td><\/tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- ================= STATISTIK PER KATEGORI ================= -->
<div class="sub-title">D. STATISTIK PER KATEGORI</div>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Jumlah Produk</th>
            <th>Total Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Hitung statistik per kategori
        $stat_kategori = [];
        foreach($produk as $p):
            $kat = $p->nama_kategori ?? 'Tidak Berkategori';
            if(!isset($stat_kategori[$kat])):
                $stat_kategori[$kat] = ['produk' => 0, 'stok' => 0];
            endif;
            $stat_kategori[$kat]['produk']++;
            $stat_kategori[$kat]['stok'] += ($p->stok ?? 0);
        endforeach;
        
        $no = 1;
        foreach($stat_kategori as $nama => $data):
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= $nama ?></td>
            <td class="text-center"><?= number_format($data['produk']) ?></td>
            <td class="text-center"><?= number_format($data['stok']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-right">TOTAL</th>
            <th class="text-center"><?= count($produk) ?></th>
            <th class="text-center"><?= number_format($total_stok) ?></th>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 30px; text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 10px;">
    <p>Dicetak oleh: <?= $this->session->userdata('nama') ?? 'Manajer' ?> | <?= date('d/m/Y H:i:s') ?></p>
    <p>Laporan ini sah sebagai bukti stok apotek</p>
</div>

</body>
</html>