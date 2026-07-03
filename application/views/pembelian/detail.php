<div class="container">
    <h4>📄 Detail Pembelian</h4>
    
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Kode:</strong> <?= $pembelian->kode_pembelian ?>
                </div>
                <div class="col-md-4">
                    <strong>Supplier:</strong> <?= $pembelian->nama_supplier ?>
                </div>
                <div class="col-md-4">
                    <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($pembelian->tanggal)) ?>
                </div>
            </div>
        </div>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($detail as $d): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $d->nama_produk ?></td>
                <td>
                    <?php 
                    // 🔥 TAMPILKAN NAMA SATUAN
                    if(isset($d->nama_satuan) && $d->nama_satuan) {
                        echo $d->nama_satuan;
                    } else {
                        // Fallback: cari dari database
                        $satuan = $this->db->get_where('satuan_produk', ['id' => $d->satuan_id])->row();
                        echo $satuan ? $satuan->nama_satuan : 'Unit';
                    }
                    ?>
                </td>
                <td>Rp <?= number_format($d->harga, 0, ',', '.') ?></td>
                <td><?= $d->jumlah ?></td>
                <td>Rp <?= number_format($d->subtotal, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">TOTAL:</th>
                <th>Rp <?= number_format($pembelian->total, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>
    
    <a href="<?= base_url('pembelian/pembelian_supplier') ?>" class="btn btn-secondary">Kembali</a>
    <a href="<?= base_url('pembelian/cetak/'.$pembelian->id_pembelian) ?>" class="btn btn-primary">Cetak PDF</a>
</div>