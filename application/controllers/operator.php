<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operator extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            redirect('auth');
        }
        $this->load->model('M_operator');
        $this->load->library('form_validation');
    }

    // Tampilkan daftar operator
    public function index()
    {
        $data['title'] = 'Data Operator';
        $data['operator'] = $this->db->get('operator')->result_array();

        $data['user'] = [
            'username'   => $this->session->userdata('username'),
            'nama'       => $this->session->userdata('nama'),
            'last_login' => $this->session->userdata('last_login')
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('operator/index', $data);
        $this->load->view('templates/footer', $data);
    }

    // Form tambah operator
    public function tambah()
    {
        $data['title'] = 'Tambah Operator';
        $data['user'] = [
            'username'   => $this->session->userdata('username'),
            'nama'       => $this->session->userdata('nama'),
            'last_login' => $this->session->userdata('last_login')
        ];

        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules(
            'username',
            'Username',
            'required|trim|is_unique[operator.username]',
            ['is_unique' => 'Username sudah digunakan!']
        );

        // Validasi password dengan regex (huruf kecil, angka, dan simbol)
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|min_length[8]|regex_match[/^(?=.[a-z])(?=.\d)(?=.*[\W_]).+$/]',
            [
                'min_length'   => 'Password minimal 8 karakter.',
                'regex_match'  => 'Password harus mengandung huruf kecil, angka, dan simbol.'
            ]
        );

        $this->form_validation->set_rules(
            'password_confirm',
            'Konfirmasi Password',
            'required|matches[password]',
            ['matches' => 'Konfirmasi password tidak cocok.']
        );

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('operator/tambah', $data);
            $this->load->view('templates/footer', $data);
        } else {
            $data_insert = [
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'username'     => $this->input->post('username', TRUE),
                'password'     => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'last_login'   => NULL
            ];

            $this->db->insert('operator', $data_insert);
            $this->session->set_flashdata('success', 'Operator baru berhasil ditambahkan.');
            redirect('operator');
        }
    }

    // Form edit operator
    public function edit($id)
    {
        $data['title'] = 'Edit Operator';
        $data['operator'] = $this->db->get_where('operator', ['operator_id' => $id])->row_array();

        $data['user'] = [
            'username'   => $this->session->userdata('username'),
            'nama'       => $this->session->userdata('nama'),
            'last_login' => $this->session->userdata('last_login')
        ];

        if (!$data['operator']) {
            show_404();
        }

        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');

        // Jika password diisi, maka validasi tambahan
        if ($this->input->post('password')) {
            $this->form_validation->set_rules(
                'password',
                'Password',
                'required|min_length[8]|regex_match[/^(?=.[a-z])(?=.\d)(?=.*[\W_]).+$/]',
                [
                    'min_length'   => 'Password minimal 8 karakter.',
                    'regex_match'  => 'Password harus mengandung huruf kecil, angka, dan simbol.'
                ]
            );
            $this->form_validation->set_rules(
                'password_confirm',
                'Konfirmasi Password',
                'matches[password]',
                ['matches' => 'Konfirmasi password tidak cocok.']
            );
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('operator/edit', $data);
            $this->load->view('templates/footer', $data);
        } else {
            $update_data = [
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'username'     => $this->input->post('username', TRUE),
            ];

            // Update password jika diisi
            if ($this->input->post('password')) {
                $update_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $this->db->where('operator_id', $id);
            $this->db->update('operator', $update_data);

            $this->session->set_flashdata('success', 'Data operator berhasil diperbarui.');
            redirect('operator');
        }
    }

    // Hapus operator
    public function hapus($id)
    {
        $this->db->where('operator_id', $id);
        $this->db->delete('operator'); // Perbaikan: harus delete, bukan update
        $this->session->set_flashdata('success', 'Data operator berhasil dihapus.');
        redirect('operator');
    }
}