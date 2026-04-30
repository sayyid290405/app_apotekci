<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        $this->load->database();
    }

    // =========================
    // LOGIN PAGE
    // =========================
    public function index() {

        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    // =========================
    // PROCESS LOGIN
    // =========================
    public function process() {

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth');
        }

        $email    = strtolower(trim($this->input->post('email', TRUE)));
        $password = $this->input->post('password', TRUE);

        $user = $this->db->where('LOWER(email)', $email)
                         ->get('users')
                         ->row();
        $user = $this->db
            ->where('email', $email)
            ->get('users')
            ->row();

        if (!$user) {
            $this->session->set_flashdata('error', 'Email tidak ditemukan');
            redirect('auth');
        }

        if (!password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Password salah');
            redirect('auth');
        }

        // ROLE NAME
        $role_name = $this->getRoleName($user->role_id);

        // SESSION
        $this->session->set_userdata([
            'logged_in' => true,
            'id_user'   => $user->id_user,
            'nama'      => $user->nama,
            'email'     => $user->email,
            'role_id'   => $user->role_id,
            'role'      => $role_name
        ]);

        $this->session->set_flashdata('success', 'Login berhasil.');

        // REDIRECT ROLE
        if($user->role_id == 1){
            redirect('dashboard'); // admin
        } elseif($user->role_id == 2){
            redirect('kasir'); // kasir
        } elseif($user->role_id == 3){
            redirect('Users'); // manajer
            redirect('manajer'); // manajer
        } elseif($user->role_id == 3){
            redirect('user'); // use
        } else {
            redirect('auth');
        }
    }

    // =========================
    // ROLE NAME
    // =========================
    private function getRoleName($role_id){
        switch($role_id){
            case 1: return 'admin';
            case 2: return 'kasir';
            case 3: return 'manajer';
            case 2: return 'manajer';
            case 3: return 'user';
            default: return 'unknown';
        }
    }

    // =========================
    // REGISTER PAGE
    // =========================
    
    public function register(){
        $data['title'] = 'Register';
        $this->load->view('auth/register', $data);
    }


    public function process_register(){

        $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register');
        } else {

            $email_user = strtolower($this->input->post('email', TRUE));
            $nama_user  = $this->input->post('nama', TRUE);

            $data = [
                'nama'       => htmlspecialchars($nama_user),
                'email'      => htmlspecialchars($email_user),
                'password'   => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role_id'    => 3,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // INSERT KE DATABASE
            $this->db->insert('users', $data);

            if ($this->db->affected_rows() > 0) {
                $this->_sendEmail($email_user, $nama_user);
                $this->session->set_flashdata('success', 'Registrasi Berhasil! Silakan Login.');
            } else {
                $this->session->set_flashdata('error', 'Registrasi Gagal!');
            }

            redirect('auth');
        }
    }

    // =========================
    // SEND EMAIL
    // =========================
    private function _sendEmail($to_email, $nama)
    {
        $config = [
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'sayyidabdul991@gmail.com',
            'smtp_pass' => 'hghq cvsb efct ervq',
            'smtp_port' => 465,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        ];

        $this->load->library('email', $config);
        $this->email->initialize($config);

        $this->email->from('no-reply@apotekanda.com', 'Sistem Manajemen Apotek');
        $this->email->to($to_email);
        $this->email->subject('Konfirmasi Registrasi Akun');
        $this->email->message("Selamat <b>$nama</b>, akun Anda berhasil dibuat.");

        if (!$this->email->send()) {
            echo $this->email->print_debugger();
            die;
        }
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}