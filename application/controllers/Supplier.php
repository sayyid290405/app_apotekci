<?php
class Supplier extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Supplier_model');
    }

    public function index()
    {
    $this->load->library('pagination');

    $keyword = $this->input->get('q'); // 🔥 ambil dari URL
    $data['keyword'] = $keyword;
    $data['js'] = 'supplier.js';

    // total data (dengan filter)
    $total = $this->Supplier_model->countFiltered($keyword);

    $config['base_url'] = base_url('supplier/index?q='.$keyword);
    $config['total_rows'] = $total;
    $config['per_page'] = 5;

    $this->pagination->initialize($config);

    $page = $this->uri->segment(3);

    $data['supplier'] = $this->Supplier_model->getFiltered($config['per_page'], $page, $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $this->load->view('templates/header',$data);
    $this->load->view('templates/sidebar');
    $this->load->view('supplier/index',$data);
    $this->load->view('templates/footer');
}

    public function tambah()
    {
        $data['action'] = base_url('supplier/simpan');

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('supplier/form',$data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Supplier_model->insert($data);

        $this->session->set_flashdata('success','Supplier berhasil ditambahkan');
        redirect('supplier');
    }

    public function edit($id)
    {
        $data['supplier'] = $this->Supplier_model->getById($id);
        $data['action'] = base_url('supplier/update/'.$id);

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('supplier/form',$data);
        $this->load->view('templates/footer');
    }

    public function update($id)
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Supplier_model->update($id,$data);

        $this->session->set_flashdata('success','Supplier berhasil diupdate');
        redirect('supplier');
    }

    public function hapus($id)
    {
        $this->Supplier_model->delete($id);

        $this->session->set_flashdata('success','Supplier berhasil dihapus');
        redirect('supplier');
    }

    public function detail($id)
{
    $data['supplier'] = $this->Supplier_model->getById($id);
    $data['produk'] = $this->Supplier_model->getProdukBySupplier($id);

    $this->load->view('templates/header',$data);
    $this->load->view('templates/sidebar');
    $this->load->view('supplier/detail',$data);
    $this->load->view('templates/footer');
}

public function search()
{
    $keyword = $this->input->get('q');

    $data = $this->Supplier_model->getFilteredAjax($keyword);

    echo json_encode($data);
}

}