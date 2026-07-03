
<div class="container mt-4">

    <h3 class="mb-4">📱 Verifikasi Pembayaran QRIS</h3>

    <div class="card shadow border-0">

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="200">ID Pesanan</th>
                    <td><?= $pesanan->id_pesanan ?></td>
                </tr>

                <tr>
                    <th>Total Bayar</th>
                    <td>
                        Rp <?= number_format($pesanan->total_harga,0,',','.') ?>
                    </td>
                </tr>

                <tr>
                    <th>Metode Bayar</th>
                    <td><?= strtoupper($pesanan->metode_bayar) ?></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <?php if($pesanan->status == 'menunggu_verifikasi'): ?>

                            <span class="badge bg-warning text-dark">
                                Menunggu Verifikasi
                            </span>

                        <?php else: ?>

                            <span class="badge bg-success">
                                Selesai
                            </span>

                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th>Bukti Transfer</th>
                    <td>

                        <?php if($pesanan->bukti_qris): ?>

                            <img src="<?= base_url('uploads/bukti/'.$pesanan->bukti_qris) ?>"
                                 class="img-fluid rounded border shadow"
                                 style="max-width:400px;">

                        <?php else: ?>

                            <div class="alert alert-danger mb-0">
                                Bukti transfer belum diupload
                            </div>

                        <?php endif; ?>

                    </td>
                </tr>

            </table>

            <?php if($pesanan->status == 'menunggu_verifikasi'): ?>

            <form method="post"
                  action="<?= base_url('kasir/approve_qris/'.$pesanan->id_pesanan) ?>">

                <input type="hidden"
                       name="<?= $this->security->get_csrf_token_name(); ?>"
                       value="<?= $this->security->get_csrf_hash(); ?>">

                <button class="btn btn-success">
                    ✅ Approve Pembayaran
                </button>

            </form>

            <?php endif; ?>

        </div>

    </div>

</div>