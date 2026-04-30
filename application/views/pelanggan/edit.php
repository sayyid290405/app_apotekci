<div class="container-fluid">

  <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

  <div class="card shadow mb-4">
    <div class="card-body">
      <form method="POST">

      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" 
         value="<?= $this->security->get_csrf_hash(); ?>" />

        <div class="form-group">
          <label>Kode Pelanggan</label>
          <input type="text" class="form-control" value="<?= $customer->customer_code; ?>" readonly>
        </div>
        <div class="form-group">
          <label>Nama</label>
          <input type="text" name="name" class="form-control" value="<?= $customer->name; ?>" required>
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea name="address" class="form-control" rows="3"><?= $customer->address; ?></textarea>
        </div>
        <div class="form-group">
          <label>Telepon</label>
          <input type="text" name="phone" class="form-control" value="<?= $customer->phone; ?>">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="<?= $customer->email; ?>">
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Perbarui</button>
        <a href="<?= base_url('customer'); ?>" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>

</div>
