<div class="page-container">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                <i class="fas fa-clipboard-check text-success me-2"></i>
                Manajemen Pembelian Obat
            </h3>

            <small class="text-muted">
                Kelola dan verifikasi permohonan Pembelian obat ke supplier
            </small>

        </div>

        <div>

            <span class="badge bg-success px-3 py-2">

                Total Data :
                <?= count($pembelian) ?>

            </span>

        </div>

    </div>

    <!-- CARD -->
    <div class="card modern-card">

        <div class="card-header bg-success text-white">

            <div class="d-flex justify-content-between align-items-center">

                <span>

                    <i class="fas fa-boxes me-2"></i>

                    List Permohonan Pembelian Obat

                </span>

                <span>

                    <?= date('d M Y') ?>

                </span>

            </div>

        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="get"
                  class="row g-3 align-items-end mb-4">

                <div class="col-md-4">

                    <label class="form-label">
                        Filter Status
                    </label>

                    <select
                        name="filter"
                        class="form-select">

                        <option value="">
                            -- Semua Status --
                        </option>

                        <option value="menunggu"
                            <?= $this->input->get('filter') == 'menunggu' ? 'selected' : '' ?>>
                            Menunggu
                        </option>

                        <option value="disetujui"
                            <?= $this->input->get('filter') == 'disetujui' ? 'selected' : '' ?>>
                            Disetujui
                        </option>

                        <option value="diproses"
                            <?= $this->input->get('filter') == 'diproses' ? 'selected' : '' ?>>
                            Diproses
                        </option>

                        <option value="selesai"
                            <?= $this->input->get('filter') == 'selesai' ? 'selected' : '' ?>>
                            Selesai
                        </option>

                        <option value="ditolak"
                            <?= $this->input->get('filter') == 'ditolak' ? 'selected' : '' ?>>
                            Ditolak
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <button
                        class="btn btn-primary w-100">

                        <i class="fas fa-search"></i>
                        Filter

                    </button>

                </div>

                <div class="col-md-2">

                    <a href="<?= base_url('Pembelian/approval_pembelian') ?>"
                       class="btn btn-secondary w-100">

                        Reset

                    </a>

                </div>

            </form>

            <!-- TABLE -->
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-success">

                    <tr>

                        <th width="60">No</th>

                        <th>Kode</th>

                        <th>Supplier</th>

                        <th>Tanggal</th>

                        <th width="280">Produk</th>

                        <th>Total</th>

                        <th>Status</th>

                        <th width="220">Aksi</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($pembelian)): ?>

                        <tr>

                            <td colspan="8"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i class="fas fa-inbox fa-3x mb-3"></i>

                                    <h6>
                                        Data tidak ditemukan
                                    </h6>

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

                            <strong class="text-success">

                                <?= $p->kode_pembelian ?>

                            </strong>

                        </td>

                        <td>

                            <?= $p->nama_supplier ?>

                        </td>

                        <td>

                            <?= date(
                                'd M Y',
                                strtotime($p->tanggal)
                            ) ?>

                        </td>

                        <td class="produk-cell">

                            <?= $p->nama_produk ?>

                        </td>

                        <td>

                            <strong>

                                Rp <?= number_format(
                                    $p->total,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>

                        <!-- STATUS -->
                        <td>

                            <form method="post"
                                  action="<?= base_url('Pembelian/update_status') ?>">

                                <input type="hidden"
                                    name="<?= $this->security->get_csrf_token_name(); ?>"
                                    value="<?= $this->security->get_csrf_hash(); ?>">

                                <input type="hidden"
                                    name="id_pembelian"
                                    value="<?= $p->id_pembelian ?>">

                                <?php

                                $isLocked = in_array(
                                    $p->status,
                                    ['diproses','selesai','diterima']
                                );

                                $class = 'bg-warning text-dark';

                                if($p->status == 'disetujui'){
                                    $class = 'bg-success text-white';
                                }
                                elseif($p->status == 'ditolak'){
                                    $class = 'bg-danger text-white';
                                }
                                elseif($p->status == 'diproses'){
                                    $class = 'bg-info text-white';
                                }
                                elseif($p->status == 'selesai'){
                                    $class = 'bg-primary text-white';
                                }

                                ?>

                                <?php if($isLocked): ?>

                                    <span class="badge <?= $class ?>">

                                        <?= ucfirst($p->status) ?>

                                    </span>

                                <?php else: ?>

                                    <select
                                        name="status"
                                        class="form-select form-select-sm status-select <?= $class ?>"
                                        onchange="this.form.submit()">

                                        <option value="menunggu"
                                            <?= $p->status == 'menunggu' ? 'selected' : '' ?>>
                                            Menunggu
                                        </option>

                                        <option value="disetujui"
                                            <?= $p->status == 'disetujui' ? 'selected' : '' ?>>
                                            Disetujui
                                        </option>

                                        <option value="ditolak"
                                            <?= $p->status == 'ditolak' ? 'selected' : '' ?>>
                                            Ditolak
                                        </option>

                                    </select>

                                <?php endif; ?>

                            </form>

                        </td>

                        <!-- AKSI -->
                        <td>

                            <a href="<?= base_url('pembelian/detail_manajer/'.$p->id_pembelian) ?>"
                               class="btn btn-info btn-sm">

                                <i class="fas fa-eye"></i>

                            </a>

                            <?php if(!in_array($p->status,['diproses','selesai'])): ?>

                                <!-- <a href="<?= base_url('pembelian/edit_pembelian/'.$p->id_pembelian) ?>"
                                   class="btn btn-warning btn-sm">

                                    Edit

                                </a> -->

                                <a href="<?= base_url('pembelian/delete/'.$p->id_pembelian) ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus data ini?')">

                                    Hapus

                                </a>

                            <?php else: ?>

                                <button
                                    class="btn btn-secondary btn-sm"
                                    disabled>

                                    Edit

                                </button>

                                <button
                                    class="btn btn-secondary btn-sm"
                                    disabled>

                                    Hapus

                                </button>

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
    padding:14px 20px;
}

.table th{
    white-space:nowrap;
    vertical-align:middle;
    font-weight:600;
}

.table td{
    vertical-align:middle;
}

.status-select{
    cursor:pointer;
    border-radius:20px !important;
    font-weight:600;
    text-align:center;
}

.badge{
    font-size:.75rem;
}

.produk-cell{
    max-width:300px;
    white-space:normal;
    word-break:break-word;
}

@media(max-width:768px){

    .page-container{
        padding:15px;
    }

}

</style>