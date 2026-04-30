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

        // ===== UPLOAD GAMBAR =====
        $upload = $this->uploadGambar();

        if($upload && !$upload['status']){
            $this->session->set_flashdata('error', $upload['error']);
            redirect('produk/tambah');
        }

        if($upload && $upload['status']){
            $gambar = $upload['file']; // hanya nama file
        } else {
            $gambar = $post['gambar_url'] ?? null;
        }

        // ===== DATA SATUAN =====
        $satuan_names    = $this->input->post('nama_satuan');
        $satuan_konversi = $this->input->post('konversi');
        $satuan_harga    = $this->input->post('harga_satuan');

        $harga_jual_utama = isset($satuan_harga[0]) ? $satuan_harga[0] : 0;
        $satuan_dasar     = isset($satuan_names[0]) ? $satuan_names[0] : 'unit';

        // ===== DATA PRODUK =====
        $data_produk = [
            'nama_produk'        => $post['nama_produk'],
            'kategori_id'        => $post['kategori_id'],
            'supplier_id'        => $post['supplier_id'],
            'harga_beli'         => $post['harga_beli'],
            'harga_jual'         => $harga_jual_utama,
            'stok'               => $post['stok'],
            'stok_minimal'       => $post['stok_minimal'],
            'tanggal_kadaluarsa' => $post['tanggal_kadaluarsa'],
            'gambar'             => $gambar,
            'satuan_dasar'       => $satuan_dasar
        ];

        $this->db->trans_start();

        // ===== INSERT PRODUK =====
        $produk_id = $this->Produk_model->insert($data_produk);

        // ===== INSERT SATUAN =====
        if(!empty($satuan_names)){
            foreach ($satuan_names as $key => $val) {

                if(empty($val)) continue;

                $this->db->insert('satuan_produk', [
                    'produk_id'   => $produk_id,
                    'nama_satuan' => $val,
                    'konversi'    => $satuan_konversi[$key] ?? 1,
                    'harga'       => $satuan_harga[$key] ?? 0
                ]);
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
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['satuan']   = $this->db->get_where('satuan_produk', ['produk_id' => $id])->result();
        $data['action']   = base_url('produk/update/'.$id);

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

        // ===== UPLOAD GAMBAR =====
        $upload = $this->uploadGambar();

        if($upload && !$upload['status']){
            $this->session->set_flashdata('error', $upload['error']);
            redirect('produk/edit/'.$id);
        }

        if($upload && $upload['status']){
            $this->_hapusGambarLama($produk_lama->gambar);
            $gambar = $upload['file'];
        } else {
            $gambar = !empty($post['gambar_url']) ? $post['gambar_url'] : $produk_lama->gambar;
        }

        // ===== DATA SATUAN =====
        $satuan_names    = $this->input->post('nama_satuan');
        $satuan_konversi = $this->input->post('konversi');
        $satuan_harga    = $this->input->post('harga_satuan');

        $data_produk = [
            'nama_produk'        => $post['nama_produk'],
            'kategori_id'        => $post['kategori_id'],
            'supplier_id'        => $post['supplier_id'],
            'harga_beli'         => $post['harga_beli'],
            'harga_jual'         => $satuan_harga[0] ?? 0,
            'stok'               => $post['stok'],
            'stok_minimal'       => $post['stok_minimal'],
            'tanggal_kadaluarsa' => $post['tanggal_kadaluarsa'],
            'gambar'             => $gambar,
            'satuan_dasar'       => $satuan_names[0] ?? 'unit'
        ];

        $this->db->trans_start();

        // ===== UPDATE PRODUK =====
        $this->Produk_model->update($id, $data_produk);

        // ===== RESET SATUAN =====
        $this->db->delete('satuan_produk', ['produk_id' => $id]);

        if(!empty($satuan_names)){
            foreach ($satuan_names as $key => $val) {

                if(empty($val)) continue;

                $this->db->insert('satuan_produk', [
                    'produk_id'   => $id,
                    'nama_satuan' => $val,
                    'konversi'    => $satuan_konversi[$key] ?? 1,
                    'harga'       => $satuan_harga[$key] ?? 0
                ]);
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
    // 🔍 cek apakah produk dipakai di pembelian
    $dipakai = $this->db
        ->where('produk_id', $id)
        ->count_all_results('detail_pembelian');

    if($dipakai > 0){
        // ❌ tidak boleh hapus
        $this->session->set_flashdata('error', 
            'Produk tidak bisa dihapus karena sudah digunakan dalam transaksi pembelian!'
        );
        redirect('produk');
        return;
    }

    // 🔥 lanjut hapus jika aman
    $produk = $this->Produk_model->getById($id);

    if($produk){
        $this->_hapusGambarLama($produk->gambar);

        $this->db->trans_start();

        $this->db->delete('satuan_produk', ['produk_id' => $id]);
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

        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|webp';
        $config['max_size']      = 2048;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('gambar_file')){
            return [
                'status' => false,
                'error'  => strip_tags($this->upload->display_errors())
            ];
        }

        return [
            'status' => true,
            'file'   => $this->upload->data('file_name')
        ];
    }

    // =========================
    // HAPUS GAMBAR LAMA
    // =========================
    private function _hapusGambarLama($gambar)
    {
        if(empty($gambar)) return;

        // skip kalau URL
        if(filter_var($gambar, FILTER_VALIDATE_URL)){
            return;
        }

        $path = FCPATH . 'uploads/' . $gambar;

        if(file_exists($path)){
            unlink($path);
        }
    }

    public function search()
{
    $keyword = $this->input->get('q');

    $data = $this->Produk_model->searchProduk($keyword);

    header('Content-Type: application/json');
    echo json_encode($data);
}

// =========================
// GET SATUAN PRODUK (API)
// =========================
public function getSatuan($produk_id)
{
    $data = $this->db
        ->where('produk_id', $produk_id)
        ->order_by('konversi', 'ASC') // satuan terkecil dulu
        ->get('satuan_produk')
        ->result();

    header('Content-Type: application/json');
    echo json_encode($data);
}
}