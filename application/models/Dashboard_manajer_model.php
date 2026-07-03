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
        $this->db->where('status', 'selesai');
        $this->db->group_by('DATE(tanggal_pesan)');
        $this->db->order_by('tgl', 'ASC');

        return $this->db->get()->result();
    }

    // ✅ AMBIL PRODUK DENGAN SUPPLIER (tanpa kategori)
    public function get_all_obat()
    {
        $this->db->select('produk.*, supplier.nama_supplier');
        $this->db->from('produk');
        $this->db->join('supplier', 'supplier.id_supplier = produk.supplier_id', 'left');
        $this->db->order_by('produk.nama_produk', 'ASC');

        return $this->db->get()->result();
    }

    // ✅ AMBIL PRODUK DENGAN KATEGORI DAN SUPPLIER (LENGKAP)
    public function get_produk_with_kategori()
    {
        $this->db->select('produk.*, kategori.nama_kategori, supplier.nama_supplier');
        $this->db->from('produk');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id', 'left');
        $this->db->join('supplier', 'supplier.id_supplier = produk.supplier_id', 'left');
        $this->db->order_by('produk.nama_produk', 'ASC');
        
        return $this->db->get()->result();
    }

    // total stok semua produk
    public function get_total_stok()
    {
        $this->db->select_sum('stok');
        $query = $this->db->get('produk');
        return $query->row()->stok ?? 0;
    }

    // stok minim (stok <= stok_minimal)
    public function get_stok_minim_obat()
    {
        $this->db->select('produk.*, supplier.nama_supplier, kategori.nama_kategori');
        $this->db->from('produk');
        $this->db->join('supplier', 'supplier.id_supplier = produk.supplier_id', 'left');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id', 'left');
        $this->db->where('produk.stok <=', 'produk.stok_minimal', false);
        $this->db->order_by('produk.stok', 'ASC');
        
        return $this->db->get()->result();
    }

    // kadaluarsa 30 hari ke depan
    public function get_kadaluarsa()
    {
        $batas = date('Y-m-d', strtotime('+30 days'));
        
        $this->db->select('produk.*, supplier.nama_supplier, kategori.nama_kategori');
        $this->db->from('produk');
        $this->db->join('supplier', 'supplier.id_supplier = produk.supplier_id', 'left');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id', 'left');
        $this->db->where('produk.tanggal_kadaluarsa IS NOT NULL');
        $this->db->where('produk.tanggal_kadaluarsa <=', $batas);
        $this->db->order_by('produk.tanggal_kadaluarsa', 'ASC');
        
        return $this->db->get()->result();
    }
    
    // statistik per kategori
    public function get_statistik_kategori()
    {
        $this->db->select('
            kategori.id_kategori,
            kategori.nama_kategori,
            COUNT(produk.id_produk) as total_produk,
            SUM(produk.stok) as total_stok
        ');
        $this->db->from('kategori');
        $this->db->join('produk', 'produk.kategori_id = kategori.id_kategori', 'left');
        $this->db->group_by('kategori.id_kategori');
        $this->db->order_by('kategori.nama_kategori', 'ASC');
        
        return $this->db->get()->result();
    }
}
?>