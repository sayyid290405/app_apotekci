<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    

    public function totalProduk()
    {
        return $this->db->count_all('produk');
    }

    // =========================
    // TOTAL PELANGGAN (ROLE USER)
    // =========================
    public function totalPelanggan()
    {
        return $this->db->join('roles','roles.id_role = users.role_id')
                        ->where('roles.nama_role','user')
                        ->count_all_results('users');
    }

    // =========================
    // PRODUK KADALUARSA
    // =========================
    public function produkKadaluarsa()
    {
        return $this->db->where('tanggal_kadaluarsa <=', date('Y-m-d'))
                        ->count_all_results('produk');
    }

    // =========================
// STOK RENDAH (FIX)
// =========================
public function stokRendah()
{
    return $this->db->where('stok <= stok_minimal', null, false)
                    ->count_all_results('produk');
}

    // =========================
    // PENJUALAN HARI INI
    // =========================
    public function penjualanHariIni()
    {
        $this->db->select_sum('total_harga');
        $this->db->from('pesanan');
        $this->db->where('DATE(tanggal_pesan)', date('Y-m-d'));
        $this->db->where('status','selesai');

        $result = $this->db->get()->row();

        return $result->total_harga ?? 0;
    }

    // =========================
    // TOTAL TRANSAKSI HARI INI
    // =========================
    public function totalTransaksiHariIni()
    {
        return $this->db->where('DATE(tanggal_pesan)', date('Y-m-d'))
                        ->where('status','selesai')
                        ->count_all_results('pesanan');
    }

    // =========================
// GRAFIK PENJUALAN (7 HARI)
// =========================
public function grafikPenjualan()
{
    return $this->db->query("
        SELECT 
            DATE(tanggal_pesan) as tanggal,
            SUM(total_harga) as total
        FROM pesanan
        WHERE status='selesai'
        AND tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(tanggal_pesan)
        ORDER BY tanggal ASC
    ")->result();
}

    // =========================
    // KEUNTUNGAN HARI INI (ESTIMASI)
    // =========================
    public function keuntunganHariIni()
    {
        $this->db->select('SUM(dp.subtotal - (p.harga_beli * dp.jumlah)) as keuntungan');
        $this->db->from('detail_pesanan dp');
        $this->db->join('produk p','p.id_produk = dp.produk_id');
        $this->db->join('pesanan ps','ps.id_pesanan = dp.pesanan_id');
        $this->db->where('DATE(ps.tanggal_pesan)', date('Y-m-d'));
        $this->db->where('ps.status','selesai');

        $result = $this->db->get()->row();

        return $result->keuntungan ?? 0;
    }

    // =========================
// PRODUK TERLARIS (FIX)
// =========================
public function produkTerlaris($limit = 5)
{
    return $this->db->query("
        SELECT 
            p.nama_produk,
            SUM(dp.jumlah) as total_terjual
        FROM detail_pesanan dp
        JOIN produk p ON p.id_produk = dp.produk_id
        JOIN pesanan ps ON ps.id_pesanan = dp.pesanan_id
        WHERE ps.status = 'selesai'
        GROUP BY dp.produk_id
        ORDER BY total_terjual DESC
        LIMIT $limit
    ")->result();
}

}