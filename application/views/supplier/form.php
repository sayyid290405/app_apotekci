<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">
<?= isset($supplier) ? '✏️ Edit Supplier' : '➕ Tambah Supplier' ?>
</h4>

<form method="post" action="<?= $action ?>">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>" />

<div class="row">

<div class="col-md-6 mb-3">
    <label>Nama Supplier</label>
    <input type="text" name="nama_supplier" class="form-control"
           value="<?= $supplier->nama_supplier ?? '' ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label>Legalitas</label>
    <input type="text" name="legalitas" class="form-control"
           value="<?= $supplier->legalitas ?? '' ?>">
</div>

<div class="col-md-12 mb-3">
    <label>Alamat</label>
    <textarea name="alamat" class="form-control"><?= $supplier->alamat ?? '' ?></textarea>
</div>

<div class="col-md-6 mb-3">
    <label>Kontak</label>
    <input type="text" name="kontak" class="form-control"
           value="<?= $supplier->kontak ?? '' ?>">
</div>

</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">
        💾 Simpan
    </button>

    <a href="<?= base_url('supplier') ?>" class="btn btn-secondary">
        Batal
    </a>
</div>

</form>

</div>
</div>

</div>