<div class="page-container">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                <i class="fas fa-truck text-success me-2"></i>
                Data Supplier
            </h3>

            <small class="text-muted">
                Kelola dan lihat informasi supplier yang terdaftar
            </small>

        </div>

        <div>

            <span class="badge bg-success px-3 py-2">

                Total Supplier :
                <?= count($supplier ?? []) ?>

            </span>

        </div>

    </div>

    <!-- SEARCH CARD -->
    <div class="card modern-card mb-4">

        <div class="card-body">

            <form method="get">

                <div class="input-group">

                    <span class="input-group-text bg-white">

                        <i class="fas fa-search text-success"></i>

                    </span>

                    <input
                        type="text"
                        name="q"
                        value="<?= $keyword ?? '' ?>"
                        class="form-control"
                        placeholder="Cari supplier...">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Cari

                    </button>

                </div>

            </form>

        </div>

    </div>

    <!-- DATA CARD -->
    <div class="card modern-card">

        <div class="card-header bg-success text-white">

            <div class="d-flex justify-content-between align-items-center">

                <span>

                    <i class="fas fa-warehouse me-2"></i>
                    Daftar Supplier

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

                            <th width="60">#</th>

                            <th>Nama Supplier</th>

                            <th>Legalitas</th>

                            <th>Alamat</th>

                            <th>Kontak</th>

                            <th width="120">Aksi</th>

                        </tr>

                    </thead>

                    <tbody id="supplierTable">

                    <?php if(empty($supplier)): ?>

                        <tr>

                            <td colspan="6" class="text-center py-5">

                                <div class="text-muted">

                                    <i class="fas fa-inbox fa-3x mb-3"></i>

                                    <p class="mb-0">
                                        Data supplier tidak ditemukan
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php $no = 1; ?>

                    <?php foreach($supplier as $s): ?>

                    <tr>

                        <td>

                            <?= $no++ ?>

                        </td>

                        <td>

                            <strong>

                                <?= $s->nama_supplier ?>

                            </strong>

                        </td>

                        <td>

                            <?=
                                !empty($s->legalitas)
                                ? $s->legalitas
                                : '<span class="text-muted">-</span>'
                            ?>

                        </td>

                        <td>

                            <?=
                                !empty($s->alamat)
                                ? $s->alamat
                                : '<span class="text-muted">-</span>'
                            ?>

                        </td>

                        <td>

                            <?=
                                !empty($s->kontak)
                                ? $s->kontak
                                : '<span class="text-muted">-</span>'
                            ?>

                        </td>

                        <td>

                            <!-- DETAIL -->

                            <a href="<?= base_url('supplier/detail_manajer/'.$s->id_supplier) ?>"
                               class="btn btn-info btn-sm text-white"
                               title="Detail Supplier">

                                <i class="fas fa-eye"></i>

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- PAGINATION -->

            <?php if(!empty($pagination)): ?>

            <div class="mt-4 d-flex justify-content-center">

                <?= $pagination ?>

            </div>

            <?php endif; ?>

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
    font-weight:600;
}

.table td{
    vertical-align:middle;
}

.form-control{
    border-radius:10px;
}

.input-group-text{
    border-radius:10px 0 0 10px;
}

.btn{
    border-radius:10px;
}

.badge{
    font-size:.8rem;
}

.pagination{
    justify-content:center;
}

.pagination .page-link{
    border-radius:8px;
    margin:0 2px;
}

@media(max-width:768px){

    .page-container{
        padding:15px;
    }

    .table{
        min-width:700px;
    }

}

</style>