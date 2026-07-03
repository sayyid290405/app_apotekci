<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // load model
        $this->load->model('Users_model');

        if(!$this->session->userdata('logged_in')){
            redirect('auth/login');
        }
        if($this->session->userdata('role_id') != 3){
            redirect('auth/blocked');
        }
    }

    public function index()
    {
        $data['title'] = 'Manajemen Pengguna';
        $data['users'] = $this->Users_model->getAllUsers();
        $data['roles'] = $this->Users_model->getAllRoles();
        $this->load->view('manajer/templates/header', $data);
        $this->load->view('manajer/templates/sidebar_manajer', $data);
        $this->load->view('users/index', $data);
        $this->load->view('manajer/templates/footer');
    }

public function create()
{
    $data['title'] = 'Tambah User';
    $data['roles'] = $this->Users_model->getAllRoles(); // 🔥 WAJIB

    $this->load->view('templates/header',$data);
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('users/create',$data);
    $this->load->view('templates/footer');
}
    public function store()
    {
        $nama     = trim($this->input->post('nama', TRUE));
        $email    = strtolower(trim($this->input->post('email', TRUE)));
        $password = password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT);
        $role_id  = (int)$this->input->post('role_id', TRUE);

        $data = [
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
            'role_id' => $role_id
        ];

        if ($this->Users_model->createUser($data)) {
            $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan');
            redirect('users');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan pengguna');
            redirect('users/create');
        }
    }
    public function edit($id)
    {
        $data['title'] = 'Edit Pengguna';
        $data['user'] = $this->Users_model->getUserById($id);
        $data['roles'] = $this->Users_model->getAllRoles();
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Pengguna tidak ditemukan');
            redirect('users');
        }
        $this->load->view('manajer/templates/header', $data);
        $this->load->view('manajer/templates/sidebar_manajer', $data);
        $this->load->view('users/edit', $data);
        $this->load->view('manajer/templates/footer');
    }

public function update($id)
{
    // ambil data lama
    $user = $this->Users_model->getUserById($id);

    if(!$user){
        $this->session->set_flashdata('error','User tidak ditemukan');
        redirect('users');
    }

    // ambil input
    $nama     = trim($this->input->post('nama', TRUE));
    $email    = strtolower(trim($this->input->post('email', TRUE)));
    $role_id  = (int)$this->input->post('role_id', TRUE);
    $password = $this->input->post('password');

    // validasi sederhana
    if(empty($nama) || empty($email)){
        $this->session->set_flashdata('error','Nama & Email wajib diisi');
        redirect('users/edit/'.$id);
    }

    $data = [
        'nama' => $nama,
        'email' => $email,
        'role_id' => $role_id
    ];

    // 🔥 kalau password diisi → update
    if(!empty($password)){
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($this->Users_model->updateUser($id, $data)) {
        $this->session->set_flashdata('success', 'Pengguna berhasil diperbarui');
        redirect('users');
    } else {
        $this->session->set_flashdata('error', 'Gagal memperbarui pengguna');
        redirect('users/edit/' . $id);
    }
}

    public function delete($id)
    {
        if ($this->Users_model->deleteUser($id)) {
            $this->session->set_flashdata('success', 'Pengguna berhasil dihapus');
            redirect('users');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus pengguna');
            redirect('users');
        }
    }
    public function search()
{
    $keyword = $this->input->get('q');
    $data = $this->Users_model->getFilteredAjax($keyword);
    echo json_encode($data);
}
}
