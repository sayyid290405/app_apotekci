<?php
class Resep extends CI_Controller {

    public function index()
    {
        $data['js'] = 'resep.js';
        
        $this->load->view('templates/header',$data);
        $this->load->view('templates/sidebar');
        $this->load->view('resep/index');
        $this->load->view('templates/footer');
    }

    public function simpan()
{
    header('Content-Type: application/json');

    try {

        $items = json_decode($this->input->post('items'), true);

        if(!$items || !is_array($items)){
            echo json_encode([
                'status'=>'error',
                'message'=>'Data item kosong'
            ]);
            return;
        }

        $this->db->trans_start();

        // ================= INSERT RESEP (TANPA PASIEN/DOKTER)
        $this->db->insert('resep', [
            'tanggal' => date('Y-m-d'),
            'status'  => 'draft'
        ]);

        $resep_id = $this->db->insert_id();

        // ================= DETAIL RESEP
        foreach($items as $i){

            if(!isset($i['id'])) continue;

            $this->db->insert('detail_resep', [
                'resep_id'  => $resep_id,
                'produk_id' => $i['id'],
                'jumlah'    => $i['qty'] ?? 1,
                'dosis'     => $i['dosis'] ?? null,
                'satuan'    => $i['satuan'] ?? '-',   // ✅ fallback
                 'harga'     => $i['harga'] ?? 0  ,
                'catatan'   => null
            ]);
        }

        // ================= UPLOAD FILE
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){

            $config['upload_path']   = './uploads/resep/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['encrypt_name']  = true;

            $this->load->library('upload',$config);

            if($this->upload->do_upload('file')){

                $file = $this->upload->data('file_name');

                $this->db->insert('resep_file',[
                    'resep_id' => $resep_id,
                    'file_path'=> $file
                ]);

            } else {
                throw new Exception(strip_tags($this->upload->display_errors()));
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Gagal simpan database');
        }

        echo json_encode([
            'status'=>'ok',
            'id'=>$resep_id
        ]);

    } catch(Exception $e){

        echo json_encode([
            'status'=>'error',
            'message'=>$e->getMessage()
        ]);
    }
}

    public function searchProduk()
{
    $q = $this->input->get('q');

    $data = $this->db
        ->like('nama_produk', $q)
        ->limit(10)
        ->get('produk')
        ->result();

    echo json_encode($data);
}

public function detail($id)
{
    $data['resep'] = $this->db
        ->where('id_resep',$id)
        ->get('resep')
        ->row();

    $data['detail'] = $this->db
        ->select('detail_resep.*, produk.nama_produk')
        ->join('produk','produk.id_produk = detail_resep.produk_id')
        ->where('resep_id',$id)
        ->get('detail_resep')
        ->result();

    $this->load->view('templates/header',$data);
    $this->load->view('templates/sidebar');
    $this->load->view('resep/detail',$data);
    $this->load->view('templates/footer');
}

public function verifikasi($id)
{
    $this->db->where('id_resep',$id)
             ->update('resep',[
                'status' => 'verified'
             ]);

    redirect('resep/detail/'.$id);
}

public function edit($id)
{
    // ambil data resep utama
    $data['resep'] = $this->db
        ->where('id_resep', $id)
        ->get('resep')
        ->row();

    // ambil detail resep
    $data['detail'] = $this->db
        ->select('detail_resep.*, produk.nama_produk')
        ->join('produk','produk.id_produk = detail_resep.produk_id')
        ->where('resep_id', $id)
        ->get('detail_resep')
        ->result();
}

}