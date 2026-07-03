<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url','form']);
    }

    // ================= LOGIN PAGE =================
public function index()
{
    if ($this->session->userdata('logged_in')) {

        switch ($this->session->userdata('role_id')) {

            case 1:
                redirect('dashboard');
                break;

            case 2:
                redirect('supplier/laporan_pembelian');
                break;

            case 3:
                redirect('users');
                break;

            default:
                $this->session->sess_destroy();
                redirect('auth');
                break;
        }
    }

    $data['title'] = 'Login';
    $this->load->view('auth/login', $data);
}
    // ================= PROCESS LOGIN =================
    public function process(){

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('error', validation_errors());
            redirect('auth');
        }

        $email    = strtolower(trim($this->input->post('email')));
        $password = $this->input->post('password');

        $user = $this->Users_model->get_user_by_email($email);

        if(!$user){
            $this->session->set_flashdata('error', 'Email tidak ditemukan');
            redirect('auth');
        }

        if(!password_verify($password, $user->password)){
            $this->session->set_flashdata('error', 'Password salah');
            redirect('auth');
        }

        // set session
        $this->session->set_userdata([
            'logged_in' => true,
            'id_user'   => $user->id_user,
            'nama'      => $user->nama,
            'email'     => $user->email,
            'role_id'   => $user->role_id
        ]);

        // redirect berdasarkan role
switch($user->role_id){
    case 1:
        $this->session->set_flashdata('success', 'Selamat datang Admin');
        redirect('dashboard');
        break;

    case 2:
        $this->session->set_flashdata('success', 'Selamat datang Supplier');
        redirect('supplier/laporan_pembelian');
        break;

    case 3:
        $this->session->set_flashdata('success', 'Selamat datang Manajer');
        redirect('Users');
        break;
}
    }

    // ================= REGISTER =================
    public function register(){
        $data['title'] = 'Register';
        $this->load->view('auth/register', $data);
    }

    public function process_register(){

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE){
            $this->load->view('auth/register');
            return;
        }

        $data = [
            'nama'     => $this->input->post('nama'),
            'email'    => strtolower($this->input->post('email')),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role_id'  => 3,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->User_models->insert_user($data);

        $this->session->set_flashdata('success','Registrasi berhasil');
        redirect('auth');
    }

    // ================= LOGOUT =================
public function logout()
{
    if ($this->session->userdata('logged_in')) {

        $this->session->unset_userdata([
            'logged_in',
            'id_user',
            'nama',
            'email',
            'role_id'
        ]);

        $this->session->set_flashdata('success', 'Berhasil logout');
    } else {
        $this->session->set_flashdata('error', 'Anda belum login');
    }

    redirect('auth');
}
}