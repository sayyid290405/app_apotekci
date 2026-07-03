<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produk_model');
        $this->load->library(['session', 'form_validation']);

        if(!$this->session->userdata('logged_in')){
            redirect('auth');
        }

        if($this->session->userdata('role_id') != 1){
            redirect('auth/blocked');
        }
    }

    // =========================
    // INDEX
    // =========================
    public function index()
    {
        $data['title']  = 'Data Produk';
        $data['produk'] = $this->Produk_model->getAllWithSatuan();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/index', $data);
        $this->load->view('templates/footer');
    }

    // =========================
    // FORM TAMBAH
    // =========================
    public function tambah()
    {
        $data['title']    = 'Tambah Produk';
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action']   = base_url('produk/simpan');
        $data['mode']     = 'tambah';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form', $data);
        $this->load->view('templates/footer');
    }

    // =========================
    // SIMPAN
    // =========================
    public function simpan()
    {
        $post = $this->input->post(NULL, TRUE);

        // ===== VALIDASI DASAR =====
        if(empty($post['nama_produk'])){
            $this->session->set_flashdata('error', 'Nama produk wajib diisi');
            redirect('produk/tambah');
        }

        // ===== CEK METODE GAMBAR =====
        $gambar = null;
        $mode_gambar = isset($post['mode_gambar']) ? $post['mode_gambar'] : 'url';

        if($mode_gambar == 'upload'){
            $upload = $this->uploadGambar();
            if($upload && !$upload['status']){
                $this->session->set_flashdata('error', $upload['error']);
                redirect('produk/tambah');
            }
            if($upload && $upload['status']){
                $gambar = $upload['file'];
            }
        } else {
            $gambar = !empty($post['gambar_url']) ? $post['gambar_url'] : null;
        }

        // ===== DATA SATUAN =====
        $satuan_names    = $this->input->post('nama_satuan');
        $satuan_konversi = $this->input->post('konversi');
        $satuan_harga    = $this->input->post('harga_satuan');

        // Pastikan data satuan ada
        if(empty($satuan_names)){
            $this->session->set_flashdata('error', 'Data satuan tidak lengkap');
            redirect('produk/tambah');
        }

        // Cari satuan dasar (konversi = 1)
        $satuan_dasar = 'Tablet';
        $harga_jual_utama = 0;
        
        foreach($satuan_names as $key => $nama){
            $nama = trim($nama);
            if(empty($nama)) continue;
            
            $konversi = isset($satuan_konversi[$key]) ? (int)$satuan_konversi[$key] : 1;
            if($konversi == 1){
                $satuan_dasar = $nama;
                $harga_jual_utama = isset($satuan_harga[$key]) ? (float)$satuan_harga[$key] : 0;
                break;
            }
        }

        // Jika tidak ada satuan dengan konversi 1, gunakan data pertama
        if(empty($harga_jual_utama) && !empty($satuan_harga)){
            $harga_jual_utama = (float)$satuan_harga[0];
        }

        // ===== DATA PRODUK =====
        $data_produk = array(
            'nama_produk'        => $post['nama_produk'],
            'kategori_id'        => !empty($post['kategori_id']) ? $post['kategori_id'] : null,
            'supplier_id'        => !empty($post['supplier_id']) ? $post['supplier_id'] : null,
            'harga_beli'         => !empty($post['harga_beli']) ? (int)$post['harga_beli'] : 0,
            'harga_jual'         => !empty($post['harga_jual']) ? (int)$post['harga_jual'] : (int)$harga_jual_utama,
            'stok'               => !empty($post['stok']) ? (int)$post['stok'] : 0,
            'stok_minimal'       => !empty($post['stok_minimal']) ? (int)$post['stok_minimal'] : 5,
            'tanggal_kadaluarsa' => !empty($post['tanggal_kadaluarsa']) ? $post['tanggal_kadaluarsa'] : null,
            'gambar'             => $gambar,
            'satuan_dasar'       => $satuan_dasar,
            'isi_per_unit'       => !empty($post['isi_per_unit']) ? (int)$post['isi_per_unit'] : 1
        );

        $this->db->trans_start();

        // ===== INSERT PRODUK =====
        $produk_id = $this->Produk_model->insert($data_produk);

        if(!$produk_id){
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Gagal menyimpan produk');
            redirect('produk/tambah');
        }

        // ===== INSERT SATUAN =====
        if(!empty($satuan_names)){
            foreach ($satuan_names as $key => $nama) {
                $nama = trim($nama);
                if(empty($nama)) continue;

                $konversi = isset($satuan_konversi[$key]) ? (int)$satuan_konversi[$key] : 1;
                $harga = isset($satuan_harga[$key]) ? (float)$satuan_harga[$key] : 0;

                // Jika konversi 1, gunakan harga_jual dari produk
                if($konversi == 1 && !empty($post['harga_jual'])){
                    $harga = (float)$post['harga_jual'];
                }

                // Pastikan harga tidak kosong
                if(empty($harga) && $harga !== 0){
                    $harga = 0;
                }

                $this->db->insert('satuan_produk', array(
                    'produk_id'   => $produk_id,
                    'nama_satuan' => $nama,
                    'konversi'    => $konversi,
                    'harga'       => $harga
                ));
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menyimpan produk');
            redirect('produk/tambah');
        }

        $this->session->set_flashdata('success', 'Produk berhasil ditambahkan');
        redirect('produk');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $data['title']    = 'Edit Produk';
        $data['produk']   = $this->Produk_model->getById($id);
        
        if(!$data['produk']){
            $this->session->set_flashdata('error', 'Produk tidak ditemukan');
            redirect('produk');
        }
        
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['satuan']   = $this->db->get_where('satuan_produk', array('produk_id' => $id))->result();
        $data['action']   = base_url('produk/update/'.$id);
        $data['mode']     = 'edit';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form', $data);
        $this->load->view('templates/footer');
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id)
    {
        $post = $this->input->post(NULL, TRUE);
        $produk_lama = $this->Produk_model->getById($id);

        if(!$produk_lama){
            $this->session->set_flashdata('error', 'Produk tidak ditemukan');
            redirect('produk');
        }

        // ===== CEK METODE GAMBAR =====
        $gambar = $produk_lama->gambar;
        $mode_gambar = isset($post['mode_gambar']) ? $post['mode_gambar'] : 'url';

        if($mode_gambar == 'upload'){
            $upload = $this->uploadGambar();
            if($upload && !$upload['status']){
                $this->session->set_flashdata('error', $upload['error']);
                redirect('produk/edit/'.$id);
            }
            if($upload && $upload['status']){
                $this->_hapusGambarLama($produk_lama->gambar);
                $gambar = $upload['file'];
            }
        } elseif($mode_gambar == 'url') {
            if(!empty($post['gambar_url'])){
                $this->_hapusGambarLama($produk_lama->gambar);
                $gambar = $post['gambar_url'];
            } else {
                $gambar = $produk_lama->gambar;
            }
        } elseif($mode_gambar == 'remove') {
            $this->_hapusGambarLama($produk_lama->gambar);
            $gambar = null;
        }

        // ===== DATA SATUAN =====
        $satuan_names    = $this->input->post('nama_satuan');
        $satuan_konversi = $this->input->post('konversi');
        $satuan_harga    = $this->input->post('harga_satuan');

        // Cari satuan dasar
        $satuan_dasar = 'Tablet';
        $harga_jual_utama = 0;
        if(!empty($satuan_names)){
            foreach($satuan_names as $key => $nama){
                $nama = trim($nama);
                if(empty($nama)) continue;
                
                $konversi = isset($satuan_konversi[$key]) ? (int)$satuan_konversi[$key] : 1;
                if($konversi == 1){
                    $satuan_dasar = $nama;
                    $harga_jual_utama = isset($satuan_harga[$key]) ? (float)$satuan_harga[$key] : 0;
                    break;
                }
            }
        }

        $data_produk = array(
            'nama_produk'        => $post['nama_produk'],
            'kategori_id'        => !empty($post['kategori_id']) ? $post['kategori_id'] : null,
            'supplier_id'        => !empty($post['supplier_id']) ? $post['supplier_id'] : null,
            'harga_beli'         => !empty($post['harga_beli']) ? (int)$post['harga_beli'] : 0,
            'harga_jual'         => !empty($post['harga_jual']) ? (int)$post['harga_jual'] : (int)$harga_jual_utama,
            'stok'               => !empty($post['stok']) ? (int)$post['stok'] : 0,
            'stok_minimal'       => !empty($post['stok_minimal']) ? (int)$post['stok_minimal'] : 5,
            'tanggal_kadaluarsa' => !empty($post['tanggal_kadaluarsa']) ? $post['tanggal_kadaluarsa'] : null,
            'gambar'             => $gambar,
            'satuan_dasar'       => $satuan_dasar,
            'isi_per_unit'       => !empty($post['isi_per_unit']) ? (int)$post['isi_per_unit'] : 1
        );

        $this->db->trans_start();

        // ===== UPDATE PRODUK =====
        $this->Produk_model->update($id, $data_produk);

        // ===== RESET SATUAN =====
        $this->db->delete('satuan_produk', array('produk_id' => $id));

        // ===== INSERT ULANG SATUAN =====
        if(!empty($satuan_names)){
            foreach ($satuan_names as $key => $nama) {
                $nama = trim($nama);
                if(empty($nama)) continue;

                $konversi = isset($satuan_konversi[$key]) ? (int)$satuan_konversi[$key] : 1;
                $harga = isset($satuan_harga[$key]) ? (float)$satuan_harga[$key] : 0;

                if($konversi == 1 && !empty($post['harga_jual'])){
                    $harga = (float)$post['harga_jual'];
                }

                if(empty($harga) && $harga !== 0){
                    $harga = 0;
                }

                $this->db->insert('satuan_produk', array(
                    'produk_id'   => $id,
                    'nama_satuan' => $nama,
                    'konversi'    => $konversi,
                    'harga'       => $harga
                ));
            }
        }

        $this->db->trans_complete();

        $this->session->set_flashdata(
            $this->db->trans_status() ? 'success' : 'error',
            $this->db->trans_status() ? 'Produk berhasil diperbarui' : 'Gagal update produk'
        );

        redirect('produk');
    }

    // =========================
    // HAPUS
    // =========================
    public function hapus($id)
    {
        // Cek apakah produk dipakai di pembelian
        $dipakai = $this->db
            ->where('produk_id', $id)
            ->count_all_results('detail_pembelian');

        if($dipakai > 0){
            $this->session->set_flashdata('error', 
                'Produk tidak bisa dihapus karena sudah digunakan dalam transaksi pembelian!'
            );
            redirect('produk');
            return;
        }

        // Cek di detail_pesanan
        $dipesan = $this->db
            ->where('produk_id', $id)
            ->count_all_results('detail_pesanan');

        if($dipesan > 0){
            $this->session->set_flashdata('error', 
                'Produk tidak bisa dihapus karena sudah digunakan dalam transaksi penjualan!'
            );
            redirect('produk');
            return;
        }

        $produk = $this->Produk_model->getById($id);

        if($produk){
            $this->_hapusGambarLama($produk->gambar);

            $this->db->trans_start();
            $this->db->delete('satuan_produk', array('produk_id' => $id));
            $this->Produk_model->delete($id);
            $this->db->trans_complete();
        }

        $this->session->set_flashdata('success', 'Produk berhasil dihapus');
        redirect('produk');
    }

    // =========================
    // UPLOAD GAMBAR
    // =========================
    private function uploadGambar()
    {
        if(empty($_FILES['gambar_file']['name'])) return null;

        if(!is_dir('./uploads/')){
            mkdir('./uploads/', 0777, true);
        }

        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp|gif';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('gambar_file')){
            return array(
                'status' => false,
                'error'  => strip_tags($this->upload->display_errors())
            );
        }

        return array(
            'status' => true,
            'file'   => $this->upload->data('file_name')
        );
    }

    // =========================
    // HAPUS GAMBAR LAMA
    // =========================
    private function _hapusGambarLama($gambar)
    {
        if(empty($gambar)) return;

        if(filter_var($gambar, FILTER_VALIDATE_URL)){
            return;
        }

        $path = FCPATH . 'uploads/' . $gambar;

        if(file_exists($path)){
            unlink($path);
        }
    }

    // =========================
    // SEARCH
    // =========================
    public function search()
    {
        $keyword = $this->input->get('q');
        $data = $this->Produk_model->searchProduk($keyword);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // =========================
    // GET SATUAN
    // =========================
    public function getSatuan($produk_id)
    {
        $data = $this->db
            ->where('produk_id', $produk_id)
            ->order_by('konversi', 'ASC')
            ->get('satuan_produk')
            ->result();

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}