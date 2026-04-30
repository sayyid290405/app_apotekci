<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <?php if ($this->session->userdata('role_id') == 1): ?>
            <form action="<?= base_url('audit_log/truncate') ?>" method="post" onsubmit="return confirm('PERINGATAN! Semua data log akan dihapus permanen. Lanjutkan?')">
                <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                    <i class="fas fa-trash fa-sm text-white-50"></i> Kosongkan Semua Log
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-line mr-1"></i> Tren Aktivitas Sistem (30 Hari Terakhir)
            </h6>
        </div>
        <div class="card-body">
            <div style="position: relative; height:300px; width:100%">
                <canvas id="logChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-1"></i> Detail Riwayat Aktivitas</h6>
        </div>
        <div class="card-body">

            <form class="form-inline mb-4" method="GET" action="<?= base_url('audit_log') ?>">
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="start_date" class="form-control form-control-sm"
                        value="<?= $this->input->get('start_date') ?>" title="Tanggal Mulai">
                </div>
                <div class="form-group mr-2 mb-2">
                    <input type="date" name="end_date" class="form-control form-control-sm"
                        value="<?= $this->input->get('end_date') ?>" title="Tanggal Selesai">
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="user_id" class="form-control form-control-sm">
                        <option value="">-- Semua User --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>" <?= ($this->input->get('user_id') == $u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->fullname) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <select name="action" class="form-control form-control-sm">
                        <option value="">-- Semua Aksi --</option>
                        <?php 
                        $actions = ['login', 'logout', 'create', 'update', 'delete', 'error'];
                        foreach($actions as $act): ?>
                            <option value="<?= $act ?>" <?= ($this->input->get('action') == $act) ? 'selected' : '' ?>><?= ucfirst($act) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="fas fa-filter"></i> Filter</button>
                    <a class="btn btn-sm btn-secondary mr-1" href="<?= base_url('audit_log') ?>"><i class="fas fa-sync"></i> Reset</a>
                    <a class="btn btn-sm btn-success" href="<?= site_url('audit_log/export_csv') . '?' . $_SERVER['QUERY_STRING'] ?>">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="auditLogTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Waktu</th>
                            <th width="15%">User</th>
                            <th width="10%">Aksi</th>
                            <th width="55%">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): foreach ($logs as $log): ?>
                        <tr class="<?= ($log->action == 'error') ? 'table-danger' : '' ?>">
                            <td class="text-center"></td>
                            <td data-sort="<?= strtotime($log->created_at) ?>">
                                <?= date('d/m/Y H:i', strtotime($log->created_at)) ?>
                            </td>
                            <td><strong><?= htmlspecialchars($log->fullname) ?></strong></td>
                            <td class="text-center">
                                <?php
                                    $badge = [
                                        'login'  => 'success',
                                        'logout' => 'secondary',
                                        'create' => 'primary',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                        'error'  => 'dark'
                                    ][$log->action] ?? 'info';
                                ?>
                                <span class="badge badge-<?= $badge ?> px-2 py-1" style="min-width: 60px;">
                                    <?= ucfirst($log->action) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($log->detail) ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // 1. Inisialisasi Chart.js
    const ctx = document.getElementById('logChart').getContext('2d');
    
    // Data dari Controller dengan pengaman jika kosong
    const chartLabels = <?= !empty($chart_labels) ? $chart_labels : '[]' ?>;
    const chartCreate = <?= !empty($chart_create) ? $chart_create : '[]' ?>;
    const chartDelete = <?= !empty($chart_delete) ? $chart_delete : '[]' ?>;
    const chartUpdate = <?= !empty($chart_update) ? $chart_update : '[]' ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                { 
                    label: 'Tambah (Create)', 
                    data: chartCreate, 
                    borderColor: '#4e73df', 
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.3 
                },
                { 
                    label: 'Hapus (Delete)', 
                    data: chartDelete, 
                    borderColor: '#e74a3b', 
                    backgroundColor: 'transparent',
                    tension: 0.3 
                },
                { 
                    label: 'Ubah (Update)', 
                    data: chartUpdate, 
                    borderColor: '#f6c23e', 
                    backgroundColor: 'transparent',
                    tension: 0.3 
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // 2. Inisialisasi DataTable & Penomoran Otomatis
    var t = $('#auditLogTable').DataTable({
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }],
        "order": [[ 1, 'desc' ]],
        "pageLength": 25,
        "language": {
            "search": "Cari Cepat:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Tidak ada data ditemukan",
            "paginate": { "next": "Lanjut", "previous": "Kembali" }
        }
    });

    // Reset nomor setiap kali tabel di-sort atau di-filter
    t.on('order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

});
</script>