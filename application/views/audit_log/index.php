<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">

            <form id="filterForm" class="form-inline mb-3" method="GET" action="<?= base_url('audit_log') ?>">
                
                <div class="form-group mr-2">
                    <label class="mr-2 d-none d-lg-inline">Tampilkan:</label>
                    <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                        <?php 
                            $current_pp = $this->input->get('per_page') ? $this->input->get('per_page') : 20; 
                            $options = [10, 15, 20, 50, 100];
                            foreach ($options as $opt):
                        ?>
                            <option value="<?= $opt ?>" <?= $current_pp == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mr-2">
                    <input type="date" name="start_date" class="form-control form-control-sm"
                        value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                </div>

                <div class="form-group mr-2">
                    <input type="date" name="end_date" class="form-control form-control-sm"
                        value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                </div>

                <div class="form-group mr-2">
                    <select name="user_id" class="form-control form-control-sm">
                        <option value="">-- Semua User --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>"
                                <?= (isset($_GET['user_id']) && $_GET['user_id'] == $u->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->fullname) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mr-2">
                    <select name="action" class="form-control form-control-sm">
                        <option value="">-- Semua Aksi --</option>
                        <?php 
                            $actions = ['login', 'logout', 'create', 'update', 'delete', 'error'];
                            foreach ($actions as $act):
                        ?>
                            <option value="<?= $act ?>" <?= (@$_GET['action'] == $act) ? 'selected' : '' ?>><?= ucfirst($act) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mr-2">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari..."
                        value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                </div>

                <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="fas fa-filter"></i> Cari    </button>

                <a class="btn btn-sm btn-secondary mr-1" href="<?= base_url('audit_log') ?>" title="Reset">
                    <i class="fas fa-sync"></i> Reset
                </a>

                <a class="btn btn-sm btn-success mr-1" 
                    href="<?= site_url('audit_log/export_csv') . '?' . $_SERVER['QUERY_STRING'] ?>" title="Export CSV">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>

                <?php if ($this->session->userdata('role_id') == 1): ?>
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                <?php endif; ?>

            </form>

            <?php if (!$this->uri->segment(3) || $this->uri->segment(3) == 0): ?>
            <div class="mb-4">
                <canvas id="logChart" height="100"></canvas>
            </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): 
                            // Nomor urut dinamis sesuai page & offset
                            $no = $this->uri->segment(3) ? $this->uri->segment(3) + 1 : 1; 
                            foreach ($logs as $log): 
                        ?>
                        <tr class="<?= ($log->action == 'error') ? 'table-danger' : '' ?>">
                            <td><?= $no++; ?></td>
                            <td class="small"><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></td>
                            <td><?= htmlspecialchars($log->fullname) ?> <br> <small class="text-muted">(<?= $log->username ?>)</small></td>
                            <td>
                                <?php
                                    $badge = [
                                        'login'  => 'success', 'logout' => 'secondary',
                                        'create' => 'primary', 'update' => 'warning',
                                        'delete' => 'danger',  'error'  => 'dark'
                                    ][$log->action] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $badge ?>"><?= ucfirst($log->action) ?></span>
                            </td>
                            <td class="small" title="<?= htmlspecialchars($log->detail) ?>">
                                <?= (strlen($log->detail) > 80) ? htmlspecialchars(substr($log->detail, 0, 80)) . '...' : htmlspecialchars($log->detail) ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-sm-12 col-md-5">
                    <div class="small text-muted">
                        Menampilkan <?= count($logs) ?> entri di halaman ini.
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <?= $pagination_links; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="form-truncate" method="post" action="<?= site_url('audit_log/truncate') ?>">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
</form>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function confirmDelete() {
    if (confirm('Hapus seluruh log permanen?')) {
        document.getElementById('form-truncate').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const chartEl = document.getElementById('logChart');
    if (chartEl) {
        new Chart(chartEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [
                    { label: 'Login', data: <?= $chart_login ?>, backgroundColor: '#28a745' },
                    { label: 'Logout', data: <?= $chart_logout ?>, backgroundColor: '#6c757d' },
                    { label: 'Create', data: <?= $chart_create ?>, backgroundColor: '#007bff' },
                    { label: 'Update', data: <?= $chart_update ?>, backgroundColor: '#ffc107' },
                    { label: 'Delete', data: <?= $chart_delete ?>, backgroundColor: '#dc3545' },
                    { label: 'Error', data: <?= $chart_error ?>, backgroundColor: '#343a40' }
                ]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
});
</script>