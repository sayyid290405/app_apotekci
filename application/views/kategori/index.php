<div class="container-fluid">

<div class="d-flex justify-content-between mb-3">
    <h4>📂 Data Kategori</h4>

    <a href="<?= base_url('kategori/tambah') ?>" class="btn btn-success">
        ➕ Tambah Kategori
    </a>
</div>

<!-- SEARCH -->
<div class="mb-3">
    <input type="text" id="searchKategori" class="form-control" placeholder="🔍 Cari kategori...">
</div>

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="table-responsive">
<table class="table table-hover" id="kategoriTable">
<thead class="table-success">
<tr>
    <th width="5%">#</th>
    <th width="25%">Nama</th>
    <th width="25%">Peruntukan</th>
    <th width="25%">Kelas Obat</th>
    <th class="text-center" width="10%">Aksi</th>
</tr>
</thead>

<tbody id="kategoriBody">

<?php if(empty($kategori)): ?>
<tr>
    <td colspan="6" class="text-center text-muted py-4">
        <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
        Belum ada data kategori
    </td>
</tr>
<?php else: ?>
    <?php $no=1; foreach($kategori as $k): 
        $isUsed = $this->Kategori_model->isUsed($k->id_kategori);
        $count = $this->Kategori_model->countUsed($k->id_kategori);
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><strong><?= $k->nama_kategori ?></strong></td>
        <td><?= $k->peruntukan_usia ?></td>
        <td><?= $k->kelas_obat ?></td>
        <td class="text-center">
            <a href="<?= base_url('kategori/edit/'.$k->id_kategori) ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
            </a>

            <?php if($isUsed): ?>
                <button onclick="showLockedAlert(<?= $k->id_kategori ?>, '<?= $k->nama_kategori ?>', <?= $count ?>)" 
                        class="btn btn-secondary btn-sm" title="Tidak bisa dihapus karena digunakan">
                    <i class="fas fa-lock"></i>
                </button>
            <?php else: ?>
                <button onclick="hapusKategori(<?= $k->id_kategori ?>, '<?= $k->nama_kategori ?>')" 
                        class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
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

</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // ==================== SEARCH KATEGORI ====================
    var searchTimer;
    $('#searchKategori').on('keyup', function() {
        clearTimeout(searchTimer);
        var keyword = $(this).val();
        
        searchTimer = setTimeout(function() {
            if(keyword.length > 0) {
                $.ajax({
                    url: '<?= base_url("kategori/search") ?>',
                    type: 'GET',
                    data: { q: keyword },
                    dataType: 'json',
                    success: function(data) {
                        var rows = '';
                        if(data.length > 0) {
                            var no = 1;
                            $.each(data, function(index, item) {
                                // Cek status via AJAX
                                $.ajax({
                                    url: '<?= base_url("kategori/cek_status/") ?>' + item.id_kategori,
                                    type: 'GET',
                                    dataType: 'json',
                                    async: false,
                                    success: function(status) {
                                        var statusBadge = '';
                                        var actionBtn = '';
                                        if(status.status == 'locked') {
                                            statusBadge = `<span class="badge bg-danger">Terkunci (${status.jumlah_produk})</span>`;
                                            actionBtn = `<button onclick="showLockedAlert(${item.id_kategori}, '${item.nama_kategori}', ${status.jumlah_produk})" class="btn btn-secondary btn-sm"><i class="fas fa-lock"></i></button>`;
                                        } else {
                                            statusBadge = `<span class="badge bg-success">Tersedia</span>`;
                                            actionBtn = `<button onclick="hapusKategori(${item.id_kategori}, '${item.nama_kategori}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>`;
                                        }
                                        
                                        rows += `
                                            <tr>
                                                <td>${no++}</td>
                                                <td><strong>${item.nama_kategori}</strong></td>
                                                <td>${item.peruntukan_usia || '-'}</td>
                                                <td>${item.kelas_obat || '-'}</td>
                                                <td>${statusBadge}</td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('kategori/edit/') ?>${item.id_kategori}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    ${actionBtn}
                                                </td>
                                            </tr>
                                        `;
                                    }
                                });
                            });
                        } else {
                            rows = `
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-search fa-2x d-block mb-2"></i>
                                        Tidak ditemukan
                                    </td>
                                </tr>
                            `;
                        }
                        $('#kategoriBody').html(rows);
                    }
                });
            } else {
                location.reload();
            }
        }, 300);
    });
});

// ==================== HAPUS KATEGORI ====================
function hapusKategori(id, nama) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: `Kategori <strong>"${nama}"</strong> akan dihapus secara permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url("kategori/hapus/") ?>' + id;
        }
    });
}

// ==================== SHOW LOCKED ALERT ====================
function showLockedAlert(id, nama, jumlah) {
    Swal.fire({
        title: ' Kategori Terkunci!',
        html: `
            <div style="text-align:left;">
                <p><strong>Kategori:</strong> ${nama}</p>
                <p><strong>Status:</strong> <span class="badge bg-danger">Digunakan oleh ${jumlah} produk</span></p>
                <hr>
                <p style="color:#dc3545; font-weight:bold;">
                    ⚠️ Kategori ini TIDAK BISA DIHAPUS karena masih digunakan oleh produk!
                </p>
                <p style="font-size:13px; color:#666;">
                    Silakan hapus atau ubah kategori pada produk yang menggunakan kategori ini terlebih dahulu.
                </p>
            </div>
        `,
        icon: 'error',
        confirmButtonColor: '#6c757d',
        confirmButtonText: 'OK, Saya Mengerti'
    });
}

// ==================== TAMPILKAN NOTIFIKASI DARI FLASHDATA ====================
<?php if($this->session->flashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        html: '<?= $this->session->flashdata('success') ?>',
        timer: 3000,
        showConfirmButton: true
    });
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        html: '<?= $this->session->flashdata('error') ?>',
        confirmButtonColor: '#d33'
    });
<?php endif; ?>
</script>

<style>
.badge {
    font-size: 11px;
    padding: 5px 10px;
}
.table td {
    vertical-align: middle;
}
</style>