<div class="container py-4">
    
    <!-- Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">
                <i class="fas fa-prescription-bottle-alt text-primary me-2"></i>
                Detail Resep Dokter
            </h4>
        </div>
        <div class="card-body">
            
            <!-- Informasi Resep -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted">Kode Resep</small>
                        <h6 class="mb-0"><?= $resep->kode_resep ?? '-' ?></h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted">Tanggal</small>
                        <h6 class="mb-0"><?= date('d/m/Y', strtotime($resep->tanggal)) ?></h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted">Status</small>
                        <div>
                            <?php if($resep->status == 'draft'): ?>
                                <span class="badge bg-warning">📝 Draft</span>
                            <?php elseif($resep->status == 'verified'): ?>
                                <span class="badge bg-success">✅ Terverifikasi</span>
                            <?php elseif($resep->status == 'used'): ?>
                                <span class="badge bg-secondary">🔄 Sudah Diproses</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $resep->status ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted">Total Obat</small>
                        <h6 class="mb-0"><?= count($detail) ?> item</h6>
                    </div>
                </div>
            </div>
            
            <!-- Data Pasien & Dokter -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <i class="fas fa-user me-2"></i> Data Pasien
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Nama Pasien</strong></td>
                                    <td>: <?= htmlspecialchars($resep->nama_pasien ?? $resep->pasien_nama ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white py-2">
                            <i class="fas fa-user-md me-2"></i> Data Dokter
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%"><strong>Nama Dokter</strong></td>
                                    <td>: <?= htmlspecialchars($resep->nama_dokter ?? $resep->dokter ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gambar Resep -->
            <?php if(!empty($resep->gambar_resep) && file_exists('./' . $resep->gambar_resep)): ?>
            <div class="mb-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white py-2">
                        <i class="fas fa-camera me-2"></i> Foto Resep
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= base_url($resep->gambar_resep) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; cursor: pointer;"
                             onclick="window.open(this.src)" 
                             alt="Foto Resep">
                        <br>
                        <small class="text-muted">Klik gambar untuk memperbesar</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Tabel Daftar Obat -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white py-2">
                    <i class="fas fa-table-list me-2"></i> Daftar Obat Resep
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Aturan Pakai / Dosis</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $total_keseluruhan = 0;
                                foreach($detail as $d): 
                                    $subtotal = ($d->harga ?? 0) * ($d->jumlah ?? 1);
                                    $total_keseluruhan += $subtotal;
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($d->nama_produk) ?></strong>
                                        <?php if(!empty($d->catatan)): ?>
                                            <br><small class="text-muted">Catatan: <?= $d->catatan ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $d->satuan ?? '-' ?></td>
                                    <td class="text-end">Rp <?= number_format($d->harga ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= $d->jumlah ?? 0 ?></span>
                                    </td>
                                    <td>
                                        <i class="fas fa-clock text-info me-1"></i>
                                        <?= !empty($d->dosis) ? htmlspecialchars($d->dosis) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-end fw-bold">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Total Keseluruhan</td>
                                    <td class="text-end fw-bold text-primary">
                                        Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan & Catatan -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark py-2">
                            <i class="fas fa-note-sticky me-2"></i> Catatan Khusus
                        </div>
                        <div class="card-body">
                            <p class="mb-0">
                                <?= !empty($resep->catatan) ? nl2br(htmlspecialchars($resep->catatan)) : 'Tidak ada catatan khusus untuk resep ini.' ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white py-2">
                            <i class="fas fa-calculator me-2"></i> Informasi Tambahan
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Biaya Resep/Jasa</td>
                                    <td class="text-end">Rp <?= number_format($resep->biaya_resep ?? 20000, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>PPN (11%)</td>
                                    <td class="text-end">Rp <?= number_format(($total_keseluruhan + ($resep->biaya_resep ?? 20000)) * 0.11, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Grand Total</strong></td>
                                    <td class="text-end"><strong class="text-primary">Rp <?= number_format(($total_keseluruhan + ($resep->biaya_resep ?? 20000)) * 1.11, 0, ',', '.') ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Aksi -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('resep') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                
                <?php if($resep->status == 'draft' || $resep->status != 'verified'): ?>
                <a href="<?= base_url('resep/verifikasi/'.$resep->id_resep) ?>" 
                   class="btn btn-success" 
                   onclick="return confirm('Verifikasi resep ini?')">
                    <i class="fas fa-check-circle me-1"></i> Verifikasi Resep
                </a>
                <?php endif; ?>
                
                <?php if($resep->status == 'verified'): ?>
                <a href="<?= base_url('kasir?resep='.$resep->id_resep) ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i> Proses ke Kasir
                </a>
                <?php endif; ?>
                
                <a href="<?= base_url('kasir/struk/'.$resep->id_resep) ?>" 
                   class="btn btn-info" 
                   target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak Resep
                </a>
                
                <?php if($resep->status == 'draft'): ?>
                <a href="<?= base_url('resep/edit/'.$resep->id_resep) ?>" 
                   class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Resep
                </a>
                
                <a href="<?= base_url('resep/delete/'.$resep->id_resep) ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Hapus resep ini?')">
                    <i class="fas fa-trash me-1"></i> Hapus
                </a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    
</div>

<!-- Tambahan CSS untuk print -->
<style media="print">
    .btn, .bg-primary, .bg-success, .bg-info, .bg-warning {
        display: none;
    }
    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    body {
        padding: 0;
        margin: 0;
    }
    .container {
        width: 100%;
        max-width: 100%;
    }
</style>