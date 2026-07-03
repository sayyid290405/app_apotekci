<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_supplier_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    /**
     * Filter Pembelian Berdasarkan Supplier
     * @param int $supplier_id ID supplier yang login
     * @param int|null $limit Batas data
     * @param string|null $tgl_awal Tanggal awal
     * @param string|null $tgl_akhir Tanggal akhir
     * @param string|null $search Kata kunci pencarian
     * @return array
     */
    public function filterPembelianBySupplier($supplier_id, $limit = null, $tgl_awal = null, $tgl_akhir = null, $search = null){
        
        $this->db->select('
            p.*,
            s.nama_supplier,
            u.nama as user_name,
            ap.nama as approved_by_name,
            COUNT(dp.id_detail) as total_item
        ');
        $this->db->from('pembelian p');
        $this->db->join('supplier s', 'p.supplier_id = s.id_supplier');
        $this->db->join('users u', 'p.user_id = u.id_user', 'left');
        $this->db->join('users ap', 'p.approved_by = ap.id_user', 'left');
        $this->db->join('detail_pembelian dp', 'p.id_pembelian = dp.pembelian_id', 'left');
        
        // Filter berdasarkan supplier
        $this->db->where('p.supplier_id', $supplier_id);
        
        // Filter tanggal
        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(p.tanggal) >=', $tgl_awal);
            $this->db->where('DATE(p.tanggal) <=', $tgl_akhir);
        } elseif($tgl_awal){
            $this->db->where('DATE(p.tanggal) >=', $tgl_awal);
        } elseif($tgl_akhir){
            $this->db->where('DATE(p.tanggal) <=', $tgl_akhir);
        }
        
        // Search by kode pembelian
        if($search){
            $this->db->group_start();
            $this->db->like('p.kode_pembelian', $search);
            $this->db->or_like('p.status', $search);
            $this->db->group_end();
        }
        
        $this->db->group_by('p.id_pembelian');
        $this->db->order_by('p.tanggal', 'DESC');
        
        if($limit){
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Get Detail Pembelian dengan produk
     * @param int $pembelian_id ID pembelian
     * @param int $supplier_id ID supplier untuk validasi
     * @return array
     */
    public function getDetailPembelianBySupplier($pembelian_id, $supplier_id){
        
        // Validasi bahwa pembelian milik supplier ini
        $this->db->select('p.id_pembelian');
        $this->db->from('pembelian p');
        $this->db->where('p.id_pembelian', $pembelian_id);
        $this->db->where('p.supplier_id', $supplier_id);
        $check = $this->db->get()->row();
        
        if(!$check){
            return [];
        }
        
        $this->db->select('
            dp.*,
            pr.nama_produk,
            pr.gambar,
            k.nama_kategori,
            sp.nama_satuan,
            sp.konversi as satuan_konversi
        ');
        $this->db->from('detail_pembelian dp');
        $this->db->join('produk pr', 'dp.produk_id = pr.id_produk');
        $this->db->join('kategori k', 'pr.kategori_id = k.id_kategori', 'left');
        $this->db->join('satuan_produk sp', 'pr.id_produk = sp.produk_id AND sp.konversi = 1', 'left');
        $this->db->where('dp.pembelian_id', $pembelian_id);
        
        return $this->db->get()->result();
    }

    /**
     * Get data pembelian untuk detail
     * @param int $pembelian_id ID pembelian
     * @param int $supplier_id ID supplier
     * @return object|null
     */
    public function getPembelianById($pembelian_id, $supplier_id){
        $this->db->select('
            p.*,
            s.nama_supplier,
            s.alamat,
            s.kontak,
            s.legalitas,
            u.nama as user_name,
            ap.nama as approved_by_name
        ');
        $this->db->from('pembelian p');
        $this->db->join('supplier s', 'p.supplier_id = s.id_supplier');
        $this->db->join('users u', 'p.user_id = u.id_user', 'left');
        $this->db->join('users ap', 'p.approved_by = ap.id_user', 'left');
        $this->db->where('p.id_pembelian', $pembelian_id);
        $this->db->where('p.supplier_id', $supplier_id);
        
        return $this->db->get()->row();
    }

    /**
     * Get ringkasan statistik supplier
     * @param int $supplier_id ID supplier
     * @param string|null $tgl_awal Tanggal awal
     * @param string|null $tgl_akhir Tanggal akhir
     * @return object
     */
    public function getSupplierStats($supplier_id, $tgl_awal = null, $tgl_akhir = null){
        
        // Total pembelian
        $this->db->select('COALESCE(SUM(total), 0) as total_pembelian');
        $this->db->from('pembelian');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('status', 'selesai');
        
        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(tanggal) >=', $tgl_awal);
            $this->db->where('DATE(tanggal) <=', $tgl_akhir);
        }
        
        $total = $this->db->get()->row();
        
        // Jumlah transaksi
        $this->db->select('COUNT(*) as jumlah_transaksi');
        $this->db->from('pembelian');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('status !=', 'ditolak');
        
        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(tanggal) >=', $tgl_awal);
            $this->db->where('DATE(tanggal) <=', $tgl_akhir);
        }
        
        $jumlah = $this->db->get()->row();
        
        // Produk yang sering dibeli dari supplier ini
        $this->db->select('
            pr.nama_produk,
            SUM(dp.jumlah) as total_dibeli,
            COUNT(DISTINCT dp.pembelian_id) as frekuensi
        ');
        $this->db->from('detail_pembelian dp');
        $this->db->join('pembelian p', 'dp.pembelian_id = p.id_pembelian');
        $this->db->join('produk pr', 'dp.produk_id = pr.id_produk');
        $this->db->where('p.supplier_id', $supplier_id);
        $this->db->where('p.status', 'selesai');
        
        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(p.tanggal) >=', $tgl_awal);
            $this->db->where('DATE(p.tanggal) <=', $tgl_akhir);
        }
        
        $this->db->group_by('dp.produk_id');
        $this->db->order_by('total_dibeli', 'DESC');
        $this->db->limit(10);
        
        $produk_terlaris = $this->db->get()->result();
        
        return (object)[
            'total_pembelian' => $total->total_pembelian,
            'jumlah_transaksi' => $jumlah->jumlah_transaksi,
            'produk_terlaris' => $produk_terlaris
        ];
    }

    /**
     * Get status pembelian berdasarkan supplier
     * @param int $supplier_id ID supplier
     * @return array
     */
    public function getStatusCount($supplier_id){
        $this->db->select('status, COUNT(*) as jumlah');
        $this->db->from('pembelian');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->group_by('status');
        
        $results = $this->db->get()->result();
        
        $statusCount = [
            'menunggu' => 0,
            'diterima' => 0,
            'disetujui' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
            'dibatalkan' => 0
        ];
        
        foreach($results as $row){
            if(isset($statusCount[$row->status])){
                $statusCount[$row->status] = $row->jumlah;
            }
        }
        
        return $statusCount;
    }

    /**
     * Export Excel untuk Supplier
     * @param int $supplier_id ID supplier
     * @param array $params Parameter filter
     * @return array Data untuk export
     */
    public function getExportData($supplier_id, $params){
        $limit = ($params['limit'] == 'all') ? null : $params['limit'];
        
        return $this->filterPembelianBySupplier(
            $supplier_id,
            $limit,
            $params['tgl_awal'],
            $params['tgl_akhir'],
            $params['search']
        );
    }
}