<div class="container-fluid px-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Laporan Pembelian Supplier
        </h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('supplier/export_excel?' . http_build_query($_GET)) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- STATISTIK CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase">Total Pembelian</small>
                            <h3 class="mb-0 text-primary"><?= number_format($summary->jumlah_transaksi ?? 0) ?></h3>
                            <small class="text-muted">Transaksi</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-shopping-cart text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase">Total Nilai</small>
                            <h3 class="mb-0 text-success">Rp <?= number_format($summary->total_nominal ?? 0, 0, ',', '.') ?></h3>
                            <small class="text-muted">Keseluruhan</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-money-bill-wave text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase">Transaksi Selesai</small>
                            <h3 class="mb-0 text-info"><?= number_format($status_count['selesai'] ?? 0) ?></h3>
                            <small class="text-muted">Order</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-check-double text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-uppercase">Proses Aktif</small>
                            <h3 class="mb-0 text-warning"><?= number_format(($status_count['menunggu'] ?? 0) + ($status_count['diproses'] ?? 0)) ?></h3>
                            <small class="text-muted">Order</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-hourglass-half text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER FORM -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Limit</label>
                    <select name="limit" class="form-select">
                        <option value="all" <?= ($limit ?? '') == 'all' ? 'selected' : '' ?>>Semua Data</option>
                        <option value="10" <?= ($limit ?? '') == '10' ? 'selected' : '' ?>>10 Terakhir</option>
                        <option value="20" <?= ($limit ?? '') == '20' ? 'selected' : '' ?>>20 Terakhir</option>
                        <option value="50" <?= ($limit ?? '') == '50' ? 'selected' : '' ?>>50 Terakhir</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Kode / Supplier / Status" value="<?= $search ?? '' ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL PEMBELIAN -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-list-alt me-2 text-primary"></i>
                Data Pembelian
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Pembelian</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>User</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pembelian)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    Belum ada data pembelian
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($pembelian as $p): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <span class="fw-bold text-primary"><?= $p->kode_pembelian ?? '-' ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($p->tanggal)) ?></td>
                                    <td><?= $p->nama_supplier ?? '-' ?></td>
                                    <td class="text-center"><?= number_format($p->total_item ?? 0) ?> item</td>
                                    <td class="fw-bold text-success">Rp <?= number_format($p->total ?? 0, 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        if($p->status == 'selesai') $badge_class = 'success';
                                        elseif($p->status == 'ditolak' || $p->status == 'dibatalkan') $badge_class = 'danger';
                                        elseif($p->status == 'disetujui') $badge_class = 'primary';
                                        elseif($p->status == 'diproses') $badge_class = 'warning';
                                        elseif($p->status == 'menunggu') $badge_class = 'info';
                                        ?>
                                        <span class="badge bg-<?= $badge_class ?> px-3 py-2">
                                            <?= ucfirst($p->status ?? 'Menunggu') ?>
                                        </span>
                                    </td>
                                    <td><?= $p->user_name ?? '-' ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('pembelian/detail_supplier/' . $p->id_pembelian) ?>" class="btn btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('pembelian/cetak/' . $p->id_pembelian) ?>" class="btn btn-outline-secondary" title="Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TOP PRODUK TERBANYAK DIBELI -->
    <?php if(!empty($summary->produk_terlaris ?? [])): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-trophy me-2 text-warning"></i>
                Top 10 Produk Terbanyak Dibeli
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach($summary->produk_terlaris as $produk): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="small text-muted text-truncate" style="max-width: 150px;"><?= $produk->nama_produk ?? '-' ?></div>
                                        <h5 class="mb-0 text-success mt-2"><?= number_format($produk->total_dibeli ?? 0) ?></h5>
                                        <small class="text-muted">pcs</small>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                        <i class="fas fa-box text-warning"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-sync-alt me-1"></i> <?= number_format($produk->frekuensi ?? 0) ?>x transaksi
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<style>
    .bg-opacity-10 {
        background-color: rgba(0,0,0,0.05) !important;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 11px;
        font-weight: 500;
    }
    @media print {
        .btn, .btn-group, form, .d-flex.gap-2, .bg-opacity-10 {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        body {
            padding: 0;
            margin: 0;
        }
    }
</style>