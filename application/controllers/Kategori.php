<?php
class Kategori extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_model');
        $this->load->library('session');
    }

    public function index()
    {
        $data['js'] = 'kategori.js';
        $data['kategori'] = $this->Kategori_model->getAll();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['action'] = base_url('kategori/simpan');
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/form', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $data = $this->input->post(NULL, TRUE);

        // Validasi nama kategori tidak boleh kosong
        if(empty($data['nama_kategori'])){
            $this->session->set_flashdata('error', 'Nama kategori wajib diisi!');
            redirect('kategori/tambah');
        }

        $this->Kategori_model->insert($data);

        $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan');
        redirect('kategori');
    }

    public function edit($id)
    {
        $data['kategori'] = $this->Kategori_model->getById($id);
        
        if(!$data['kategori']){
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan!');
            redirect('kategori');
        }
        
        $data['action'] = base_url('kategori/update/'.$id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kategori/form', $data);
        $this->load->view('templates/footer');
    }

    public function update($id)
    {
        $data = $this->input->post(NULL, TRUE);

        // Validasi nama kategori tidak boleh kosong
        if(empty($data['nama_kategori'])){
            $this->session->set_flashdata('error', 'Nama kategori wajib diisi!');
            redirect('kategori/edit/'.$id);
        }

        $this->Kategori_model->update($id, $data);

        $this->session->set_flashdata('success', 'Kategori berhasil diupdate');
        redirect('kategori');
    }

    // ==================== HAPUS DENGAN LOCK SYSTEM ====================
    public function hapus($id)
    {
        // Cek apakah kategori ada
        $kategori = $this->Kategori_model->getById($id);
        if(!$kategori){
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan!');
            redirect('kategori');
            return;
        }

        // 🔒 CEK APAKAH KATEGORI DIGUNAKAN DI PRODUK
        if($this->Kategori_model->isUsed($id)){
            $jumlah = $this->Kategori_model->countUsed($id);
            $produk_list = $this->Kategori_model->getProductsByCategory($id);
            
            // Buat daftar produk
            $list_produk = '';
            foreach($produk_list as $p){
                $list_produk .= '- ' . $p->nama_produk . "\n";
            }
            
            $this->session->set_flashdata('error', 
                '❌ Kategori "' . $kategori->nama_kategori . '" TIDAK BISA DIHAPUS!<br><br>' .
                'Kategori ini masih digunakan oleh <strong>' . $jumlah . '</strong> produk:<br><br>' .
                nl2br($list_produk) . '<br>' .
                '⚠️ Hapus atau ubah kategori produk tersebut terlebih dahulu!'
            );
            redirect('kategori');
            return;
        }

        // Jika tidak digunakan, hapus
        $this->Kategori_model->delete($id);

        $this->session->set_flashdata('success', '✅ Kategori "' . $kategori->nama_kategori . '" berhasil dihapus');
        redirect('kategori');
    }

    public function search()
    {
        $keyword = $this->input->get('q');
        $data = $this->Kategori_model->search($keyword);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // ==================== CEK STATUS KATEGORI (AJAX) ====================
    public function cek_status($id)
    {
        $isUsed = $this->Kategori_model->isUsed($id);
        $count = $this->Kategori_model->countUsed($id);
        $produk = $this->Kategori_model->getProductsByCategory($id);
        
        $response = [
            'status' => $isUsed ? 'locked' : 'available',
            'jumlah_produk' => $count,
            'produk' => $produk
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}