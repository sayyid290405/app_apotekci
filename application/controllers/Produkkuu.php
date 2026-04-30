<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Produk_model');
        $this->load->library(['session','form_validation']);

        // cek login
        if(!$this->session->userdata('logged_in')){
            redirect('auth');
        }

        // hanya admin
        if($this->session->userdata('role_id') != 1){
            redirect('auth/blocked');
        }
    }

    // =========================
    // LIST PRODUK
    // =========================
    public function index()
    {
        $data['title'] = 'Data Produk';
        $data['produk'] = $this->Produk_model->getAll();
        $data['js'] = 'produk.js';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/index', $data);
        $this->load->view('templates/footer');
    }

    private function uploadGambar()
{
    if(empty($_FILES['gambar_file']['name'])){
        return null;
    }

    $config['upload_path']   = './uploads/';
    $config['allowed_types'] = 'jpg|jpeg|png|webp';
    $config['max_size']      = 2048; // 2MB
    $config['encrypt_name']  = TRUE;

    $this->load->library('upload', $config);

    if(!$this->upload->do_upload('gambar_file')){
        return [
            'error' => $this->upload->display_errors()
        ];
    }

    $upload = $this->upload->data();

    return [
        'success' => 'uploads/' . $upload['file_name']
    ];
}

    // =========================
    // FORM TAMBAH
    // =========================
    public function tambah()
    {
        $data['title'] = 'Tambah Produk';
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action'] = base_url('produk/simpan');

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form',$data);
        $this->load->view('templates/footer');
    }

    // =========================
    // SIMPAN DATA
    // =========================
    public function simpan()
{
    $data = $this->input->post(NULL, TRUE);

    // =====================
    // HANDLE UPLOAD GAMBAR
    // =====================
    $upload = $this->uploadGambar();

    // ❌ kalau error upload
    if(isset($upload['error'])){
        $this->session->set_flashdata('error', $upload['error']);
        redirect('produk/tambah');
    }

    // ✅ kalau upload berhasil
    if(isset($upload['success'])){
        $data['gambar'] = base_url($upload['success']);
    } 
    // ✅ kalau pakai URL
    else {
        $data['gambar'] = $this->input->post('gambar_url');
    }

    // =====================
    // SIMPAN DATA
    // =====================
    $this->Produk_model->insert($data);

    $this->session->set_flashdata('success','Produk berhasil ditambahkan');
    redirect('produk');
}

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $data['title'] = 'Edit Produk';
        $data['produk'] = $this->Produk_model->getById($id);
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action'] = base_url('produk/update/'.$id);

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form',$data);
        $this->load->view('templates/footer');
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id)
{
    $data = $this->input->post(NULL, TRUE);

    // ambil data lama
    $produk_lama = $this->Produk_model->getById($id);

    // upload baru
    $upload = $this->uploadGambar();

    // ❌ jika error upload
    if(isset($upload['error'])){
        $this->session->set_flashdata('error', $upload['error']);
        redirect('produk/edit/'.$id);
    }

    // ✅ jika upload berhasil
    if(isset($upload['success'])){
        $data['gambar'] = base_url($upload['success']);

        // 🔥 hapus gambar lama (jika bukan URL external)
        if(!empty($produk_lama->gambar) && strpos($produk_lama->gambar, 'uploads/') !== false){
            $path = FCPATH . str_replace(base_url(), '', $produk_lama->gambar);

            if(file_exists($path)){
                unlink($path);
            }
        }
    }
    // ✅ jika pakai URL
    elseif($this->input->post('gambar_url')){
        $data['gambar'] = $this->input->post('gambar_url');
    }
    // ✅ fallback (pakai lama)
    else{
        $data['gambar'] = $produk_lama->gambar;
    }

    // bersihkan field tidak perlu
    unset($data['gambar_url']);
    unset($data['mode_gambar']);

    $this->Produk_model->update($id, $data);

    $this->session->set_flashdata('success','Produk berhasil diupdate');
    redirect('produk');
}


    // =========================
    // HAPUS
    // =========================
    public function hapus($id)
{
    $produk = $this->Produk_model->getById($id);

    // hapus file jika lokal
    if(!empty($produk->gambar) && strpos($produk->gambar, 'uploads/') !== false){
        $path = FCPATH . str_replace(base_url(), '', $produk->gambar);

        if(file_exists($path)){
            unlink($path);
        }
    }

    $this->Produk_model->delete($id);

    $this->session->set_flashdata('success','Produk berhasil dihapus');
    redirect('produk');
}

public function search()
{
    $keyword = $this->input->get('q');
    $data = $this->Produk_model->getFilteredAjax($keyword);
    echo json_encode($data);
}

public function getProdukWithSatuan()
{
    return $this->db
        ->select('p.*, s.nama_satuan, s.konversi, s.harga')
        ->from('produk p')
        ->join('satuan_produk s','s.produk_id = p.id_produk')
        ->get()
        ->result();
}

}