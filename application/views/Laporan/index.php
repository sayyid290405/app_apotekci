<div class="container-fluid">

<style>
/* ===== DASHBOARD STYLE ===== */
.dashboard-title {
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* Card modern */
.card-modern {
    border: none;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
}

.card-modern:hover {
    transform: translateY(-2px);
}

/* Summary card */
.summary-card {
    padding: 20px;
    border-radius: 16px;
    color: white;
}

.summary-card h3 {
    font-size: 28px;
    font-weight: bold;
    margin: 0;
}

.summary-card small {
    opacity: 0.85;
}

/* Gradient warna */
.bg-gradient-green {
    background: linear-gradient(135deg, #16a34a, #22c55e);
}

.bg-gradient-blue {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
}

.bg-gradient-cyan {
    background: linear-gradient(135deg, #06b6d4, #22d3ee);
}

/* Table */
.table-modern thead {
    background: #f8fafc;
}

.section {
    margin-bottom: 30px;
}

.container-fluid {
    padding-bottom: 40px;
}

/* Style untuk card resep */
.badge-resep {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.resep-card {
    border-left: 4px solid #8b5cf6;
}

.btn-resep-detail {
    background: #8b5cf6;
    color: white;
    border: none;
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 6px;
}

.btn-resep-detail:hover {
    background: #6d28d9;
    color: white;
}
</style>
<style>
.scroll-table {
    max-height: 350px;
    overflow-y: auto;
    border-radius: 10px;
}

/* biar header tetap terlihat */
.scroll-table thead th {
    position: sticky;
    top: 0;
    background: #f8fafc;
    z-index: 2;
}
.scroll-table {
    scroll-behavior: smooth;
}
/* Style Tambahan Khusus Preview Gambar Modal Tanpa Merusak Style Utama */
.img-preview-bukti {
    max-width: 100%;
    max-height: 450px;
    display: block;
    margin: 0 auto;
    border-radius: 8px;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="dashboard-title">📊 Laporan Transaksi</h3>

    <div class="d-flex gap-2">
        <a href="<?= base_url('laporan/export_excel?'.$_SERVER['QUERY_STRING']) ?>" 
           class="btn btn-success btn-sm px-3">
           Export Excel
        </a>

    </div>
</div>

<div class="card card-modern section">
    <div class="card-body">

        <form method="get" class="row g-3">

            <div class="col-md-2">
                <label>Jenis Data</label>
                <select name="jenis" class="form-control">
                    <option value="semua" <?= ($jenis == 'semua') ? 'selected' : '' ?>>Semua</option>
                    <option value="penjualan" <?= ($jenis == 'penjualan') ? 'selected' : '' ?>>Penjualan</option>
                    <option value="pembelian" <?= ($jenis == 'pembelian') ? 'selected' : '' ?>>Pembelian</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>Jumlah</label>
                <select name="limit" class="form-control">
                    <option value="all" <?= ($limit == 'all') ? 'selected' : '' ?>>Semua</option>
                    <option value="10" <?= ($limit == '10') ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= ($limit == '25') ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= ($limit == '50') ? 'selected' : '' ?>>50</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>Tgl Mulai</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
            </div>

            <div class="col-md-2">
                <label>Tgl Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
            </div>

            <div class="col-md-3">
                <label>Search</label>
                <input type="text" name="search" class="form-control"
value="<?= $search ?? '' ?>"
placeholder="Cari TRX / INV / kasir..."
onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-success w-100">Filter</button>
            </div>

        </form>

    </div>
</div>
<div class="row g-3 section">

    <div class="col-md-4">
        <div class="summary-card bg-gradient-green">
            <small>Total Hari Ini</small>
            <h3>Rp <?= number_format($total_hari_ini) ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="summary-card bg-gradient-blue">
            <small>Total Penjualan</small>
            <h3><?= $total_penjualan ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="summary-card bg-gradient-cyan">
            <small>Total Pembelian</small>
            <h3><?= $total_pembelian ?></h3>
        </div>
    </div>

</div>

<div class="card card-modern section">
    <div class="card-body">

        <h6 class="fw-bold mb-3">🔥 Obat Terlaris</h6>

        <ul class="list-group list-group-flush">
            <?php foreach($obat_terlaris as $o): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= $o->nama_produk ?></span>
                    <span class="badge bg-success"><?= $o->total ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
</div>

<div class="card card-modern section">
    <div class="card-body">

        <h6 class="fw-bold mb-3">📊 Data Penjualan Obat</h6>

        <div class="table-responsive scroll-table">
            <table class="table table-hover align-middle table-modern">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Kasir</th>
                        <th>Bukti</th>
                        <th>Invoice</th>
                    </tr>
                </thead>

                <tbody>
                <?php if(empty($penjualan)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            Data tidak tersedia
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no=1; foreach($penjualan as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>TRX<?= $p->id_pesanan ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?></td>
                        <td>
                            <span class="badge <?= ($p->metode_bayar == 'cash') ? 'bg-success' : 'bg-info' ?>">
                                <?= strtoupper($p->metode_bayar) ?>
                            </span>
                        </td>
                        <td>Rp <?= number_format($p->total_harga) ?></td>
                        <td><?= $p->kasir ?? '-' ?></td>
                        <td>
                            <?php if(!empty($p->bukti_qris)): ?>
                                <button type="button" class="btn btn-sm btn-warning py-0 px-2" 
                                        onclick="lihatBukti('<?= base_url($p->bukti_qris) ?>', 'TRX<?= $p->id_pesanan ?>')">
                                    Lihat
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('Kasir/pdf/'.$p->id_pesanan) ?>"
                            target="_blank"  
                               class="btn btn-sm btn-primary">A4</a>

                            <a href="<?= base_url('kasir/struk_thermal/'.$p->id_pesanan) ?>" 
                            target="_blank" 
                            class="btn btn-sm btn-secondary">
                            Thermal
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<!-- ==================== CARD KHUSUS RESEP ==================== -->
<div class="card card-modern section">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-prescription-bottle-alt me-2" style="color: #8b5cf6;"></i>
                📋 Data Transaksi Resep
            </h6>
            <div>
                <span class="badge-resep me-2">
                    <i class="fas fa-chart-line me-1"></i> Total: Rp <?= number_format($total_pendapatan_resep ?? 0) ?>
                </span>
                <span class="badge bg-secondary">
                    <i class="fas fa-file-prescription me-1"></i> <?= $total_resep ?? 0 ?> Resep
                </span>
            </div>
        </div>

        <div class="table-responsive scroll-table">
            <table class="table table-hover align-middle table-modern">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Resep</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Kasir</th>
                        <th>Gambar Resep</th>
                        <th>Bukti Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($penjualan_resep)): ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="fas fa-prescription me-2"></i> Belum ada transaksi resep
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no=1; foreach($penjualan_resep as $r): ?>
                    <tr class="resep-card">
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="badge" style="background: #8b5cf6;">
                                <?= $r->kode_resep ?? '-' ?>
                            </span>
                        </td>
                        <td>TRX<?= $r->id_pesanan ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r->tanggal_pesan)) ?></td>
                        <td><strong><?= htmlspecialchars($r->nama_pasien ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($r->nama_dokter ?? '-') ?></td>
                        <td>
                            <span class="badge <?= ($r->metode_bayar == 'tunai') ? 'bg-success' : 'bg-info' ?>">
                                <?= strtoupper($r->metode_bayar) ?>
                            </span>
                        </td>
                        <td class="fw-bold text-primary">Rp <?= number_format($r->total_harga) ?></td>
                        <td><?= $r->kasir ?? '-' ?></td>
                        <td>
                            <?php if(!empty($r->gambar_resep)): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" 
                                        onclick="lihatGambarResep('<?= base_url($r->gambar_resep) ?>', 'Resep - <?= $r->kode_resep ?>')">
                                    <i class="fas fa-image me-1"></i> Lihat
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($r->bukti_qris)): ?>
                                <button type="button" class="btn btn-sm btn-warning py-0 px-2" 
                                        onclick="lihatBukti('<?= base_url($r->bukti_qris) ?>', 'TRX<?= $r->id_pesanan ?>')">
                                    <i class="fas fa-receipt me-1"></i> Lihat
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('kasir/struk/'.$r->id_pesanan) ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-print"></i>
                            </a>
                            <?php if($r->resep_id): ?>
                            <a href="<?= base_url('resep/detail/'.$r->resep_id) ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-prescription"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card card-modern section">
    <div class="card-body">

        <h6 class="fw-bold mb-3">📦 Data Pembelian Obat</h6>

        <div class="table-responsive scroll-table">
            <table class="table table-bordered align-middle">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if(empty($pembelian)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Data tidak tersedia
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no=1; foreach($pembelian as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $p->kode_pembelian ?></td>
                        <td><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                        <td>Rp <?= number_format($p->total) ?></td>
                        <td>
                            <span class="badge bg-success"><?= $p->status ?></span>
                        </td>
                        <td>
                            <a href="<?= base_url('pembelian/detail/'.$p->id_pembelian) ?>" 
                               class="btn btn-sm btn-info">👁</a>

                            <a href="<?= base_url('pembelian/cetak/'.$p->id_pembelian) ?>" 
                               class="btn btn-sm btn-dark">🖨</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

</div>

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBuktiLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <img id="gambarBukti" src="" alt="Bukti Nontunai" class="img-preview-bukti">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gambar Resep -->
<div class="modal fade" id="modalGambarResep" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGambarResepLabel">Gambar Resep</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light text-center">
                <img id="gambarResepPreview" src="" alt="Gambar Resep" class="img-preview-bukti">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadResepBtn" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>

<script>
function lihatBukti(urlGambar, idTransaksi) {
    document.getElementById('modalBuktiLabel').innerText = 'Bukti Pembayaran - ' + idTransaksi;
    document.getElementById('gambarBukti').src = urlGambar;
    
    var modalElement = document.getElementById('modalBukti');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    } else if (window.jQuery && typeof $.fn.modal !== 'undefined') {
        $(modalElement).modal('show');
    } else {
        alert('Gagal memuat modal. Anda dapat membuka gambar di tab baru: ' + urlGambar);
    }
}

function lihatGambarResep(urlGambar, judul) {
    document.getElementById('modalGambarResepLabel').innerText = judul;
    document.getElementById('gambarResepPreview').src = urlGambar;
    document.getElementById('downloadResepBtn').href = urlGambar;
    
    var modalElement = document.getElementById('modalGambarResep');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    } else if (window.jQuery && typeof $.fn.modal !== 'undefined') {
        $(modalElement).modal('show');
    } else {
        window.open(urlGambar, '_blank');
    }
}
</script>