<div class="container mt-4">
    <div class="card">
        <div class="card-header">Edit Pembelian</div>
        <div class="card-body">

            <form action="<?= base_url('Pembelian/edit_pembelian_data') ?>" method="post">

            <input type="hidden" 
                name="<?= $this->security->get_csrf_token_name(); ?>" 
                value="<?= $this->security->get_csrf_hash(); ?>">

            <input type="hidden" name="id_pembelian" value="<?= $pembelian->id_pembelian ?>">

                <!-- KODE -->
                <div class="mb-3">
                    <label>Kode Pembelian</label>
                    <input type="text" class="form-control"
                        value="<?= $pembelian->kode_pembelian ?>" readonly>
                </div>

                <!-- SUPPLIER -->
                <div class="mb-3">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <?php foreach($supplier as $s): ?>
                            <option value="<?= $s->id_supplier ?>"
                                <?= $s->id_supplier == $pembelian->supplier_id ? 'selected' : '' ?>>
                                <?= $s->nama_supplier ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- TANGGAL -->
                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                        value="<?= date('Y-m-d', strtotime($pembelian->tanggal)) ?>">
                </div>

                <!-- TOTAL (READONLY) -->
                <div class="mb-3">
                    <label>Total Harga</label>
                    <input type="text" class="form-control"
                        value="Rp <?= number_format($pembelian->total,0,',','.') ?>" readonly>
                </div>

                <!-- STATUS -->
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="menunggu" <?= $pembelian->status == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="disetujui" <?= $pembelian->status == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= $pembelian->status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?= base_url('pembelian/approval_pembelian') ?>" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>