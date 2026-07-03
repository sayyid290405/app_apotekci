<div class="page-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-boxes text-success me-2"></i>
                Data Pembelian
            </h3>
            <small class="text-muted">
                Riwayat permintaan dan pembelian barang ke supplier
            </small>
        </div>

        <div>
            <a href="<?= base_url('pembelian/export_excel') ?>" class="btn btn-outline-success me-2">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>

            <a href="<?= base_url('pembelian') ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Buat Permintaan Barang
            </a>
        </div>
    </div> 

    <div class="card shadow-sm border-0">

        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-shopping-cart me-2"></i>
                    Daftar Pembelian
                </span>
                <span class="badge bg-light text-success">
                    Total : <?= count($pembelian) ?>
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th width="60">No</th>
                            <th>Kode Pembelian</th>
                            <th>Supplier</th>
                            <th width="250">Produk</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if(empty($pembelian)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada data pembelian</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php $no = 1; ?>
                    <?php foreach($pembelian as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= $p->kode_pembelian ?? '-' ?></strong>
                        </td>
                        <td>
                            <?= $p->nama_supplier ?? '-' ?>
                        </td>
                        <td style="min-width:250px">
                            <?= $p->nama_produk ?? '-' ?>
                        </td>
                        <td>
                            <?= date('d M Y', strtotime($p->tanggal ?? date('Y-m-d'))) ?>
                        </td>
                        <td>
                            <strong class="text-success">
                                Rp <?= number_format($p->total ?? 0, 0, ',', '.') ?>
                            </strong>
                        </td>
                        <td>
                            <?php if($p->status == 'menunggu'): ?>
                                <span class="badge rounded-pill bg-warning text-dark">Menunggu</span>
                            <?php elseif($p->status == 'disetujui'): ?>
                                <span class="badge rounded-pill bg-primary">Disetujui</span>
                            <?php elseif($p->status == 'diproses'): ?>
                                <span class="badge rounded-pill bg-info text-dark">Diproses</span>
                            <?php elseif($p->status == 'selesai' || $p->status == 'diterima'): ?>
                                <span class="badge rounded-pill bg-success">Selesai</span>
                            <?php elseif($p->status == 'ditolak'): ?>
                                <span class="badge rounded-pill bg-danger">Ditolak</span>
                            <?php elseif($p->status == 'dibatalkan'): ?>
                                <span class="badge rounded-pill bg-secondary">Dibatalkan</span>
                            <?php elseif($p->status == 'Belum Lunas'): ?>
                                <span class="badge rounded-pill bg-danger">Belum Lunas</span>
                            <?php elseif($p->status == 'Lunas'): ?>
                                <span class="badge rounded-pill bg-success">Lunas</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-dark"><?= $p->status ?></span>
                            <?php endif; ?>

                            <a href="<?= base_url('pembelian/detail/'.$p->id_pembelian) ?>" class="btn btn-info btn-sm ms-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                        <td>
                            <?= !empty($p->catatan) ? $p->catatan : '<span class="text-muted">Tidak ada catatan</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.page-container{ padding:20px; }
.card{ border-radius:16px; }
.table th{ white-space:nowrap; vertical-align:middle; }
.table td{ vertical-align:middle; }
.badge{ font-size:.78rem; }
</style>