<?php
class Laporan_model extends CI_Model {

    // ================= PENJUALAN =================
    public function getPenjualan($limit = 10){

        $this->db->select('pesanan.*, users.nama as kasir');
        $this->db->from('pesanan');
        $this->db->join('users', 'users.id_user = pesanan.user_id', 'left');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->order_by('pesanan.id_pesanan','DESC');

        if($limit !== null){
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    // ================= PEMBELIAN =================
    public function getPembelian($limit = 10){

        $this->db->select('pembelian.*, users.nama as user');
        $this->db->from('pembelian');
        $this->db->join('users', 'users.id_user = pembelian.user_id', 'left');
        $this->db->order_by('pembelian.id_pembelian','DESC');

        if($limit !== null){
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    // ================= DETAIL PEMBELIAN =================
    public function getDetailPembelian($id){
        $this->db->select('detail_pembelian.*, produk.nama_produk');
        $this->db->from('detail_pembelian');
        $this->db->join('produk','produk.id_produk = detail_pembelian.produk_id');
        $this->db->where('pembelian_id',$id);
        return $this->db->get()->result();
    }

    // ================= TOTAL HARI INI =================
    public function getTotalHariIni(){

        $this->db->select_sum('total_harga');
        $this->db->where('DATE(tanggal_pesan)', date('Y-m-d'));
        $this->db->where('status', 'selesai');

        $result = $this->db->get('pesanan')->row();

        return $result->total_harga ?? 0;
    }

    // ================= OBAT TERLARIS =================
    public function getObatTerlaris(){

        $this->db->select('produk.nama_produk, SUM(detail_pesanan.jumlah) as total');
        $this->db->from('detail_pesanan');
        $this->db->join('produk', 'produk.id_produk = detail_pesanan.produk_id');
        $this->db->join('pesanan', 'pesanan.id_pesanan = detail_pesanan.pesanan_id');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->group_by('detail_pesanan.produk_id');
        $this->db->order_by('total','DESC');
        $this->db->limit(5);

        return $this->db->get()->result();
    }

    // ================= FILTER PENJUALAN =================
    public function filterPenjualan($limit, $tgl_awal, $tgl_akhir, $search){

        $this->db->select('pesanan.*, users.nama as kasir, qris.id_qris, pesanan.bukti_qris');
        $this->db->from('pesanan');
        $this->db->join('users', 'users.id_user = pesanan.user_id', 'left');
        $this->db->join('qris', 'qris.pesanan_id = pesanan.id_pesanan', 'left');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->where('pesanan.tipe_transaksi', 'non_resep'); // 🔥 Hanya non resep

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);

            $this->db->group_start();
            if(!empty($cleanNumber)){
                $this->db->or_like('pesanan.id_pesanan', $cleanNumber);
            }
            $this->db->or_like('users.nama', $search);
            $this->db->group_end();
        } else {
            if($tgl_awal){
                $this->db->where('DATE(pesanan.tanggal_pesan) >=', $tgl_awal);
            }
            if($tgl_akhir){
                $this->db->where('DATE(pesanan.tanggal_pesan) <=', $tgl_akhir);
            }
        }

        $this->db->order_by('pesanan.id_pesanan', 'DESC');

        if(!empty($limit)){
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    // ================= FILTER PEMBELIAN =================
    public function filterPembelian($limit, $tgl_awal, $tgl_akhir, $search){

        $this->db->select('pembelian.*');
        $this->db->from('pembelian');

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);

            $this->db->group_start();
            $this->db->or_like('pembelian.kode_pembelian', $search);
            if(!empty($cleanNumber)){
                $this->db->or_like('pembelian.kode_pembelian', $cleanNumber);
            }
            $this->db->group_end();
        } else {
            if($tgl_awal){
                $this->db->where('DATE(pembelian.tanggal) >=', $tgl_awal);
            }
            if($tgl_akhir){
                $this->db->where('DATE(pembelian.tanggal) <=', $tgl_akhir);
            }
        }

        $this->db->order_by('id_pembelian','DESC');

        if(!empty($limit)){
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    // ================= FILTER PENJUALAN RESEP =================
    public function filterPenjualanResep($limit, $tgl_awal, $tgl_akhir, $search){

        $this->db->select('
            pesanan.*, 
            users.nama as kasir,
            resep.kode_resep,
            resep.nama_pasien,
            resep.nama_dokter,
            resep.gambar_resep
        ');
        $this->db->from('pesanan');
        $this->db->join('users', 'users.id_user = pesanan.user_id', 'left');
        $this->db->join('resep', 'resep.id_resep = pesanan.resep_id', 'left');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->where('pesanan.tipe_transaksi', 'resep');

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);

            $this->db->group_start();
            if(!empty($cleanNumber)){
                $this->db->or_like('pesanan.id_pesanan', $cleanNumber);
            }
            $this->db->or_like('users.nama', $search);
            $this->db->or_like('resep.nama_pasien', $search);
            $this->db->or_like('resep.kode_resep', $search);
            $this->db->or_like('resep.nama_dokter', $search);
            $this->db->group_end();
        } else {
            if($tgl_awal){
                $this->db->where('DATE(pesanan.tanggal_pesan) >=', $tgl_awal);
            }
            if($tgl_akhir){
                $this->db->where('DATE(pesanan.tanggal_pesan) <=', $tgl_akhir);
            }
        }

        $this->db->order_by('pesanan.id_pesanan', 'DESC');

        if(!empty($limit)){
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    // ================= COUNT PENJUALAN =================
    public function countPenjualan($tgl_awal = null, $tgl_akhir = null, $search = null){
        $this->db->from('pesanan');
        $this->db->where('status', 'selesai');
        $this->db->where('tipe_transaksi', 'non_resep');

        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(tanggal_pesan) >=', $tgl_awal);
            $this->db->where('DATE(tanggal_pesan) <=', $tgl_akhir);
        }

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);
            $this->db->group_start();
            if(!empty($cleanNumber)){
                $this->db->or_like('id_pesanan', $cleanNumber);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // ================= COUNT PEMBELIAN =================
    public function countPembelian($tgl_awal = null, $tgl_akhir = null, $search = null){
        $this->db->from('pembelian');

        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(tanggal) >=', $tgl_awal);
            $this->db->where('DATE(tanggal) <=', $tgl_akhir);
        }

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);
            $this->db->group_start();
            $this->db->or_like('kode_pembelian', $search);
            if(!empty($cleanNumber)){
                $this->db->or_like('kode_pembelian', $cleanNumber);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // ================= COUNT PENJUALAN RESEP =================
    public function countPenjualanResep($tgl_awal = null, $tgl_akhir = null, $search = null){
        $this->db->from('pesanan');
        $this->db->join('resep', 'resep.id_resep = pesanan.resep_id', 'left');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->where('pesanan.tipe_transaksi', 'resep');

        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(pesanan.tanggal_pesan) >=', $tgl_awal);
            $this->db->where('DATE(pesanan.tanggal_pesan) <=', $tgl_akhir);
        }

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);
            $this->db->group_start();
            if(!empty($cleanNumber)){
                $this->db->or_like('pesanan.id_pesanan', $cleanNumber);
            }
            $this->db->or_like('resep.nama_pasien', $search);
            $this->db->or_like('resep.kode_resep', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // ================= TOTAL PENDAPATAN RESEP =================
    public function getTotalPendapatanResep($tgl_awal = null, $tgl_akhir = null, $search = null){
        $this->db->select('SUM(pesanan.total_harga) as total');
        $this->db->from('pesanan');
        $this->db->join('resep', 'resep.id_resep = pesanan.resep_id', 'left');
        $this->db->where('pesanan.status', 'selesai');
        $this->db->where('pesanan.tipe_transaksi', 'resep');

        if($tgl_awal && $tgl_akhir){
            $this->db->where('DATE(pesanan.tanggal_pesan) >=', $tgl_awal);
            $this->db->where('DATE(pesanan.tanggal_pesan) <=', $tgl_akhir);
        }

        if($search){
            $search = trim($search);
            $cleanNumber = preg_replace('/[^0-9]/', '', $search);
            $this->db->group_start();
            if(!empty($cleanNumber)){
                $this->db->or_like('pesanan.id_pesanan', $cleanNumber);
            }
            $this->db->or_like('resep.nama_pasien', $search);
            $this->db->or_like('resep.kode_resep', $search);
            $this->db->group_end();
        }

        $result = $this->db->get()->row();
        return $result->total ?? 0;
    }

}