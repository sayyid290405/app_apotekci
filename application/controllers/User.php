<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        // PENTING: Panggil constructor kelas induk terlebih dahulu
        parent::__construct(); 
        
        $this->load->library('session');
        $this->load->helper('url');
        
        // Memuat Model User
        $this->load->model('M_user'); 
        
        // Cek login: Hanya user yang sudah login yang bisa mengakses halaman ini
        $this->load->model('M_user'); 
        // 1. WAJIB LOAD MODEL PRODUK DI SINI
        $this->load->model('M_Product'); 
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    /**
     * Menampilkan halaman profil pengguna yang sedang login.
     */
    public function profile() {
        
        $user_id = $this->session->userdata('user_id');
        
        // 1. Ambil data user lengkap dari database (menggunakan get_user_by_id)
        $data['user'] = $this->M_user->get_user_by_id($user_id); 
        
        // Handle jika data user tidak ditemukan
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Data pengguna tidak ditemukan.');
            redirect('dashboard');
        }

        // 2. LOGIKA PENENTUAN GAMBAR PROFIL BERDASARKAN ROLE_ID
        // Mengacu pada file SVG yang Anda miliki: undraw_profile_1/2/3.svg
        $role_id = $data['user']['role_id'];
        $profile_image = 'default.svg';     
        
        if ($role_id == 1) {
            $profile_image = 'undraw_profile_1.svg'; //  Admin
        } elseif ($role_id == 2) {
            $profile_image = 'undraw_profile_2.svg'; // Sales
        } elseif ($role_id == 3) {
            $profile_image = 'undraw_profile_3.svg'; // Manager
        }

        // Timpa kolom 'image' di array $data['user'] dengan gambar yang ditentukan
        $data['user']['image'] = $profile_image;

        // 3. Siapkan Data Session untuk View (diperlukan untuk badge role)
    public function index(){
        // 2. AMBIL DATA PRODUK DARI DATABASE
        $data['produk'] = $this->M_Product->get_all_produk(); 
        $data['title'] = 'Dashboard - Bayur Farma';

        // 3. KIRIM VARIABEL $data KE VIEW
        $this->load->view('user/template/topbar', $data);
        $this->load->view('user/dashboard', $data);
    }

    public function profile() {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->M_user->get_user_by_id($user_id); 
        
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Data pengguna tidak ditemukan.');
            redirect('user'); // Sesuai nama controller
        }

        $role_id = $data['user']['role_id'];
        $profile_image = 'default.svg';     
        
        // Logika pemilihan gambar profil berdasarkan role_id
        if ($role_id == 1) {
            $profile_image = 'undraw_profile_1.svg';
        } elseif ($role_id == 2) {
            $profile_image = 'undraw_profile_2.svg';
        } elseif ($role_id == 3) {
            $profile_image = 'undraw_profile_3.svg';
        }

        $data['user']['image'] = $profile_image;
        $data['user_session'] = [
            'role' => $this->session->userdata('role'),
            'role_id' => $role_id
        ];

        // 4. Load View
        $data['title'] = 'Profil Saya';

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data['user_session']); 
        // $this->load->view('template/topbar', $data); 
        $this->load->view('user/profile', $data); 
        $this->load->view('template/footer', $data);
    }
    
    
        $this->load->view('user/profile', $data); 
        $this->load->view('template/footer', $data);
    }
}