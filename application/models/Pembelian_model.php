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

// Di Model (Pembelian_model.php)
public function getProdukBySupplier($supplier_id) {
    $this->db->select('
        produk.*,
        GROUP_CONCAT(
            CONCAT(satuan_produk.id, "::", 
                   satuan_produk.nama_satuan, "::", 
                   satuan_produk.konversi_ke_dasar, "::", 
                   satuan_produk.harga, "::", 
                   COALESCE(satuan_produk.level, 1)
            ) SEPARATOR "||"
        ) as list_satuan
    ');
    $this->db->from('produk');
    $this->db->join('satuan_produk', 'satuan_produk.produk_id = produk.id_produk', 'left');
    $this->db->where('produk.supplier_id', $supplier_id);
    $this->db->group_by('produk.id_produk');
    return $this->db->get()->result();
}
public function getProdukWithSatuan($supplier_id)
{
    $sql = "SELECT 
                p.*,
                (
                    SELECT GROUP_CONCAT(
                        CONCAT(s.id, '::', s.nama_satuan, '::', s.konversi, '::', s.harga, '::', 1)
                        SEPARATOR '||'
                    ) 
                    FROM satuan_produk s 
                    WHERE s.produk_id = p.id_produk
                ) as list_satuan
            FROM produk p
            WHERE p.supplier_id = ?";
    
    return $this->db->query($sql, [$supplier_id])->result();
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
    public function hitungStokDasar($produk_id, $satuan_id, $jumlah) {
    $satuan = $this->db->get_where('satuan_produk', ['id' => $satuan_id])->row();
    if (!$satuan || !$satuan->konversi_ke_dasar) {
        return $jumlah; // fallback ke jumlah asli
    }
    return $jumlah * $satuan->konversi_ke_dasar;
}

    public function simpanDetailPembelian($pembelian_id, $item) {
    // Hitung jumlah dalam satuan dasar untuk stok
    $jumlah_dasar = $this->hitungStokDasar(
        $item['produk_id'], 
        $item['satuan_id'], 
        $item['jumlah']
    );
    
    $data = [
        'pembelian_id' => $pembelian_id,
        'produk_id' => $item['produk_id'],
        'satuan_id' => $item['satuan_id'],
        'jumlah' => $item['jumlah'],
        'harga' => $item['harga'],
        'subtotal' => $item['jumlah'] * $item['harga']
    ];
    
    $this->db->insert('detail_pembelian', $data);
    
    // Update stok produk (dalam satuan dasar)
    $this->db->set('stok', 'stok + ' . $jumlah_dasar, FALSE);
    $this->db->where('id_produk', $item['produk_id']);
    $this->db->update('produk');
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
    public function sudahBayar($id_pembelian)
{
    return $this->db
        ->where('pembelian_id', $id_pembelian)
        ->count_all_results('transaksi') > 0;
}
}