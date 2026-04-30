<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Obat</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }
        .container {
            width: 95%;
            margin: auto;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h3 {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background: #2c3e50;
            color: white;
        }
        .total {
            font-size: 22px;
            font-weight: bold;
            color: #27ae60;
        }
        .stok-minim {
            background-color: #ffe6e6;
        }
        .kadaluarsa {
            background-color: #fff3cd;
        }
    </style>
</head>
<body>

<!-- TOTAL tetap full -->
<div class="card">


<div class="card shadow mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <div class="text-xs font-bold text-primary text-uppercase mb-1">
                Total Obat Tersedia
            </div>
            <div class="h4 font-weight-bold text-gray-800"><?= $total_stok ?></div>
        </div>
        <a href="<?= base_url('dashboard_manajer/export_stok') ?>" 
           class="btn btn-success btn-sm">
            Export Excel
        </a>
    </div>
</div>

<!-- GRID KIRI KANAN -->
 
<div class="grid">

    <!-- SEMUA PRODUK -->
    <div class="card">
        <h3>Semua Data Obat</h3>
        
        <table>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Stok</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Supplier Vendor</th>
            </tr>

            <?php $no=1; foreach($produk as $p): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $p->nama_produk ?></td>
                <td><?= $p->stok ?></td>
                <td><?= $p->tanggal_kadaluarsa ?></td>
                <td><?= isset($p->nama_supplier) ? $p->nama_supplier : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- STOK MINIM -->
    <div class="card">
        <h3>Stok Menipis</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Stok</th>
            </tr>

            <?php $no=1; foreach($stok_minim as $s): ?>
            <tr class="stok-minim">
                <td><?= $no++ ?></td>
                <td><?= $s->nama_produk ?></td>
                <td><?= $s->stok ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- KADALUARSA -->
    <div class="card">
        <h3>Mendekati Kadaluarsa</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Tanggal</th>
                <th>Sisa Hari</th>
            </tr>

            <?php $no=1; foreach($kadaluarsa as $k): 
                $sisa = (strtotime($k->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
            ?>
            <tr class="kadaluarsa">
                <td><?= $no++ ?></td>
                <td><?= $k->nama_produk ?></td>
                <td><?= $k->tanggal_kadaluarsa ?></td>
                <td><?= floor($sisa) ?> hari</td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>