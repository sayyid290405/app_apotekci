    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manajemen Pengadaan Barang</title>
        <!-- Menggunakan Bootstrap 5 untuk konsistensi class yang kamu pakai -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome untuk icon mata -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .status-select {
                cursor: pointer;
                border-radius: 20px !important;
                font-weight: 600;
                text-align: center;
            }
        </style>
    </head>

    <body class="bg-light">
        <div class="container py-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="mb-4 text-dark">📦 List Permohonan Pengadaan Barang</h3>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode Pembelian</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th>Item Produk</th>
                                    <!-- <td>Jumlah Pembelian</td> -->
                                    <th>Total Pembelian</th>
                                    <!-- <th class="text-center">Dibuat Oleh</th> -->
                                    <th class="text-center">Status Approval</th>
                                    
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(empty($pembelian)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Data kosong</td>
                                    </tr>
                                <?php endif; ?>

                                <?php $no=1; foreach($pembelian as $p): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td> 
                                        <td class="fw-bold text-primary"><?= $p->kode_pembelian ?? '-' ?></td>
                                        <td><?= $p->nama_supplier ?? '-' ?></td>
                                        <td><?= date('d M Y', strtotime($p->tanggal ?? date('Y-m-d'))) ?></td>
                                        <td><?= $p->nama_produk ?? '-' ?></td>
                                        <td>Rp <?= number_format($p->total ?? 0,0,',','z.') ?></td>

                                        <td style="min-width: 150px;">
                                            <form method="post" action="<?= base_url('supplier/update_status_supplier') ?>">
                                                <input type="hidden" name="id_pembelian" value="<?= $p->id_pembelian ?>">
                                                <input type="hidden" 
                                                    name="<?= $this->security->get_csrf_token_name(); ?>" 
                                                    value="<?= $this->security->get_csrf_hash(); ?>">

                                                <?php
                                                    $class = 'bg-warning text-dark';
                                                    if($p->status == 'disetujui') {
                                                        $class = 'bg-warning text-black'; 
                                                    } elseif($p->status == 'diproses') {
                                                       $class = 'bg-success text-white';
                                                    } elseif($p->status == 'ditolak') {
                                                        $class = 'bg-danger text-white';
                                                    }
                                                ?>
                                            <select name="status" 
                                            onchange="this.form.submit()" 
                                            class="form-select form-select-sm status-select <?= $class ?>">
                                                <option value="disetujui" <?= $p->status=='disetujui'?'selected':'' ?>>disetujui</option>
                                                <option value="diproses" <?= $p->status=='diproses'?'selected':'' ?>>diproses</option>
                                                <option value="ditolak" <?= $p->status=='ditolak'?'selected':'' ?>>ditolak</option>
                                            </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>