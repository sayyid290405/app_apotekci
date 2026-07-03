<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Obat - Apotek Gempas Farma</title>
    <style>
        * {
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        body {
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            opacity: 0.8;
            font-size: 13px;
        }
        .header-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card .icon { font-size: 32px; float: right; opacity: 0.3; }
        .stat-card .label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 28px; font-weight: bold; margin: 8px 0 0 0; }
        .stat-card .small-text { font-size: 11px; color: #888; margin-top: 5px; }
        
        .card {
            background: white;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: #f8f9fc;
            padding: 15px 20px;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            font-size: 16px;
        }
        .card-header i { margin-right: 8px; }
        .card-body { padding: 0; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th {
            background: #2c3e50;
            color: white;
            padding: 12px 10px;
            text-align: center;
            font-weight: 600;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
        }
        tr:hover { background: #f8f9fc; }
        
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }
        .progress-fill.danger { background: #dc3545; }
        .progress-fill.warning { background: #ffc107; }
        .progress-fill.success { background: #28a745; }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0069d9; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        
        @media print {
            body { background: white; padding: 0; }
            .btn, .header-buttons, .stats-grid .stat-card:last-child { display: none; }
            .card { box-shadow: none; border: 1px solid #ddd; }
            th { background: #ddd; color: black; }
        }
        
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-success { color: #28a745; }
    </style>
</head>
<body>

<div class="container">
    
    <!-- HEADER -->
    <div class="header">
        <h1><i class="fas fa-boxes"></i> Laporan Stok Obat</h1>
        <p>Apotek Gempas Farma - Jalan Gempol Sari 15520 Sepatan Timur Banten | Telp: 0895-4176-48792</p>
        <p style="margin-top: 8px;">Tanggal Cetak: <?= date('d/m/Y H:i:s') ?> | Dicetak oleh: <?= $this->session->userdata('nama') ?? 'Manajer' ?></p>
        
        <!-- TOMBOL EXPORT -->
        <div class="header-buttons">
            <a href="<?= base_url('dashboard_manajer/export_stok_excel') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> 📊 Export Excel
            </a>
        </div>
    </div>
    
    <!-- STATISTIK CARDS -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #4e73df;">
            <div class="icon">📦</div>
            <div class="label">TOTAL PRODUK</div>
            <div class="value"><?= count($produk) ?></div>
            <div class="small-text">jenis obat</div>
        </div>
        <div class="stat-card" style="border-left-color: #1cc88a;">
            <div class="icon">💊</div>
            <div class="label">TOTAL STOK</div>
            <div class="value"><?= number_format($total_stok) ?></div>
            <div class="small-text">unit</div>
        </div>
        <div class="stat-card" style="border-left-color: #f6c23e;">
            <div class="icon">⚠️</div>
            <div class="label">STOK MENIPIS</div>
            <div class="value"><?= count($stok_minim) ?></div>
            <div class="small-text">produk perlu restock</div>
        </div>
        <div class="stat-card" style="border-left-color: #e74a3b;">
            <div class="icon">📅</div>
            <div class="label">MENDEKATI KADALUARSA</div>
            <div class="value"><?= count($kadaluarsa) ?></div>
            <div class="small-text">≤ 30 hari</div>
        </div>
    </div>
    
    <!-- ALERT PERINGATAN -->
    <?php if(count($stok_minim) > 0 || count($kadaluarsa) > 0): ?>
    <div class="card" style="background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 20px;">
        <div class="card-body" style="padding: 15px 20px;">
            <strong><i class="fas fa-exclamation-triangle"></i> Perhatian!</strong>
            <?php if(count($stok_minim) > 0): ?>
                <span class="badge badge-warning"><?= count($stok_minim) ?> Stok Menipis</span>
            <?php endif; ?>
            <?php if(count($kadaluarsa) > 0): ?>
                <span class="badge badge-info"><?= count($kadaluarsa) ?> Mendekati Kadaluarsa</span>
            <?php endif; ?>
            <span style="float: right;">Segera lakukan tindakan untuk meminimalisir kerugian.</span>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- SEMUA DATA OBAT (DETAIL LENGKAP) -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Semua Obat
            <span style="float: right; font-size: 12px; font-weight: normal;">Total: <?= count($produk) ?> item</span>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table id="tabel-semua-obat">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Minimal</th>
                            <th>Status</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Supplier</th>
                            <th>Tanggal Kadaluarsa</th>
                            <th>Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($produk)): ?>
                        <?php $no=1; foreach($produk as $p): 
                            // Hitung status stok
                            $status_text = '';
                            $status_class = '';
                            $progress_class = '';
                            $progress_width = 0;
                            
                            if(($p->stok ?? 0) <= 0):
                                $status_text = 'HABIS';
                                $status_class = 'badge-danger';
                                $progress_class = 'danger';
                                $progress_width = 0;
                            elseif(($p->stok ?? 0) <= ($p->stok_minimal ?? 5)):
                                $status_text = 'STOK MENIPIS';
                                $status_class = 'badge-warning';
                                $progress_class = 'warning';
                                $progress_width = (($p->stok ?? 0) / max(1, ($p->stok_minimal ?? 5))) * 100;
                            else:
                                $status_text = 'AMAN';
                                $status_class = 'badge-success';
                                $progress_class = 'success';
                                $target = max(1, ($p->stok_minimal ?? 5) * 2);
                                $progress_width = min(100, (($p->stok ?? 0) / $target) * 100);
                            endif;
                            
                            // Hitung sisa hari kadaluarsa
                            $sisa_hari = '-';
                            $expired_class = '';
                            if(!empty($p->tanggal_kadaluarsa)):
                                $sisa = (strtotime($p->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
                                $sisa_hari = floor($sisa);
                                if($sisa <= 0):
                                    $expired_class = 'badge-danger';
                                elseif($sisa <= 30):
                                    $expired_class = 'badge-warning';
                                else:
                                    $expired_class = 'badge-success';
                                endif;
                            endif;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><strong><?= htmlspecialchars($p->nama_produk ?? '-') ?></strong><br><small class="text-muted"><?= $p->satuan_dasar ?? 'unit' ?></small></td>
                            <td><?= $p->nama_kategori ?? '-' ?></td>
                            <td>
                                <?= number_format($p->stok ?? 0) ?>
                                <div class="progress-bar">
                                    <div class="progress-fill <?= $progress_class ?>" style="width: <?= $progress_width ?>%;"></div>
                                </div>
                            </td>
                            <td><?= number_format($p->stok_minimal ?? 5) ?></td>
                            <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                            <td class="text-right">Rp <?= number_format($p->harga_beli ?? 0, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($p->harga_jual ?? 0, 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($p->nama_supplier ?? '-') ?></td>
                            <td><?= !empty($p->tanggal_kadaluarsa) ? date('d/m/Y', strtotime($p->tanggal_kadaluarsa)) : '-' ?></td>
                            <td>
                                <?php if($expired_class): ?>
                                    <span class="badge <?= $expired_class ?>">
                                        <?= $sisa_hari <= 0 ? 'EXPIRED' : $sisa_hari . ' hari' ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="11" class="text-center">Belum ada data produk</td><\/tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- STOK MENIPIS (DETAIL) -->
    <div class="card">
        <div class="card-header" style="background: #fff3cd; color: #856404;">
            <i class="fas fa-exclamation-triangle"></i> Stok Menipis (Stok ≤ Minimal)
        </div>
        <div class="card-body">
            <?php if(!empty($stok_minim)): ?>
            <div style="overflow-x: auto;">
                <table id="tabel-stok-menipis">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Stok Saat Ini</th>
                            <th>Stok Minimal</th>
                            <th>Kekurangan</th>
                            <th>Supplier</th>
                            <th>Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($stok_minim as $s): 
                            $kekurangan = max(0, ($s->stok_minimal ?? 5) - ($s->stok ?? 0));
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><strong><?= htmlspecialchars($s->nama_produk ?? '-') ?></strong><br><small><?= $s->satuan_dasar ?? 'unit' ?></small></td>
                            <td><?= $s->nama_kategori ?? '-' ?></td>
                            <td class="text-warning fw-bold"><?= number_format($s->stok ?? 0) ?></td>
                            <td><?= number_format($s->stok_minimal ?? 5) ?></td>
                            <td><?= number_format($kekurangan) ?></td>
                            <td><?= htmlspecialchars($s->nama_supplier ?? '-') ?></td>
                            <td><span class="badge badge-warning">⚠️ SEGERA ORDER KE SUPPLIER</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="padding: 20px; text-align: center; color: #28a745;">
                <i class="fas fa-check-circle"></i> Semua stok dalam kondisi aman
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- MENDEKATI KADALUARSA (DETAIL) -->
    <div class="card">
        <div class="card-header" style="background: #d1ecf1; color: #0c5460;">
            <i class="fas fa-calendar-times"></i> Obat Mendekati Kadaluarsa (≤ 30 Hari)
        </div>
        <div class="card-body">
            <?php if(!empty($kadaluarsa)): ?>
            <div style="overflow-x: auto;">
                <table id="tabel-kadaluarsa">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Tanggal Kadaluarsa</th>
                            <th>Sisa Hari</th>
                            <th>Stok</th>
                            <th>Estimasi Kerugian</th>
                            <th>Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($kadaluarsa as $k): 
                            $sisa = (strtotime($k->tanggal_kadaluarsa) - strtotime(date('Y-m-d'))) / (60*60*24);
                            $estimasi_rugi = ($k->harga_beli ?? 0) * ($k->stok ?? 0);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><strong><?= htmlspecialchars($k->nama_produk ?? '-') ?></strong><br><small><?= $k->satuan_dasar ?? 'unit' ?></small></td>
                            <td class="text-left"><?= $k->nama_kategori ?? '-' ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($k->tanggal_kadaluarsa)) ?></td>
                            <td class="text-center"><span class="badge badge-warning"><?= floor($sisa) ?> hari</span></td>
                            <td class="text-center"><?= number_format($k->stok ?? 0) ?></td>
                            <td class="text-danger text-right">Rp <?= number_format($estimasi_rugi, 0, ',', '.') ?></td>
                            <td class="text-center"><span class="badge badge-info">⚠️ PERCEPAT PENJUALAN / DISKON</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">TOTAL ESTIMASI KERUGIAN POTENSIAL</th>
                            <th class="text-danger text-right">Rp <?= number_format(array_sum(array_map(function($k) {
                                return ($k->harga_beli ?? 0) * ($k->stok ?? 0);
                            }, $kadaluarsa)), 0, ',', '.') ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div style="padding: 20px; text-align: center; color: #28a745;">
                <i class="fas fa-check-circle"></i> Tidak ada obat yang mendekati kadaluarsa
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

</body>
</html>