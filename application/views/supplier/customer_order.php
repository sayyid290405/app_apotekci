<div class="page-container">

    <!-- HEADER PAGE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-shopping-cart text-success me-2"></i>
                Customer Order
            </h3>

            <small class="text-muted">
                Kelola permintaan pengadaan barang dari apotek
            </small>
        </div>

        <div>
            <span class="badge bg-success px-3 py-2">
                Total Data :
                <?= count($pembelian ?? []) ?>
            </span>
        </div>

    </div>

    <!-- CARD -->
    <div class="card modern-card">

        <div class="card-header bg-success text-white">

            <i class="fas fa-boxes me-2"></i>
            List Permohonan Pengadaan Barang

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-success">

                        <tr>

                            <th width="60">No</th>

                            <th>Kode Pembelian</th>

                            <th>Supplier</th>

                            <th>Tanggal</th>

                            <th>Item Produk</th>

                            <th>Total Pembelian</th>

                            <th width="180" class="text-center">
                                Status Approval
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($pembelian)): ?>

                        <tr>

                            <td colspan="7" class="text-center py-4">

                                <div class="text-muted">

                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                    <p class="mb-0">
                                        Belum ada data pembelian
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php $no = 1; ?>

                    <?php foreach($pembelian as $p): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>

                                <strong class="text-primary">
                                    <?= $p->kode_pembelian ?? '-' ?>
                                </strong>

                            </td>

                            <td>

                                <?= $p->nama_supplier ?? '-' ?>

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

                                <?= $p->nama_produk ?? '-' ?>

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

                            <td>

                                <form
                                    method="post"
                                    action="<?= base_url('supplier/update_status_supplier') ?>">

                                    <input
                                        type="hidden"
                                        name="id_pembelian"
                                        value="<?= $p->id_pembelian ?>">

                                    <input
                                        type="hidden"
                                        name="<?= $this->security->get_csrf_token_name(); ?>"
                                        value="<?= $this->security->get_csrf_hash(); ?>">

                                    <?php

                                        $class = 'bg-warning text-dark';

                                        if($p->status == 'diproses'){

                                            $class = 'bg-success text-white';

                                        }elseif($p->status == 'ditolak'){

                                            $class = 'bg-danger text-white';

                                        }

                                    ?>

                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="form-select form-select-sm status-select <?= $class ?>">

                                        <option
                                            value="disetujui"
                                            <?= $p->status == 'disetujui'
                                                ? 'selected'
                                                : '' ?>>

                                            Disetujui

                                        </option>

                                        <option
                                            value="diproses"
                                            <?= $p->status == 'diproses'
                                                ? 'selected'
                                                : '' ?>>

                                            Diproses

                                        </option>

                                        <option
                                            value="ditolak"
                                            <?= $p->status == 'ditolak'
                                                ? 'selected'
                                                : '' ?>>

                                            Ditolak

                                        </option>

                                    </select>

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
    padding:24px;
}

.modern-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 4px 16px rgba(0,0,0,.08);
}

.card-header{
    border:none;
    padding:15px 20px;
    font-weight:600;
}

.table th{
    white-space:nowrap;
    vertical-align:middle;
}

.table td{
    vertical-align:middle;
}

.status-select{
    border-radius:10px !important;
    font-weight:600;
    cursor:pointer;
}

</style>