 <div class="page-container">


<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="fw-bold mb-1">
            <i class="fas fa-file-invoice-dollar text-success me-2"></i>
            Riwayat Pembelian & Pembayaran
        </h3>

        <small class="text-muted">
            Monitoring transaksi pembelian dan pembayaran supplier
        </small>

    </div>

    <div>

        <span class="badge bg-success px-3 py-2">

            Total Data :
            <?= count($pembayaran ?? []) ?>

        </span>

    </div>

</div>

<!-- FILTER -->
<div class="card modern-card mb-4">

    <div class="card-body">

        <form method="get">

            <div class="row g-3">

                <div class="col-md-8">

                    <label class="form-label">
                        Filter Status Pembayaran
                    </label>

                    <select
                        name="status_bayar"
                        class="form-select">

                        <option value="">
                            Semua
                        </option>

                        <option value="lunas"
                            <?= ($this->input->get('status_bayar') == 'lunas') ? 'selected' : '' ?>>
                            Lunas
                        </option>

                        <option value="belum lunas"
                            <?= ($this->input->get('status_bayar') == 'belum lunas') ? 'selected' : '' ?>>
                            Belum Lunas
                        </option>

                    </select>

                </div>

                <div class="col-md-4 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary me-2">

                        <i class="fas fa-filter"></i>
                        Filter

                    </button>

                    <a href="<?= base_url('supplier/pembelian_selesai') ?>"
                       class="btn btn-secondary">

                        <i class="fas fa-sync-alt"></i>
                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- TABLE -->
<div class="card modern-card">

    <div class="card-header bg-success text-white">

        <i class="fas fa-history me-2"></i>
        Riwayat Pembelian Supplier

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-success">

                    <tr>

                        <th width="60">No</th>

                        <th>Kode Pembelian</th>

                        <th>Supplier</th>

                        <th>Item Produk</th>

                        <th>Tanggal</th>

                        <th>Total Harga</th>

                        <th>Status Pembayaran</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(empty($pembayaran)): ?>

                    <tr>

                        <td colspan="7" class="text-center py-4">

                            <div class="text-muted">

                                <i class="fas fa-inbox fa-2x mb-2"></i>

                                <p class="mb-0">
                                    Data kosong
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                <?php $no = 1; ?>

                <?php foreach($pembayaran as $p): ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td>
                            <strong>
                                <?= $p->kode_pembelian ?? '-' ?>
                            </strong>
                        </td>

                        <td>
                            <?= $p->nama_supplier ?? '-' ?>
                        </td>

                        <td>
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

                        <td>

                            <?php if(
                                strtolower($p->status_bayar ?? '')
                                == 'lunas'
                            ): ?>

                                <span class="badge bg-success">

                                    Lunas

                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger">

                                    Belum Lunas

                                </span>

                            <?php endif; ?>

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
    font-weight:600;
}

.table th{
    white-space:nowrap;
    font-weight:600;
    vertical-align:middle;
}

.table td{
    vertical-align:middle;
}

.btn{
    border-radius:10px;
}

.form-control,
.form-select{
    border-radius:10px;
}

.badge{
    font-size:.8rem;
}

@media(max-width:768px){

    .page-container{
        padding:15px;
    }

}

</style>
