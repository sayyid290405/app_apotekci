<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-3">🛒 Order Obat ke Supplier</h4>

<!-- SUPPLIER -->
<div class="mb-3">
    <label>Supplier</label>
    <select id="supplier" class="form-control">
        <option value="">-- Pilih Supplier --</option>
        <?php foreach($supplier as $s): ?>
            <option value="<?= $s->id_supplier ?>">
                <?= $s->nama_supplier ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- SEARCH -->
<div class="mb-3">
    <input type="text" id="searchProduk" class="form-control" placeholder="🔍 Cari produk...">
</div>

<div class="row">

<!-- ================= LEFT: PRODUK ================= -->
<div class="col-md-8">
    <div class="row" id="produkList"></div>
</div>

<!-- ================= RIGHT: CART ================= -->
<div class="col-md-4">

    <div class="card shadow-sm">
    <div class="card-body">

        <h5>🧾 Keranjang</h5>

        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Sub</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="cart"></tbody>
        </table>

        <h5 class="mt-3">Total: Rp <span id="total">0</span></h5>

        <button id="btnSimpan" class="btn btn-success w-100 mt-2">
            💾 Simpan Order
        </button>

    </div>
    </div>

</div>

</div>

</div>
</div>

</div>