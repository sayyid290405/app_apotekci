<?php
class Pembelian_model extends CI_Model {
public function get_pembelian_untuk_bayar()
{
    return $this->db->select('
            pembelian.*, 
            supplier.nama_supplier, 
            produk.nama_produk,
            transaksi.bukti_pembayaran,
            transaksi.id_transaksi as id_trans_bayar
        ')
        ->from('pembelian')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
        ->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left')
        ->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left')
        ->join('transaksi', 'transaksi.id_pembelian = pembelian.id_pembelian', 'left') 
        ->group_by('pembelian.id_pembelian')
        ->order_by('pembelian.id_pembelian', 'DESC')
        ->get()
        ->result();
}
// Simpan data transaksi baru dan kembalikan ID-nya
public function insert_transaksi($data)
{
    $this->db->insert('transaksi', $data);
    return $this->db->insert_id();
}

// Update kolom id_transaksi di tabel pembelian
public function update_pembelian_transaksi($id_pembelian, $id_transaksi)
{
    $this->db->where('id_pembelian', $id_pembelian);
    return $this->db->update('pembelian', [
        'id_transaksi' => $id_transaksi,
        'status'       => 'selesai' // Opsional: Ubah status pembelian menjadi selesai
    ]);
}
        public function get_produk()
    {
        $this->db->get('produk')->result();
        $this->db->get('supplier')->result();
    }

    public function hapus($id)
    {
    $this->db->trans_start();

        // Hapus detail permohonan dulu
        $this->db->where('pembelian_id', $id);
        $this->db->delete('detail_pembelian');

        // Baru hapus data induknya
        $this->db->where('id_pembelian', $id);
        $this->db->delete('pembelian');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

public function edit_pembelian($id, $data)
{
 $this->db->trans_start();

    // update header
    $this->db->where('id_pembelian', $id);
    $this->db->update('pembelian', $data);

    // hapus detail lama
    $this->db->where('pembelian_id', $id);
    $this->db->delete('detail_pembelian');

    // insert ulang
    foreach($cart as $c){
        $this->db->insert('detail_pembelian', [
            'pembelian_id' => $id,
            'produk_id'    => $c['id'],
            'jumlah'       => $c['qty'],
            'harga'        => $c['harga'],
            'subtotal'     => $c['subtotal']
        ]);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();

}

    public function update_pembelian($id, $status, $approved_by, $tanggal, $catatan)
    {
        $this->db->where('id_pembelian', $id);
        return $this->db->update('pembelian', [
            'status' => $status,
            'tanggal_approve' => $tanggal,
            'approved_by' => $approved_by,
            'catatan' => $catatan
        ]);
    }

    public function get_pembelian_diproses()
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

        // FILTER STATUS DIPROSES
        ->where('pembelian.status', 'diproses')

        ->order_by('pembelian.id_pembelian','DESC')
        ->get()
        ->result();
}

public function get_laporan_pembayaran()
{
$this->db->select('
    pembelian.*, 
    supplier.nama_supplier, 
    GROUP_CONCAT(produk.nama_produk SEPARATOR ", ") as list_produk
');
$this->db->from('pembelian');
$this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
$this->db->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left');
$this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left');
$this->db->group_by('pembelian.id_pembelian'); // Menyatukan baris berdasarkan ID Pembelian
$data['pembelian'] = $this->db->get()->result();

return $data;
}
public function get_pembelian()
{
    return $this->db

        ->select('
            pembelian.*,

            supplier.nama_supplier,

            users.nama as nama_user,

            detail_pembelian.produk_id,
            detail_pembelian.jumlah,
            detail_pembelian.harga,
            detail_pembelian.subtotal,

            produk.nama_produk
        ')

        ->from('pembelian')

        // JOIN SUPPLIER
        ->join(
            'supplier',
            'supplier.id_supplier = pembelian.supplier_id',
            'left'
        )

        // JOIN USERS
        ->join(
            'users',
            'users.id_user = pembelian.user_id',
            'left'
        )

        // JOIN DETAIL PEMBELIAN
        ->join(
            'detail_pembelian',
            'detail_pembelian.pembelian_id = pembelian.id_pembelian',
            'left'
        )

        // JOIN PRODUK
        ->join(
            'produk',
            'produk.id_produk = detail_pembelian.produk_id',
            'left'
        )

        // FILTER MULTI STATUS
        ->where_in('pembelian.status', [

            'menunggu',
            'disetujui',
            'diproses',
            'selesai',
            'ditolak',
            'dibatalkan'

        ])

        // SORTING
        ->order_by(
            'pembelian.id_pembelian',
            'DESC'
        )

        ->get()

        ->result();
}

public function get_bayar()
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
    ->where('pembelian.status', 'selesai')
    ->order_by('pembelian.id_pembelian', 'DESC')
    ->get()
    ->result();
}

    public function getProdukBySupplier($supplier_id)
    {
        return $this->db
            ->where('supplier_id', $supplier_id)
            ->get('produk')
            ->result();
    }

    public function getStokMinim()
    {
        return $this->db
            ->where('stok <= stok_minimal', NULL, FALSE)
            ->get('produk')
            ->result();
    }

    public function searchProduk($q)
    {
        return $this->db
            ->like('nama_produk', $q)
            ->get('produk')
            ->result();
    }

    public function simpanPembelian($supplier_id, $user_id, $cart)
    {
        $this->db->trans_start();

        $total = array_sum(array_column($cart,'subtotal'));

        $pembelian = [
            'kode_pembelian' => 'INV-'.date('YmdHis'),
            'supplier_id'    => $supplier_id,
            'user_id'        => $user_id,
            'status'         => 'menunggu',
            'total'          => $total
        ];

        $this->db->insert('pembelian', $pembelian);
        $id = $this->db->insert_id();

        foreach($cart as $c){
            $this->db->insert('detail_pembelian',[
                'pembelian_id' => $id,
                'produk_id'    => $c['id'],
                'jumlah'       => $c['qty'],
                'harga'        => $c['harga'],
                'subtotal'     => $c['subtotal']
            ]);
        }

        $this->db->trans_complete();

        return $id;
    }
}