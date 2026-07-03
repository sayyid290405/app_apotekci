
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart"></i> 
                                Order: <?= htmlspecialchars($pembelian->kode_pembelian) ?>
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('Laporan_supplier/print_pembelian2/' . $pembelian->id_pembelian) ?>" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                <a href="<?= base_url('laporan_supplier') ?>" class="btn btn-sm btn-default">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Utama -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="35%"><strong>Kode Pembelian</strong></td>
                                            <td>: <?= htmlspecialchars($pembelian->kode_pembelian) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Order</strong></td>
                                            <td>: <?= date('d/m/Y H:i:s', strtotime($pembelian->tanggal)) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>: 
                                                <?php 
                                                $badge = 'secondary';
                                                if ($pembelian->status == 'selesai') $badge = 'success';
                                                elseif ($pembelian->status == 'disetujui') $badge = 'primary';
                                                elseif ($pembelian->status == 'diproses') $badge = 'warning';
                                                elseif ($pembelian->status == 'diterima') $badge = 'info';
                                                elseif ($pembelian->status == 'ditolak' || $pembelian->status == 'dibatalkan') $badge = 'danger';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($pembelian->status) ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier</strong></td>
                                            <td>: <?= htmlspecialchars($pembelian->nama_supplier) ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="35%"><strong>User Pembuat</strong></td>
                                            <td>: <?= htmlspecialchars($pembelian->user_name ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Approved By</strong></td>
                                            <td>: <?= htmlspecialchars($pembelian->approved_by_name ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Approve</strong></td>
                                            <td>: <?= $pembelian->tanggal_approve ? date('d/m/Y H:i:s', strtotime($pembelian->tanggal_approve)) : '-' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Pembelian</strong></td>
                                            <td>: <strong class="text-success">Rp <?= number_format($pembelian->total, 0, ',', '.') ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Catatan jika ada -->
                            <?php if (!empty($pembelian->catatan)): ?>
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle"></i> <strong>Catatan:</strong> <?= nl2br(htmlspecialchars($pembelian->catatan)) ?>
                            </div>
                            <?php endif; ?>

                            <!-- Tabel Detail Produk -->
                            <div class="mt-4">
                                <h5><i class="fas fa-boxes"></i> Daftar Produk</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="40%">Nama Produk</th>
                                                <th width="15%">Jumlah</th>
                                                <th width="20%">Harga Satuan (Rp)</th>
                                                <th width="20%">Subtotal (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($detail)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Tidak ada detail produk</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php $no = 1; foreach ($detail as $item): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($item->nama_produk) ?>
                                                            <?php if ($item->nama_satuan): ?>
                                                                <small class="text-muted">(<?= $item->nama_satuan ?>)</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center"><?= number_format($item->jumlah) ?> pcs</td>
                                                        <td class="text-right"><?= number_format($item->harga, 0, ',', '.') ?></td>
                                                        <td class="text-right"><?= number_format($item->subtotal, 0, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="bg-light font-weight-bold">
                                            <tr>
                                                <td colspan="4" class="text-right">GRAND TOTAL</td>
                                                <td class="text-right">Rp <?= number_format($pembelian->total, 0, ',', '.') ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Informasi Tambahan Supplier -->
                            <div class="mt-4 p-3 bg-gray-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong><i class="fas fa-building"></i> Alamat Supplier</strong><br>
                                        <?= nl2br(htmlspecialchars($pembelian->alamat ?? '-')) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-phone"></i> Kontak</strong><br>
                                        <?= htmlspecialchars($pembelian->kontak ?? '-') ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-file-certificate"></i> Legalitas</strong><br>
                                        <?= htmlspecialchars($pembelian->legalitas ?? '-') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card-body -->
                        <div class="card-footer text-muted text-center">
                            <small>Dokumen ini merupakan bukti transaksi yang sah. Dicetak pada <?= date('d/m/Y H:i:s') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>