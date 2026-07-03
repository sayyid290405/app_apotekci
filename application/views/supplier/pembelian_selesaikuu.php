<div class="container mt-4">

    <h3>📦 Riwayat Pembelian & Pembayaran</h3>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('supplier/pembelian_selesai') ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter Status Pembelian</label>
                    <select name="status_pembelian" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="diproses" <?= ($this->input->get('status_pembelian') == 'diproses') ? 'selected' : '' ?>>Diproses</option>
                        <option value="diterima" <?= ($this->input->get('status_pembelian') == 'diterima') ? 'selected' : '' ?>>Diterima</option>
                        <option value="selesai" <?= ($this->input->get('status_pembelian') == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Filter Status Pembayaran</label>
                    <select name="status_bayar" class="form-select">
                        <option value="">Semua</option>
                        <option value="lunas" <?= ($this->input->get('status_bayar') == 'lunas') ? 'selected' : '' ?>>Lunas</option>
                        <option value="belum lunas" <?= ($this->input->get('status_bayar') == 'belum lunas') ? 'selected' : '' ?>>Belum Lunas</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="<?= base_url('supplier/pembelian_selesai') ?>" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('pembelian') ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Buat Permintaan Barang
        </a>
    </div> -->

    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kode Pembelian</th>
                    <th>Nama Supplier</th>
                    <th>Item Produk</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Status Pembelian</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Bayar</th>
                    <th>Bukti Bayar</th>
                    <th>Catatan</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            // Filter data berdasarkan GET parameter
            $status_pembelian_filter = $this->input->get('status_pembelian');
            $status_bayar_filter = $this->input->get('status_bayar');
            
            $filtered_data = [];
            foreach($pembayaran as $item):
                // Filter status pembelian
                if(!empty($status_pembelian_filter) && $item->status != $status_pembelian_filter) {
                    continue;
                }
                
                // Filter status pembayaran
                $status_bayar = isset($item->status_bayar) ? strtolower($item->status_bayar) : 'belum lunas';
                if(!empty($status_bayar_filter) && $status_bayar != $status_bayar_filter) {
                    continue;
                }
                
                $filtered_data[] = $item;
            endforeach;
            
            if(empty($filtered_data)): 
            ?>
                <tr>
                    <td colspan="11" class="text-center">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Belum ada data pembelian
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach($filtered_data as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <strong><?= $p->kode_pembelian ?? '-' ?></strong>
                    </td>
                    <td><?= $p->nama_supplier ?? '-' ?></td>
                    <td><?= $p->nama_produk ?? '-' ?></td>
                    <td><?= date('d M Y', strtotime($p->tanggal ?? date('Y-m-d'))) ?></td>
                    <td class="text-end">Rp <?= number_format($p->total ?? 0,0,',','.') ?></td>

                    <!-- Status Pembelian -->
                    <td class="text-center">
                        <?php 
                        $status_class = '';
                        $status_text = '';
                        switch($p->status) {
                            case 'menunggu':
                                $status_class = 'bg-warning text-dark';
                                $status_text = 'Menunggu';
                                break;
                            case 'disetujui':
                                $status_class = 'bg-primary';
                                $status_text = 'Disetujui';
                                break;
                            case 'diproses':
                                $status_class = 'bg-info text-dark';
                                $status_text = 'Diproses';
                                break;
                            case 'selesai':
                                $status_class = 'bg-success';
                                $status_text = 'Selesai';
                                break;
                            case 'diterima':
                                $status_class = 'bg-success';
                                $status_text = 'Diterima';
                                break;
                            case 'ditolak':
                                $status_class = 'bg-danger';
                                $status_text = 'Ditolak';
                                break;
                            case 'dibatalkan':
                                $status_class = 'bg-secondary';
                                $status_text = 'Dibatalkan';
                                break;
                            default:
                                $status_class = 'bg-dark';
                                $status_text = $p->status ?? 'Unknown';
                        }
                        ?>
                        <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                        <br>
                        <!-- <a href="<?= base_url('pembelian/detail/'.$p->id_pembelian) ?>" 
                           class="btn btn-sm btn-info mt-1">
                            <i class="fas fa-eye"></i> Detail
                        </a> -->
                    </td>

                    <!-- Status Pembayaran -->
                    <td class="text-center">
                        <?php 
                        $status_bayar = isset($p->status_bayar) ? strtolower($p->status_bayar) : 'belum lunas';
                        if($status_bayar == 'lunas'): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Lunas
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-clock"></i> Belum Lunas
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Tanggal Bayar -->
                    <td class="text-center">
                        <?php if(isset($p->tanggal_bayar) && !empty($p->tanggal_bayar)): ?>
                            <?= date('d M Y H:i', strtotime($p->tanggal_bayar)) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- Bukti Bayar -->
                    <td class="text-center">
                        <?php if(isset($p->bukti_pembayaran) && !empty($p->bukti_pembayaran)): ?>
                            <a href="#" 
                               data-bs-toggle="modal" 
                               data-bs-target="#modalBukti<?= $p->id_pembelian ?>">
                                <img src="<?= base_url('uploads/bukti/'.$p->bukti_pembayaran) ?>"
                                     width="70"
                                     class="img-thumbnail"
                                     style="cursor:pointer; border-radius:6px;">
                            </a>

                            <!-- MODAL Bukti Pembayaran -->
                            <div class="modal fade" id="modalBukti<?= $p->id_pembelian ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-receipt"></i> Bukti Pembayaran - <?= $p->kode_pembelian ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="<?= base_url('uploads/bukti/'.$p->bukti_pembayaran) ?>" 
                                                 class="img-fluid rounded">
                                        </div>
                                        <div class="modal-footer">
                                            <a href="<?= base_url('uploads/bukti/'.$p->bukti_pembayaran) ?>" 
                                               class="btn btn-primary" 
                                               download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">
                                <i class="fas fa-image"></i> Belum upload
                            </span>
                        <?php endif; ?>
                    </td>

                    <td><?= $p->catatan ?? 'tidak ada catatan' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
// Auto submit form when select changes
document.querySelectorAll('.form-select').forEach(function(select) {
    select.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>