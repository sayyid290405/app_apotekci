<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_manajer_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function grafik_penjualan()
    {
        $this->db->select('DATE(tanggal_pesan) as tgl, SUM(total_harga) as total');
        $this->db->from('pesanan');
        $this->db->where('status', 'selesai'); // optional (biar valid)
        $this->db->group_by('DATE(tanggal_pesan)');
        $this->db->order_by('tgl', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_obat()
    {
        $this->db->select('produk.*, supplier.nama_supplier');
        $this->db->from('produk');
        $this->db->join('supplier', 'supplier.id_supplier = produk.supplier_id', 'left');

        return $this->db->get()->result();
    }

    // total stok semua produk
    public function get_total_stok()
    {
        $this->db->select_sum('stok');
        $query = $this->db->get('produk');
        return $query->row()->stok;
    }

    // stok minim (<10)
    public function get_stok_minim_obat()
    {
        $this->db->where('stok <', 6);
        return $this->db->get('produk')->result();
    }

    // kadaluarsa 30 hari lagi
    public function get_kadaluarsa()
    {
        $this->db->where('tanggal_kadaluarsa <=', date('Y-m-d', strtotime('+30 days')));
        return $this->db->get('produk')->result();
    }
    
    public function get_keuntungan()
    {
    }

}