<div class="page-container">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-truck-loading text-success me-2"></i>
                Verifikasi Penerimaan Barang
            </h3>

            <small class="text-muted">
                Kelola dan verifikasi barang yang telah dikirim supplier
            </small>
        </div>

        <div>
            <span class="badge bg-success fs-6">
                Total Data :
                <?= count($pembelian_diproses) ?>
            </span>
        </div>

    </div>

    <!-- CARD -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-success text-white">

            <div class="d-flex justify-content-between align-items-center">

                <span>
                    <i class="fas fa-boxes me-2"></i>
                    List Barang Tiba
                </span>

                <span class="badge bg-light text-success">
                    <?= date('d M Y') ?>
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

                            <th width="280">Produk</th>

                            <th>Tanggal</th>

                            <th>Total Harga</th>

                            <th>Status</th>

                            <th>Catatan</th>

                            <th width="250">
                                Verifikasi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($pembelian_diproses)): ?>

                        <tr>

                            <td colspan="9" class="text-center py-4">

                                <div class="text-muted">

                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                    <p class="mb-0">
                                        Tidak ada data barang tiba
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php $no = 1; ?>

                    <?php foreach($pembelian_diproses as $p): ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>

                        <td>

                            <strong>
                                <?= $p->kode_pembelian ?? '-' ?>
                            </strong>

                        </td>

                        <td>

                            <?= $p->nama_supplier ?? '-' ?>

                        </td>

                        <td style="min-width:250px">

                            <?= $p->nama_produk ?? '-' ?>

                        </td>

                        <td>

                            <?= date(
                                'd M Y',
                                strtotime(
                                    $p->tanggal ?? date('Y-m-d')
                                )
                            ) ?>

                        </td>

                        <td>

                            <strong class="text-success">

                                Rp <?= number_format(
                                    $p->total ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>

                        <!-- STATUS -->
                        <td>

                            <?php if($p->status == 'menunggu'): ?>

                                <span class="badge rounded-pill bg-warning text-dark">
                                    Menunggu
                                </span>

                            <?php elseif($p->status == 'disetujui'): ?>

                                <span class="badge rounded-pill bg-primary">
                                    Disetujui
                                </span>

                            <?php elseif($p->status == 'diproses'): ?>

                                <span class="badge rounded-pill bg-info text-dark">
                                    Diproses
                                </span>

                            <?php elseif(
                                $p->status == 'diterima'
                                || $p->status == 'diterima'
                            ): ?>

                                <span class="badge rounded-pill bg-success">
                                    Diterima
                                </span>

                            <?php elseif($p->status == 'ditolak'): ?>

                                <span class="badge rounded-pill bg-danger">
                                    Ditolak
                                </span>

                            <?php elseif($p->status == 'dibatalkan'): ?>

                                <span class="badge rounded-pill bg-secondary">
                                    Dibatalkan
                                </span>

                            <?php else: ?>

                                <span class="badge rounded-pill bg-dark">
                                    Unknown
                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- CATATAN -->
                        <td>

                            <?= !empty($p->catatan)
                                ? $p->catatan
                                : '<span class="text-muted">Tidak ada catatan</span>' ?>

                        </td>

                        <!-- VERIFIKASI -->
                        <td>

                            <form
                                method="post"
                                action="<?= base_url('supplier/update_status_admin') ?>"
                                class="d-flex gap-2 align-items-center">

                                <input
                                    type="hidden"
                                    name="id_pembelian"
                                    value="<?= $p->id_pembelian ?>">

                                <input
                                    type="hidden"
                                    name="<?= $this->security->get_csrf_token_name(); ?>"
                                    value="<?= $this->security->get_csrf_hash(); ?>">

                                <select
                                    name="status"
                                    class="form-select form-select-sm">

                                    <option
                                        value="diproses"
                                        <?= $p->status == 'diproses'
                                            ? 'selected'
                                            : '' ?>>
                                        Diproses
                                    </option>

                                    <option
                                        value="diterima"
                                        <?= (
                                            $p->status == 'diterima'
                                            || $p->status == 'diterima'
                                        )
                                            ? 'selected'
                                            : '' ?>>
                                        diterima
                                    </option>

                                </select>

                                <button
                                    type="submit"
                                    class="btn btn-success btn-sm">

                                    <i class="fas fa-save"></i>

                                </button>

                            </form>

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

.page-container{
    padding:20px;
}

.card{
    border-radius:16px;
}

.table th{
    white-space:nowrap;
    vertical-align:middle;
}

.table td{
    vertical-align:middle;
}

.badge{
    font-size:.78rem;
}

.form-select-sm{
    min-width:120px;
}

</style>