
<div class="page-container">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">

                <i class="fas fa-truck text-success me-2"></i>

                <?= isset($supplier)
                    ? 'Edit Supplier'
                    : 'Tambah Supplier' ?>

            </h3>

            <small class="text-muted">

                <?= isset($supplier)
                    ? 'Perbarui data supplier'
                    : 'Tambahkan supplier baru ke sistem' ?>

            </small>

        </div>

        <div>

            <a href="<?= base_url('supplier') ?>"
               class="btn btn-outline-secondary">

                <i class="fas fa-arrow-left me-1"></i>

                Kembali

            </a>

        </div>

    </div>

    <!-- CARD -->
    <div class="card modern-card">

        <div class="card-header bg-success text-white">

            <i class="fas fa-building me-2"></i>

            Form Data Supplier

        </div>

        <div class="card-body">

            <form method="post"
                  action="<?= $action ?>">

                <input
                    type="hidden"
                    name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="row">

                    <!-- NAMA SUPPLIER -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Nama Supplier

                        </label>

                        <input
                            type="text"
                            name="nama_supplier"
                            class="form-control"
                            value="<?= $supplier->nama_supplier ?? '' ?>"
                            required>

                    </div>

                    <!-- LEGALITAS -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Legalitas

                        </label>

                        <input
                            type="text"
                            name="legalitas"
                            class="form-control"
                            value="<?= $supplier->legalitas ?? '' ?>">

                    </div>

                    <!-- ALAMAT -->
                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-semibold">

                            Alamat

                        </label>

                        <textarea
                            name="alamat"
                            rows="4"
                            class="form-control"><?= $supplier->alamat ?? '' ?></textarea>

                    </div>

                    <!-- KONTAK -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">

                            Kontak

                        </label>

                        <input
                            type="text"
                            name="kontak"
                            class="form-control"
                            value="<?= $supplier->kontak ?? '' ?>">

                    </div>

                </div>

                <!-- BUTTON -->
                <div class="d-flex gap-2 mt-3">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="fas fa-save me-1"></i>

                        Simpan

                    </button>

                    <a
                        href="<?= base_url('supplier') ?>"
                        class="btn btn-secondary">

                        <i class="fas fa-times me-1"></i>

                        Batal

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

.form-control,
.form-select,
textarea{
    border-radius:10px;
}

.form-label{
    margin-bottom:.4rem;
}

.btn{
    border-radius:10px;
}

</style>
