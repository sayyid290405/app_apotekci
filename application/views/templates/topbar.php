<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    
    <div class="d-none d-sm-inline-block mr-auto">
        <?php
        // Tentukan sapaan berdasarkan jam saat ini (Pagi, Siang, Sore)
        $jam = date('H');
        if ($jam >= 5 && $jam < 12) {
            $salam = "Selamat Pagi";
        } elseif ($jam >= 12 && $jam < 17) {
            $salam = "Selamat Siang";
        } else {
            $salam = "Selamat Sore";
        }
        ?>

        <h1 class="h3 mb-1 text-gray-800">
            <?= $salam; ?>, Senang Melihat Anda Kembali **<?= $this->session->userdata('fullname'); ?>**!
        </h1>
        
        <p class="mb-0 text-gray-700 small">
            Siap Beraksi Hari Ini.!
        </p>
    </div>
    
    <ul class="navbar-nav ml-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= $this->session->userdata('fullname'); ?> (<?= $this->session->userdata('role'); ?>)
                </span>
                
                <?php 
                    $role_id = $this->session->userdata('role_id');
                    $profile_image = 'default.svg'; 
                    
                    if ($role_id == 1) {
                        $profile_image = 'undraw_profile_1.svg';
                    } elseif ($role_id == 2) {
                        $profile_image = 'undraw_profile_2.svg';
                    } elseif ($role_id == 3) {
                        $profile_image = 'undraw_profile_3.svg';
                    }
                ?>
                <img class="img-profile rounded-circle"
                    src="<?= base_url('assets/img/profile/') . $profile_image; ?>" width="40">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                
                <a class="dropdown-item" href="<?= site_url('user/profile'); ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                
                <div class="dropdown-divider"></div>
                
                <span class="dropdown-item text-muted small" style="pointer-events: none;">
                    **Terakhir Login:**
                    <?php 
                    $last_login = $this->session->userdata('last_login');
                    if (!empty($last_login)) {
                        echo date('d M Y H:i', strtotime($last_login)) . ' WIB';
                    } else {
                        echo 'Belum pernah login';
                    }
                    ?>
                </span>
                
                <div class="dropdown-divider"></div>
                
                <a class="dropdown-item" href="<?= site_url('auth/logout'); ?>">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>