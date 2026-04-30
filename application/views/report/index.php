<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('report'); ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="from" class="form-control"
                               value="<?= isset($filter['from']) ? $filter['from'] : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="to" class="form-control"
                               value="<?= isset($filter['to']) ? $filter['to'] : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Sales</label>
                        <select name="sales_id" class="form-control">
                            <option value="">-- Semua Sales --</option>
                            <?php foreach($sales as $s): ?>
                                <option value="<?= $s->id ?>" <?= (isset($filter['sales_id']) && $filter['sales_id']==$s->id) ? 'selected' : '' ?>>
                                    <?= $s->name ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">Status Order</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <?php foreach($status_labels as $status): ?>
                                <option value="<?= $status ?>" <?= (isset($filter['status']) && $filter['status']==$status) ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan Filter</button>
                        <a href="<?= base_url('report'); ?>" class="btn btn-secondary">Reset</a>
                    </div>
                    <a href="<?= base_url('report/export_pdf?' . $_SERVER['QUERY_STRING']); ?>" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Total Order Berdasarkan Status</h6>
                </div>
                <div class="card-body">
                    <div style="height:300px; position: relative;">
                        <canvas id="orderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Laporan Order</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="reportTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Order</th>
                            <th>Customer</th>
                            <th>Produk</th>
                            <th>Sales</th>
                            <th>Status</th>
                            <th>Tgl Order</th>
                            <th>Tgl Update Status</th> <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): $no=1; ?>
                            <?php foreach($orders as $o): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><strong><?= $o->order_code ?></strong></td>
                                    <td><?= $o->customer_name ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= !empty($o->product_names) ? $o->product_names : '<i class="text-danger">Tidak ada produk</i>'; ?>
                                        </small>
                                    </td>
                                    <td><?= $o->sales_name ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $badge = 'secondary';
                                            if($o->status == 'dikirim') $badge = 'info';
                                            if($o->status == 'selesai') $badge = 'success';
                                            if($o->status == 'dibatalkan') $badge = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $badge ?> p-2">
                                            <?= ucfirst($o->status) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d-m-Y', strtotime($o->order_date)) ?></td>
                                    <td>
                                        <?php 
                                            // Menampilkan tanggal spesifik sesuai kolom di DB (image_7618e5.png)
                                            if ($o->status == 'dikirim' && !empty($o->sent_at)) {
                                                echo '<small class="text-info font-weight-bold"><i class="fas fa-truck"></i> ' . date('d-m-Y H:i', strtotime($o->sent_at)) . '</small>';
                                            } elseif ($o->status == 'selesai' && !empty($o->completed_at)) {
                                                echo '<small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> ' . date('d-m-Y H:i', strtotime($o->completed_at)) . '</small>';
                                            } elseif ($o->status == 'dibatalkan' && !empty($o->canceled_at)) {
                                                echo '<small class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> ' . date('d-m-Y H:i', strtotime($o->canceled_at)) . '</small>';
                                            } else {
                                                echo '<span class="text-muted small italic">Menunggu update...</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-right"><strong>Rp <?= number_format($o->total_price, 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var chartDataGlobal = {
        labels: <?= json_encode($status_labels); ?>,
        totals: <?= json_encode($status_totals); ?>
    };
</script>