<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
  <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger">
          <?= $this->session->flashdata('error'); ?>
      </div>
  <?php endif; ?>

  <div class="card shadow mb-4">
    <div class="card-body">
      <form method="POST" action="<?= base_url('orders/create'); ?>">
        
        <?php if ($this->security->get_csrf_token_name()) : ?>
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                 value="<?= $this->security->get_csrf_hash(); ?>">
        <?php endif; ?>

        <div class="form-group">
          <label>Kode Order</label>
          <input type="text" name="order_code" class="form-control" value="<?= $order_code; ?>" readonly>
        </div>

        <div class="form-group">
          <label>Pelanggan</label>
          <select name="customer_id" class="form-control" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= $c->id; ?>"><?= $c->name; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php if ($this->session->userdata('role') == 'admin'): ?>
        <div class="form-group">
          <label>Sales</label>
          <select name="sales_id" class="form-control" required>
            <option value="">-- Pilih Sales --</option>
            <?php foreach ($sales as $s): ?>
              <option value="<?= $s->id; ?>"><?= $s->name; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php else: ?>
          <input type="hidden" name="sales_id" value="<?= $this->session->userdata('sales_id'); ?>">
        <?php endif; ?>

        <div class="form-group">
          <label>Catatan</label>
          <textarea name="note" class="form-control" rows="3"></textarea>
        </div>

        <hr>
        <h5><strong>Detail Produk</strong></h5>

        <div id="product-section">
          <div class="row mb-2 product-row">
            <div class="col-md-5">
              <select name="product_id[]" class="form-control product-select" required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                  <option value="<?= $p->id; ?>" data-price="<?= $p->price; ?>">
                    <?= $p->name; ?> (Rp <?= number_format($p->price,0,',','.'); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" name="quantity[]" class="form-control qty" min="1" value="1" required>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
            </div>
            <div class="col-md-1 d-flex align-items-center">
              <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        </div>

        <button type="button" id="addProductRow" class="btn btn-secondary btn-sm mb-3">
          <i class="fas fa-plus"></i> Tambah Produk
        </button>

        <div class="form-group">
          <label><strong>Total Harga</strong></label>
          <input type="text" id="total_price" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Order</button>
        <a href="<?= base_url('orders'); ?>" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

<!-- Script dinamis untuk tambah produk -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const productSection = document.getElementById('product-section');
  const addBtn = document.getElementById('addProductRow');

  function updateSubtotal(row) {
    const select = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.qty');
    const subtotalField = row.querySelector('.subtotal');

    const price = parseFloat(select.selectedOptions[0].getAttribute('data-price') || 0);
    const qty = parseInt(qtyInput.value) || 0;
    subtotalField.value = (price * qty).toLocaleString('id-ID');
    updateTotal();
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(el => {
      total += parseInt(el.value.replace(/\D/g,'')) || 0;
    });
    document.getElementById('total_price').value = 'Rp ' + total.toLocaleString('id-ID');
  }

  productSection.addEventListener('change', e => {
    if (e.target.classList.contains('product-select') || e.target.classList.contains('qty')) {
      updateSubtotal(e.target.closest('.product-row'));
    }
  });

  addBtn.addEventListener('click', () => {
    const newRow = productSection.querySelector('.product-row').cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(input => {
      input.value = '';
    });
    productSection.appendChild(newRow);
  });

  productSection.addEventListener('click', e => {
    if (e.target.closest('.remove-row')) {
      if (document.querySelectorAll('.product-row').length > 1) {
        e.target.closest('.product-row').remove();
        updateTotal();
      }
    }
  });
});
</script>
