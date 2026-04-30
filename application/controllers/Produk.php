<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
<<<<<<< HEAD

        $this->load->model('Produk_model');
        $this->load->library(['session','form_validation']);

        // cek login
=======
        $this->load->model('Produk_model');
        $this->load->library(['session', 'form_validation']);

>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
        if(!$this->session->userdata('logged_in')){
            redirect('auth');
        }

<<<<<<< HEAD
        // hanya admin
=======
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
        if($this->session->userdata('role_id') != 1){
            redirect('auth/blocked');
        }
    }

    // =========================
<<<<<<< HEAD
    // LIST PRODUK
    // =========================
    public function index()
    {
        $data['title'] = 'Data Produk';
        $data['produk'] = $this->Produk_model->getAll();
        $data['js'] = 'produk.js';
=======
    // INDEX
    // =========================
    public function index()
    {
        $data['title']  = 'Data Produk';
        $data['produk'] = $this->Produk_model->getAllWithSatuan();
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/index', $data);
        $this->load->view('templates/footer');
    }

<<<<<<< HEAD
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

=======
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    // =========================
    // FORM TAMBAH
    // =========================
    public function tambah()
    {
<<<<<<< HEAD
        $data['title'] = 'Tambah Produk';
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action'] = base_url('produk/simpan');

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form',$data);
=======
        $data['title']    = 'Tambah Produk';
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action']   = base_url('produk/simpan');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form', $data);
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
        $this->load->view('templates/footer');
    }

    // =========================
<<<<<<< HEAD
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

=======
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

>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
<<<<<<< HEAD
        $data['title'] = 'Edit Produk';
        $data['produk'] = $this->Produk_model->getById($id);
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['action'] = base_url('produk/update/'.$id);

        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form',$data);
=======
        $data['title']    = 'Edit Produk';
        $data['produk']   = $this->Produk_model->getById($id);
        $data['kategori'] = $this->Produk_model->getKategori();
        $data['supplier'] = $this->Produk_model->getSupplier();
        $data['satuan']   = $this->db->get_where('satuan_produk', ['produk_id' => $id])->result();
        $data['action']   = base_url('produk/update/'.$id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('produk/form', $data);
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
        $this->load->view('templates/footer');
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id)
<<<<<<< HEAD
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

=======
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
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

    // =========================
    // HAPUS
    // =========================
    public function hapus($id)
{
<<<<<<< HEAD
    $produk = $this->Produk_model->getById($id);

    // hapus file jika lokal
    if(!empty($produk->gambar) && strpos($produk->gambar, 'uploads/') !== false){
        $path = FCPATH . str_replace(base_url(), '', $produk->gambar);
=======
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
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

        if(file_exists($path)){
            unlink($path);
        }
    }

<<<<<<< HEAD
    $this->Produk_model->delete($id);

    $this->session->set_flashdata('success','Produk berhasil dihapus');
    redirect('produk');
}

public function search()
{
    $keyword = $this->input->get('q');
    $data = $this->Produk_model->getFilteredAjax($keyword);
=======
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
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    echo json_encode($data);
}
}