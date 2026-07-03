<div class="page-container">

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="fw-bold mb-1">
            <i class="fas fa-chart-line text-success me-2"></i>
            Laporan Pembelian Supplier
        </h3>

        <small class="text-muted">
            Monitoring transaksi pembelian obat yang telah lunas
        </small>

    </div>

    <div>

        <span class="badge bg-success px-3 py-2">

            Total Data :
            <?= count($laporan ?? []) ?>

        </span>

    </div>

</div>

<!-- FILTER -->
<div class="card modern-card mb-4">

    <div class="card-body">

        <form method="get">

            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">
                        Tanggal Awal
                    </label>

                    <input
                        type="date"
                        name="tgl_awal"
                        class="form-control"
                        value="<?= $this->input->get('tgl_awal') ?>">

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        name="tgl_akhir"
                        class="form-control"
                        value="<?= $this->input->get('tgl_akhir') ?>">

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Cari Data
                    </label>

                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Kode / Supplier / Produk"
                        value="<?= $this->input->get('q') ?>">

                </div>

                <div class="col-md-3 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-success me-2">

                        <i class="fas fa-filter"></i>
                        Filter

                    </button>

                    <a
                        href="<?= base_url('supplier/laporan') ?>"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- ACTION -->
<div class="mb-3">

    <a href="<?= base_url('supplier/laporan_pdf?'.http_build_query($_GET)) ?>"
       target="_blank"
       class="btn btn-danger">

        <i class="fas fa-file-pdf"></i>
        Export PDF

    </a>

    <a href="<?= base_url('supplier/laporan_excel?'.http_build_query($_GET)) ?>"
       class="btn btn-success">

        <i class="fas fa-file-excel"></i>
        Export Excel

    </a>

</div>

<!-- TABLE -->
<div class="card modern-card">

    <div class="card-header bg-success text-white">

        <i class="fas fa-table me-2"></i>
        Data Laporan

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-success">

                    <tr>

                        <th>No</th>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(empty($laporan)): ?>

                    <tr>

                        <td colspan="8"
                            class="text-center py-4">

                            Data tidak ditemukan

                        </td>

                    </tr>

                <?php endif; ?>

                <?php
                $no = 1;
                $grand_total = 0;
                ?>

                <?php foreach($laporan as $row): ?>

                <?php
                $grand_total += $row->total;
                ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>

                        <strong>
                            <?= $row->kode_pembelian ?>
                        </strong>

                    </td>

                    <td>

                        <?= date(
                            'd M Y',
                            strtotime($row->tanggal)
                        ) ?>

                    </td>

                    <td>

                        <?= $row->nama_supplier ?>

                    </td>

                    <td>

                        <?= $row->nama_produk ?>

                    </td>

                    <td>

                        <?= $row->jumlah ?>

                    </td>

                    <td>

                        Rp <?= number_format(
                            $row->total,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </td>

                    <td>

                        <span class="badge bg-success">

                            LUNAS

                        </span>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

                <tfoot>

                    <tr>

                        <th colspan="6"
                            class="text-end">

                            Total Pembelian

                        </th>

                        <th colspan="2"
                            class="text-success">

                            Rp <?= number_format(
                                $grand_total,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </th>

                    </tr>

                </tfoot>

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
    font-weight:600;
}

.table th{
    white-space:nowrap;
}

.btn{
    border-radius:10px;
}

.form-control{
    border-radius:10px;
}

.badge{
    font-size:.8rem;
}

</style>
