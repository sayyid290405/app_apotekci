<div class="container mt-4">

    <h3>List Barang Tiba</h3>

    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('pembelian') ?>" class="btn btn-success">
            ➕ Pesan barang ke supplier
        </a>
    </div> -->

    <table class="table table-bordered table-striped mt-3">
        
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode Pembelian</th>
                <th>Nama Supplier</th>
                <th>Item Produk</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Catatan</th>
                <th>Verifikasi Penerimaan Barang</th>
            </tr>
        </thead>

        <tbody>

        <?php if(empty($pembelian_diproses)): ?>
            <tr>
                <td colspan="9" class="text-center">
                    Data kosong
                </td>
            </tr>
        <?php endif; ?>

        <?php $no = 1; ?>

        <?php foreach($pembelian_diproses as $p): ?>

            <tr>

                <td><?= $no++ ?></td>

                <td>
                    <?= $p->kode_pembelian ?? '-' ?>
                </td>

                <td>
                    <?= $p->nama_supplier ?? '-' ?>
                </td>

                <td>
                    <?= $p->nama_produk ?? '-' ?>
                </td>

                <td>
                    <?= date('d M Y', strtotime($p->tanggal ?? date('Y-m-d'))) ?>
                </td>

                <td>
                    Rp <?= number_format($p->total ?? 0,0,',','.') ?>
                </td>

                <!-- STATUS -->
                <td>

                    <?php if($p->status == 'menunggu'): ?>

                        <span class="badge bg-warning text-dark">
                            Menunggu
                        </span>

                    <?php elseif($p->status == 'disetujui'): ?>

                        <span class="badge bg-primary">
                            Disetujui
                        </span>

                    <?php elseif($p->status == 'diproses'): ?>

                        <span class="badge bg-info text-dark">
                            Diproses
                        </span>

                    <?php elseif($p->status == 'diterima'): ?>

                        <span class="badge bg-success">
                            diterima
                        </span>

                    <?php elseif($p->status == 'ditolak'): ?>

                        <span class="badge bg-danger">
                            Ditolak
                        </span>

                    <?php elseif($p->status == 'dibatalkan'): ?>

                        <span class="badge bg-secondary">
                            Dibatalkan
                        </span>

                    <?php else: ?>

                        <span class="badge bg-dark">
                            Unknown
                        </span>

                    <?php endif; ?>

                    <br>

                    <!-- <a href="<?= base_url('pembelian/detail/'.$p->id_pembelian) ?>" 
                       class="btn btn-sm btn-info mt-1">
                        <i class="fas fa-eye"></i>
                    </a> -->

                </td>

                <!-- CATATAN -->
                <td>
                    <?= $p->catatan ?? 'Tidak ada catatan' ?>
                </td>

<td>

    <form method="post"
          action="<?= base_url('supplier/update_status_admin') ?>"
          class="d-flex gap-2">

        <input type="hidden"
               name="id_pembelian"
               value="<?= $p->id_pembelian ?>">

        <input type="hidden"
               name="<?= $this->security->get_csrf_token_name(); ?>"
               value="<?= $this->security->get_csrf_hash(); ?>">

        <?php

            $class = 'bg-warning text-dark';

            if($p->status == 'selesai') {

                $class = 'bg-success text-white';

            } elseif($p->status == 'diproses') {

                $class = 'bg-info text-dark';

            } elseif($p->status == 'ditolak') {

                $class = 'bg-danger text-white';

            } elseif($p->status == 'dibatalkan') {

                $class = 'bg-secondary text-white';
            }

        ?>

        <select name="status"
                class="form-select form-select-sm <?= $class ?>">

            <option value="diproses"
                <?= $p->status == 'diproses' ? 'selected' : '' ?>>
                Diproses
            </option>

            <option value="diterima"
                <?= $p->status == 'diterima' ? 'selected' : '' ?>>
                diterima
            </option>

            <!-- <option value="dibatalkan"
                <?= $p->status == 'dibatalkan' ? 'selected' : '' ?>>
                Dibatalkan
            </option> -->

        </select>

        <button type="submit"
                class="btn btn-primary btn-sm">
            Update
        </button>

    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>  