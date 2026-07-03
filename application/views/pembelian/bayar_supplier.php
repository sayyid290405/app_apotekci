<div class="container mt-4">

    <h3><i class="fas fa-money-bill-wave"></i> Manajemen Pembelian Supplier</h3>
    <p class="text-muted">Upload bukti pembayaran dan approve pembelian supplier dalam satu halaman</p>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('dashboard/manajer') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= $this->session->flashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    

   <!-- FILTER STATUS -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-center" id="filterForm">
            <div class="col-auto">
                <label class="fw-bold">Filter Status:</label>
            </div>
            <div class="col-auto">
                <select name="filter" class="form-select" id="filterSelect">
                    <option value="">Semua Status</option>
                    <option value="menunggu" <?= ($filter_aktif ?? '') == 'menunggu' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                    <option value="disetujui" <?= ($filter_aktif ?? '') == 'disetujui' ? 'selected' : '' ?>>Sudah Upload (Menunggu Approve)</option>
                    <option value="selesai" <?= ($filter_aktif ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('pembelian/bayar_supplier') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>


    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Pembelian</th>
                            <th>Supplier</th>
                            <th>Tanggal Order</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Bukti Bayar</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pembelian)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                    <p class="mt-2">Belum ada data pembelian</p>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php $no = 1; foreach($pembelian as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $p->kode_pembelian ?? '-' ?></strong></td>
                                <td><?= $p->nama_supplier ?? '-' ?></td>
                                <td><?= date('d M Y H:i', strtotime($p->tanggal ?? date('Y-m-d'))) ?></td>
                                <td class="fw-bold text-primary">
                                    Rp <?= number_format($p->total ?? 0, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if($p->status == 'selesai'): ?>
                                        <span class="badge bg-success">✅ Selesai</span>
                                    <?php elseif($p->status == 'disetujui'): ?>
                                        <span class="badge bg-info text-dark">📋 Sudah Upload Bukti</span>
                                    <?php elseif($p->status == 'menunggu'): ?>
                                        <span class="badge bg-warning text-dark">⏳ Menunggu Pembayaran</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= $p->status ?? 'Diproses' ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!empty($p->bukti_pembayaran)): ?>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBukti<?= $p->id_pembelian ?>">
                                            <img src="<?= base_url('uploads/bukti/' . $p->bukti_pembayaran) ?>"
                                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Belum upload</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- KASUS 1: MENUNGGU PEMBAYARAN (BELUM UPLOAD BUKTI) -->
                                    <?php if($p->status == 'diterima' && $p->id_transaksi == NULL): ?>
                                        <form action="<?= base_url('Pembelian/bayar/'.$p->id_pembelian) ?>" 
                                              method="post" 
                                              enctype="multipart/form-data"
                                              class="upload-form">
                                            <input type="hidden" 
                                                   name="<?= $this->security->get_csrf_token_name(); ?>" 
                                                   value="<?= $this->security->get_csrf_hash(); ?>">
                                            <div class="mb-2">
                                                <input type="file" 
                                                       name="bukti_pembayaran" 
                                                       class="form-control form-control-sm" 
                                                       accept="image/*" 
                                                       required>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-upload"></i> Upload Bukti
                                            </button>
                                        </form>

                                    <!-- KASUS 2: SUDAH UPLOAD BUKTI (MENUNGGU APPROVE) -->
                                    <?php elseif($p->status == 'disetujui' && $p->id_transaksi != NULL): ?>
                                        <form action="<?= base_url('Pembelian/approve/'.$p->id_pembelian) ?>" 
                                              method="post"
                                              onsubmit="return confirm('Kirim?.')">
                                            <input type="hidden" 
                                                   name="<?= $this->security->get_csrf_token_name(); ?>" 
                                                   value="<?= $this->security->get_csrf_hash(); ?>">
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                <i class="fas fa-check-circle"></i> Approve
                                            </button>
                                        </form>

                                    <!-- KASUS 3: SUDAH SELESAI -->
                                    <?php elseif($p->status == 'selesai'): ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-check"></i> Selesai
                                        </button>
                                        <a href="<?= base_url('Pembelian/cetak/'.$p->id_pembelian) ?>" 
                                           class="btn btn-info btn-sm w-100 mt-1" target="_blank">
                                            <i class="fas fa-print"></i> Cetak Invoice
                                        </a>

                                    <!-- KASUS 4: STATUS LAIN -->
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-hourglass"></i> <?= $p->status ?? 'Diproses' ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- MODAL PREVIEW BUKTI -->
                            <div class="modal fade" id="modalBukti<?= $p->id_pembelian ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bukti Pembayaran - <?= $p->kode_pembelian ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="<?= base_url('uploads/bukti/' . $p->bukti_pembayaran) ?>" 
                                                 class="img-fluid rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan JavaScript untuk auto-submit -->
<script>
document.getElementById('filterSelect').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
</script>
<style>
.upload-form {
    width: 100%;
}

.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

.btn-sm {
    font-size: 0.8rem;
}
</style>

<script>
// Auto dismiss alert after 5 seconds
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        let bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>