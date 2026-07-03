<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resep extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Produk_model');
    }

    public function index()
    {
        $data['produk'] = $this->Produk_model->getAllWithSatuan();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('resep/index', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        // Set header untuk AJAX response
        header('Content-Type: application/json');

        try {
            // Ambil data dari form
            $produk = json_decode($this->input->post('produk'), true);
            $metode_bayar = $this->input->post('metode_bayar');
            $nama_pasien = $this->input->post('nama_pasien');
            $nama_dokter = $this->input->post('nama_dokter');
            $bayar = $this->input->post('bayar');
            $subtotal = $this->input->post('subtotal');
            $diskon = $this->input->post('diskon');
            $ppn = $this->input->post('ppn');
            $total_harga = $this->input->post('total_harga');

            // Validasi dasar
            if (!$produk || !is_array($produk) || count($produk) == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Keranjang belanja kosong']);
                return;
            }

            if (!$nama_pasien) {
                echo json_encode(['status' => 'error', 'message' => 'Nama pasien harus diisi']);
                return;
            }

            // Start transaction
            $this->db->trans_start();

            // ========== 1. UPLOAD GAMBAR RESEP ==========
            $gambar_resep = null;
            if (isset($_FILES['gambar_resep']) && $_FILES['gambar_resep']['error'] == 0) {
                $config['upload_path'] = './uploads/resep/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = true;

                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('gambar_resep')) {
                    $upload_data = $this->upload->data();
                    $gambar_resep = 'uploads/resep/' . $upload_data['file_name'];
                } else {
                    throw new Exception('Upload gambar resep gagal: ' . strip_tags($this->upload->display_errors()));
                }
            } else {
                throw new Exception('Foto resep wajib diupload!');
            }

            // ========== 2. UPLOAD BUKTI TRANSFER (jika transfer) ==========
            $bukti_pembayaran = null;
            if ($metode_bayar == 'transfer') {
                if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
                    $config['upload_path'] = './uploads/bukti_transfer/';
                    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                    $config['max_size'] = 5120;
                    $config['encrypt_name'] = true;

                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, true);
                    }

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('bukti_pembayaran')) {
                        $upload_data = $this->upload->data();
                        $bukti_pembayaran = 'uploads/bukti_transfer/' . $upload_data['file_name'];
                    } else {
                        throw new Exception('Upload bukti transfer gagal: ' . strip_tags($this->upload->display_errors()));
                    }
                } else {
                    throw new Exception('Bukti pembayaran transfer wajib diupload!');
                }
            }

            // ========== 3. GENERATE KODE RESEP ==========
            $kode_resep = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // ========== 4. INSERT KE TABEL RESEP ==========
            $this->db->insert('resep', [
                'kode_resep'   => $kode_resep,
                'nama_pasien'  => $nama_pasien,
                'nama_dokter'  => $nama_dokter,
                'tanggal'      => date('Y-m-d'),
                'gambar_resep' => $gambar_resep,
                'status'       => 'verified' // Langsung verified karena udah upload bukti
            ]);

            $resep_id = $this->db->insert_id();

            // ========== 5. INSERT DETAIL RESEP ==========
            foreach ($produk as $item) {
                // Ambil harga jual dari database untuk keamanan
                $produk_db = $this->db->get_where('produk', ['id_produk' => $item['id']])->row();
                $harga_jual = $produk_db ? $produk_db->harga_jual : $item['harga'];

                $this->db->insert('detail_resep', [
                    'resep_id'  => $resep_id,
                    'produk_id' => $item['id'],
                    'jumlah'    => $item['qty'],
                    'dosis'     => $item['dosis'] ?? null,
                    'satuan'    => $item['satuan'] ?? '-',
                    'harga'     => $harga_jual,
                    'catatan'   => null
                ]);

                // Kurangi stok produk
                $this->db->set('stok', 'stok - ' . ($item['qty'] * ($item['konversi'] ?? 1)), FALSE);
                $this->db->where('id_produk', $item['id']);
                $this->db->update('produk');
            }

            // ========== 6. INSERT KE TABEL PESANAN (Transaksi) ==========
            $kembalian = ($metode_bayar == 'tunai') ? ($bayar - $total_harga) : 0;
            
            $this->db->insert('pesanan', [
                'user_id'        => $this->session->userdata('id_user') ?? 1,
                'tanggal_pesan'  => date('Y-m-d H:i:s'),
                'subtotal'       => $subtotal,
                'diskon'         => $diskon ?? 0,
                'ppn'            => $ppn ?? 0,
                'total_harga'    => $total_harga,
                'bayar'          => ($metode_bayar == 'tunai') ? $bayar : $total_harga,
                'kembalian'      => $kembalian,
                'tipe_transaksi' => 'resep',
                'resep_id'       => $resep_id,
                'metode_bayar'   => $metode_bayar,
                'status'         => 'selesai',
                'bukti_qris'     => $bukti_pembayaran
            ]);

            $pesanan_id = $this->db->insert_id();

            // ========== 7. INSERT DETAIL PESANAN ==========
            foreach ($produk as $item) {
                $this->db->insert('detail_pesanan', [
                    'pesanan_id' => $pesanan_id,
                    'produk_id'  => $item['id'],
                    'jumlah'     => $item['qty'],
                    'harga'      => $item['harga'],
                    'subtotal'   => $item['qty'] * $item['harga'],
                    'satuan'     => $item['satuan'] ?? 'unit',
                    'konversi'   => $item['konversi'] ?? 1
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan transaksi ke database');
            }

            // Response sukses
            echo json_encode([
                'status'   => 'success',
                'message'  => 'Transaksi berhasil disimpan',
                'redirect' => site_url('kasir/struk/' . $pesanan_id)
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function searchProduk()
    {
        $q = $this->input->get('q');
        
        $data = $this->db
            ->select('id_produk as id, nama_produk as text, harga_jual as harga')
            ->like('nama_produk', $q)
            ->limit(10)
            ->get('produk')
            ->result();
        
        echo json_encode($data);
    }

    public function detail($id)
    {
        $data['resep'] = $this->db->where('id_resep', $id)->get('resep')->row();
        $data['detail'] = $this->db
            ->select('detail_resep.*, produk.nama_produk')
            ->join('produk', 'produk.id_produk = detail_resep.produk_id')
            ->where('resep_id', $id)
            ->get('detail_resep')
            ->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('resep/detail', $data);
        $this->load->view('templates/footer');
    }
}