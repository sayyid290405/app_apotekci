<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-circle mr-2"></i> <?= $title; ?>
    </h1>

    <div class="row">

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar Akun</h6>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <?php 
                                // Variabel $image_file akan otomatis berisi nama file SVG yang sudah ditentukan di Controller
                                $image_file = $user['image'];
                            ?>
                            <img src="<?= base_url('assets/img/profile/') . $image_file; ?>" 
                                 class="img-fluid rounded-circle border border-secondary p-1" 
                                 alt="Profile Image"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            
                            <h5 class="mt-3 text-gray-800">
                                **<?= $user['fullname']; ?>**
                            </h5>
                            <span class="badge badge-info">
                                <?= strtoupper($user_session['role']); ?>
                            </span>
                        </div>
                        
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-4 text-muted">Nama Lengkap</div>
                                        <div class="col-sm-8 text-gray-900"><?= $user['fullname']; ?></div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-4 text-muted">Bergabung Sejak</div>
                                        <div class="col-sm-8 text-gray-900">
                                            <?= date('d F Y', strtotime($user['created_at'])); ?>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-4 text-muted">Terakhir Login</div>
                                        <div class="col-sm-8 text-gray-900">
                                            <?= date('d F Y H:i', strtotime($user['last_login'])); ?> WIB
                                        </div>
                                    </div>
                                </li>
                                                                
                            </ul>
                            
                            <a href="<?= site_url('dashboard'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-fw fa-tachometer-alt mr-2 text-primary"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>

    </div>
</div>