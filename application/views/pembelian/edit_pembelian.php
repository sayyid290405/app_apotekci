
<div class="page-container">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                <i class="fas fa-edit text-success me-2"></i>
                Edit Pembelian
            </h3>

            <small class="text-muted">
                Ubah data permohonan pengadaan barang
            </small>

        </div>

        <div>

            <span class="badge bg-success px-3 py-2">

                <?= $pembelian->kode_pembelian ?>

            </span>

        </div>

    </div>

    <!-- CARD -->
    <div class="card modern-card">

        <div class="card-header bg-success text-white">

            <i class="fas fa-file-invoice me-2"></i>

            Form Edit Pembelian

        </div>

        <div class="card-body">

            <form action="<?= base_url('Pembelian/edit_pembelian_data') ?>"
                  method="post">

                <input type="hidden"
                       name="<?= $this->security->get_csrf_token_name(); ?>"
                       value="<?= $this->security->get_csrf_hash(); ?>">

                <input type="hidden"
                       name="id_pembelian"
                       value="<?= $pembelian->id_pembelian ?>">

                <!-- KODE -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Kode Pembelian

                    </label>

                    <input type="text"
                           class="form-control"
                           value="<?= $pembelian->kode_pembelian ?>"
                           readonly>

                </div>

                <!-- SUPPLIER -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Supplier

                    </label>

                    <select name="supplier_id"
                            class="form-select">

                        <?php foreach($supplier as $s): ?>

                            <option
                                value="<?= $s->id_supplier ?>"
                                <?= $s->id_supplier == $pembelian->supplier_id ? 'selected' : '' ?>>

                                <?= $s->nama_supplier ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- TANGGAL -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Tanggal Pembelian

                    </label>

                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="<?= date('Y-m-d', strtotime($pembelian->tanggal)) ?>">

                </div>

                <!-- TOTAL -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Total Harga

                    </label>

                    <input type="text"
                           class="form-control bg-light"
                           value="Rp <?= number_format($pembelian->total,0,',','.') ?>"
                           readonly>

                </div>

                <!-- STATUS -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Status

                    </label>

                    <select name="status"
                            class="form-select">

                        <option value="menunggu"
                            <?= $pembelian->status == 'menunggu' ? 'selected' : '' ?>>
                            Menunggu
                        </option>

                        <option value="disetujui"
                            <?= $pembelian->status == 'disetujui' ? 'selected' : '' ?>>
                            Disetujui
                        </option>

                        <option value="ditolak"
                            <?= $pembelian->status == 'ditolak' ? 'selected' : '' ?>>
                            Ditolak
                        </option>

                    </select>

                </div>

                <!-- BUTTON -->
                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-success">

                        <i class="fas fa-save me-1"></i>

                        Simpan Perubahan

                    </button>

                    <a href="<?= base_url('pembelian/approval_pembelian') ?>"
                       class="btn btn-secondary">

                        <i class="fas fa-arrow-left me-1"></i>

                        Kembali

                    </a>

                </div>

            </form>

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

.form-label{
    margin-bottom:.4rem;
}

.form-control,
.form-select{
    border-radius:10px;
}

.btn{
    border-radius:10px;
}

</style>

