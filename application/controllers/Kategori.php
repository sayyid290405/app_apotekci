<?php
class Kategori extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_model');
    }

    public function index()
    {
        $data['js'] = 'kategori.js';
        $data['kategori'] = $this->Kategori_model->getAll();

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['action'] = base_url('kategori/simpan');

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/form', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Kategori_model->insert($data);

        $this->session->set_flashdata('success','Kategori berhasil ditambahkan');
        redirect('kategori');
    }

    public function edit($id)
    {
        $data['kategori'] = $this->Kategori_model->getById($id);
        $data['action'] = base_url('kategori/update/'.$id);

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/form', $data);
        $this->load->view('templates/footer');
    }

    public function update($id)
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Kategori_model->update($id, $data);

        $this->session->set_flashdata('success','Kategori berhasil diupdate');
        redirect('kategori');
    }

    public function hapus($id)
    {
        $this->Kategori_model->delete($id);

        $this->session->set_flashdata('success','Kategori berhasil dihapus');
        redirect('kategori');
    }

    public function search()
    {
        $keyword = $this->input->get('q');
        $data = $this->Kategori_model->search($keyword);

        echo json_encode($data);
    }
}