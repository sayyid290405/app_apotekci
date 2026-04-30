<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load library dan model yang dibutuhkan
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        $this->load->model(['M_order', 'M_customer', 'M_sales', 'M_product']);

        // Proteksi Login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        $this->role = $this->session->userdata('role');
        $this->user_id = $this->session->userdata('user_id');
    }

    /**
     * Menampilkan daftar Sales Order
     */
    public function index() {
        $data['title'] = 'Data Sales Order';

        // Filter data berdasarkan Role
        if ($this->role === 'admin') {
            $data['orders'] = $this->M_order->get_all();
        } else {
            $sales_id = $this->session->userdata('sales_id');
            $data['orders'] = $this->M_order->get_by_sales($sales_id);
        }

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('orders/index', $data);
        $this->load->view('template/footer');
    }

    /**
     * Form tambah order baru
     */
    public function create() {
        // Generate Kode SO Otomatis
        $last = $this->db->select('order_code')->order_by('id', 'DESC')->limit(1)->get('orders')->row();
        $num = $last ? (int) substr($last->order_code, 2) + 1 : 1;
        $order_code = 'SO' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $data['title'] = 'Tambah Sales Order';
        $data['order_code'] = $order_code;
        $data['customers'] = $this->M_customer->get_all();
        $data['products'] = $this->M_product->get_all();

        if ($this->role === 'admin') {
            $data['sales'] = $this->M_sales->get_all();
        }

        if ($this->input->post()) {
            $this->_process_create($order_code);
        } else {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('orders/add', $data);
            $this->load->view('template/footer');
        }
    }

    /**
     * Logika simpan order & potong stok
     */
    private function _process_create($order_code) {
        $sales_id = ($this->role === 'admin') ? $this->input->post('sales_id', true) : $this->session->userdata('sales_id');
        $products_input = $this->input->post('product_id');
        $quantities_input = $this->input->post('quantity');

        if (empty($products_input)) {
            $this->session->set_flashdata('error', 'Pilih minimal 1 produk.');
            redirect('orders/create');
        }

        $this->db->trans_start(); // Database Transaction Start

        $total_price = 0;
        $order_data = [
            'order_code'  => $order_code,
            'customer_id' => $this->input->post('customer_id', true),
            'sales_id'    => $sales_id,
            'status'      => 'draft',
            'order_date'  => date('Y-m-d H:i:s'),
            'note'        => $this->input->post('note', true),
            'total_price' => 0
        ];

        $order_id = $this->M_order->insert_order($order_data);

        foreach ($products_input as $i => $pid) {
            $qty = (int) $quantities_input[$i];
            if ($qty <= 0) continue;

            $product = $this->M_product->get_by_id($pid);

            // Validasi Stok (Mencegah Race Condition)
            if (!$product || $product->stock < $qty) {
                $this->session->set_flashdata('error', 'Stok ' . ($product->name ?? 'Produk') . ' tidak cukup.');
                $this->db->trans_rollback();
                redirect('orders/create');
            }

            $subtotal = $product->price * $qty;
            $total_price += $subtotal;

            $this->M_order->insert_item([
                'order_id'   => $order_id,
                'product_id' => $pid,
                'quantity'   => $qty,
                'unit_price' => $product->price,
                'subtotal'   => $subtotal
            ]);

            // Potong Stok
            $this->db->where('id', $pid)->set('stock', 'stock-' . $qty, FALSE)->update('products');
        }

        $this->M_order->update_order($order_id, ['total_price' => $total_price]);

        // Audit Log
        $this->_log('create', 'Membuat SO: ' . $order_code . ' | Total: Rp ' . number_format($total_price, 0, ',', '.'));

        $this->db->trans_complete(); // Transaction End

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menyimpan order.');
        } else {
            $this->session->set_flashdata('success', 'Sales order berhasil ditambahkan!');
        }
        redirect('orders');
    }

    /**
     * Update Status SO (Admin Only)
     */
    public function update_status($id) {
        if ($this->role !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('orders');
        }

        $order = $this->M_order->get_by_id($id);
        $new_status = $this->input->post('status', true);

        if (!$order || $new_status === $order->status) redirect('orders');

        $this->db->trans_start();

        $update_data = ['status' => $new_status];
        $now = date('Y-m-d H:i:s');

        // Logika Pengembalian Stok jika dibatalkan
        if ($new_status === 'dibatalkan' && $order->status !== 'dibatalkan') {
            $this->_restore_stock($id);
            $update_data['canceled_at'] = $now;
        } 
        // Logika Pengurangan Stok jika status batal diaktifkan kembali
        elseif ($order->status === 'dibatalkan' && $new_status !== 'dibatalkan') {
            $this->_deduct_stock($id);
        }

        if ($new_status === 'dikirim') $update_data['sent_at'] = $now;
        if ($new_status === 'selesai') $update_data['completed_at'] = $now;

        $this->M_order->update_order($id, $update_data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('success', 'Status SO #' . $order->order_code . ' menjadi ' . strtoupper($new_status));
        }
        redirect('orders');
    }

    /**
     * Hapus Order & Kembalikan Stok otomatis
     */
    public function delete($id) {
        if ($this->role !== 'admin') show_error('Akses ditolak.', 403);

        $order = $this->M_order->get_by_id($id);
        
        if ($order) {
            $this->db->trans_start();

            // Kembalikan stok hanya jika status belum Selesai/Dikirim
            if ($order->status === 'draft' || $order->status === 'dibatalkan') {
                $this->_restore_stock($id);
            }

            $this->M_order->delete($id);
            $this->_log('delete', 'Menghapus SO: ' . $order->order_code);

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $this->session->set_flashdata('success', 'Order dihapus & stok telah diperbarui.');
            }
        }
        redirect('orders');
    }

    // --- HELPER METHODS ---

    private function _restore_stock($order_id) {
        $items = $this->M_order->get_items($order_id);
        foreach ($items as $item) {
            $this->db->where('id', $item->product_id)
                     ->set('stock', 'stock+' . (int)$item->quantity, FALSE)
                     ->update('products');
        }
    }

    private function _deduct_stock($order_id) {
        $items = $this->M_order->get_items($order_id);
        foreach ($items as $item) {
            $this->db->where('id', $item->product_id)
                     ->set('stock', 'stock-' . (int)$item->quantity, FALSE)
                     ->update('products');
        }
    }

    private function _log($action, $detail) {
        $this->db->insert('audit_log', [
            'user_id'    => $this->user_id,
            'action'     => $action,
            'detail'     => $detail,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}