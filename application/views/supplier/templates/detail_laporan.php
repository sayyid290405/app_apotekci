<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('laporan_supplier') ?>">Laporan</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart"></i> 
                                <?= $pembelian->kode_pembelian ?>
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('laporan_supplier/print_pembelian/' . $pembelian->id_pembelian) ?>" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                <a href="<?= base_url('laporan_supplier') ?>" class="btn btn-sm btn-default">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Info Header -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Kode Order</strong></td>
                                            <td>: <?= $pembelian->kode_pembelian ?></td>
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
                                                if($pembelian->status == 'selesai') $badge = 'success';
                                                elseif($pembelian->status == 'ditolak' || $pembelian->status == 'dibatalkan') $badge = 'danger';
                                                elseif($pembelian->status == 'disetujui') $badge = 'primary';
                                                elseif($pembelian->status == 'diproses') $badge = 'warning';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($pembelian->status) ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Supplier</strong></td>
                                            <td>: <?= $pembelian->nama_supplier ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>: <?= $pembelian->user_name ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Disetujui Oleh</strong></td>
                                            <td>: <?= $pembelian->approved_by_name ?? '-' ?></td>
                                        </tr>
                                        <?php if($pembelian->tanggal_approve): ?>
                                        <tr>
                                            <td><strong>Tgl Approve</strong></td>
                                            <td>: <?= date('d/m/Y H:i:s', strtotime($pembelian->tanggal_approve)) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Detail Produk -->
                            <h5 class="mt-3">Detail Produk</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($detail)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada detail produk</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach($detail as $d): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= $d->nama_produk ?> <?= $d->nama_satuan ? '(' . $d->nama_satuan . ')' : '' ?></td>
                                                <td class="text-center"><?= number_format($d->jumlah) ?> pcs</td>
                                                <td class="text-right"><?= number_format($d->harga, 0, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($d->subtotal, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="4" class="text-right">GRAND TOTAL</th>
                                        <th class="text-right"><?= number_format($pembelian->total, 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Catatan -->
                            <?php if($pembelian->catatan): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Catatan:</strong> <?= $pembelian->catatan ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>