<?php
class Supplier_model extends CI_Model {

    public function get_laporan_pembelian($start_date = null, $end_date = null, $status = null)
    {
        $this->db->select('
            pembelian.id_pembelian,
            pembelian.kode_pembelian,
            pembelian.tanggal,
            pembelian.total,
            pembelian.status,
            pembelian.catatan,
            supplier.nama_supplier,
            supplier.kontak,
            supplier.alamat,
            GROUP_CONCAT(DISTINCT produk.nama_produk SEPARATOR ", ") as nama_produk,
            GROUP_CONCAT(DISTINCT detail_pembelian.jumlah SEPARATOR ", ") as jumlah,
            transaksi.status_bayar,
            transaksi.tanggal_bayar,
            transaksi.bukti_pembayaran,
            users.nama as nama_user,
            approved.nama as nama_approver
        ');
        $this->db->from('pembelian');
        $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
        $this->db->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left');
        $this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left');
        $this->db->join('transaksi', 'transaksi.id_transaksi = pembelian.id_transaksi', 'left');
        $this->db->join('users', 'users.id_user = pembelian.user_id', 'left');
        $this->db->join('users as approved', 'approved.id_user = pembelian.approved_by', 'left');
        
        // Filter tanggal
        if($start_date && $end_date) {
            $this->db->where('DATE(pembelian.tanggal) >=', $start_date);
            $this->db->where('DATE(pembelian.tanggal) <=', $end_date);
        } elseif($start_date) {
            $this->db->where('DATE(pembelian.tanggal) >=', $start_date);
        } elseif($end_date) {
            $this->db->where('DATE(pembelian.tanggal) <=', $end_date);
        }
        
        // Filter status
        if($status && $status != 'semua') {
            $this->db->where('pembelian.status', $status);
        }
        
        $this->db->group_by('pembelian.id_pembelian');
        $this->db->order_by('pembelian.tanggal', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get summary statistics for report
     * @param string $start_date
     * @param string $end_date
     * @param string $status
     * @return object
     */
    public function get_laporan_summary($start_date = null, $end_date = null, $status = null)
    {
        $this->db->select('
            COUNT(pembelian.id_pembelian) as total_transaksi,
            SUM(pembelian.total) as total_nominal,
            SUM(CASE WHEN transaksi.status_bayar = "lunas" THEN pembelian.total ELSE 0 END) as total_lunas,
            SUM(CASE WHEN transaksi.status_bayar != "lunas" OR transaksi.status_bayar IS NULL THEN pembelian.total ELSE 0 END) as total_belum_lunas,
            COUNT(CASE WHEN pembelian.status = "selesai" THEN 1 END) as jumlah_selesai,
            COUNT(CASE WHEN pembelian.status = "diproses" THEN 1 END) as jumlah_diproses,
            COUNT(CASE WHEN pembelian.status = "diterima" THEN 1 END) as jumlah_diterima,
            COUNT(CASE WHEN pembelian.status = "menunggu" THEN 1 END) as jumlah_menunggu,
            COUNT(CASE WHEN pembelian.status = "disetujui" THEN 1 END) as jumlah_disetujui,
            COUNT(CASE WHEN pembelian.status = "ditolak" THEN 1 END) as jumlah_ditolak
        ');
        $this->db->from('pembelian');
        $this->db->join('transaksi', 'transaksi.id_transaksi = pembelian.id_transaksi', 'left');
        
        // Filter tanggal
        if($start_date && $end_date) {
            $this->db->where('DATE(pembelian.tanggal) >=', $start_date);
            $this->db->where('DATE(pembelian.tanggal) <=', $end_date);
        } elseif($start_date) {
            $this->db->where('DATE(pembelian.tanggal) >=', $start_date);
        } elseif($end_date) {
            $this->db->where('DATE(pembelian.tanggal) <=', $end_date);
        }
        
        // Filter status
        if($status && $status != 'semua') {
            $this->db->where('pembelian.status', $status);
        }
        
        return $this->db->get()->row();
    }
    
    /**
     * Get pembelian by supplier
     * @param int $supplier_id
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_laporan_by_supplier($supplier_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select('
            pembelian.id_pembelian,
            pembelian.kode_pembelian,
            pembelian.tanggal,
            pembelian.total,
            pembelian.status,
            supplier.nama_supplier,
            supplier.kontak,
            GROUP_CONCAT(DISTINCT produk.nama_produk SEPARATOR ", ") as nama_produk,
            transaksi.status_bayar,
            transaksi.tanggal_bayar
        ');
        $this->db->from('pembelian');
        $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
        $this->db->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left');
        $this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left');
        $this->db->join('transaksi', 'transaksi.id_transaksi = pembelian.id_transaksi', 'left');
        
        if($supplier_id) {
            $this->db->where('pembelian.supplier_id', $supplier_id);
        }
        
        if($start_date && $end_date) {
            $this->db->where('DATE(pembelian.tanggal) >=', $start_date);
            $this->db->where('DATE(pembelian.tanggal) <=', $end_date);
        }
        
        $this->db->group_by('pembelian.id_pembelian');
        $this->db->order_by('pembelian.tanggal', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get all suppliers for dropdown filter
     * @return array
     */
    public function get_all_suppliers()
    {
        return $this->db->select('id_supplier, nama_supplier')
                        ->order_by('nama_supplier', 'ASC')
                        ->get('supplier')
                        ->result();
    }
    
    /**
     * Get status options
     * @return array
     */
    public function get_status_options()
    {
        return [
            'semua' => 'Semua Status',
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'diproses' => 'Diproses',
            'diterima' => 'Diterima',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan'
        ];
    }


public function get_pembayaran_selesai()
{
    $this->db->select('
        pembelian.*,
        pembelian.status as status,
        supplier.nama_supplier,
        transaksi.bukti_pembayaran,
        transaksi.tanggal_bayar,
        transaksi.status_bayar,
        GROUP_CONCAT(produk.nama_produk SEPARATOR ", ") as nama_produk
    ');

    $this->db->from('pembelian');
    $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
    $this->db->join('transaksi', 'transaksi.id_transaksi = pembelian.id_transaksi', 'left');
    $this->db->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left');
    $this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left');

    $this->db->group_by('pembelian.id_pembelian');
    $this->db->order_by('pembelian.tanggal', 'DESC');

    return $this->db->get()->result();
}
public function get_approved_pembelian()
{
    return $this->db
    ->select('
        pembelian.*,
        supplier.nama_supplier,
        users.nama as nama_user,
        detail_pembelian.produk_id,
        produk.nama_produk,
        detail_pembelian.jumlah,
        detail_pembelian.harga,
        detail_pembelian.subtotal
    ')
    ->from('pembelian')
    ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
    ->join('users', 'users.id_user = pembelian.user_id', 'left')
    ->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left')
    ->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left')

        // HANYA STATUS MENUNGGU
        ->where('pembelian.status', 'disetujui')
        ->order_by('pembelian.tanggal', 'DESC')

    // sembunyikan status selesai
    ->where('pembelian.status !=', 'selesai')

    ->order_by('pembelian.id_pembelian','DESC')
    ->get()
    ->result();
}
    // Mengambil detail item untuk satu pembelian tertentu
    public function get_detail_pembelian($id_pembelian) {
        $this->db->select('detail_pembelian.*, produk.nama_produk');
        $this->db->from('detail_pembelian');
        $this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id');
        $this->db->where('pembelian_id', $id_pembelian);
        return $this->db->get()->result();
    }
    public function get_order()
    {
        return $this->db->get_where('pesanan', ['status' => 0])->result();
    }
    public function getAll()
    {
        return $this->db->get('supplier')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('supplier',['id_supplier'=>$id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('supplier',$data);
    }

    public function update($id,$data)
    {
        return $this->db->where('id_supplier',$id)->update('supplier',$data);
    }

    public function delete($id)
    {
        return $this->db->where('id_supplier',$id)->delete('supplier');
    }

    public function getFiltered($limit, $start, $keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->get('supplier', $limit, $start)->result();
}

public function countFiltered($keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->count_all_results('supplier');
}

public function countAll()
{
    return $this->db->count_all('supplier');
}

public function getPaginated($limit, $start)
{
    return $this->db->get('supplier', $limit, $start)->result();
}

public function getProdukBySupplier($id)
{
    return $this->db
        ->join('supplier', 'supplier.id_supplier = produk.supplier_id')
        ->where('supplier_id', $id)
        ->get('produk')
        ->result();
}

public function getFilteredAjax($keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->get('supplier')->result();
}
}