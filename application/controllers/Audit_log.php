<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit_log extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Memuat library pagination
        $this->load->library(['session','form_validation','pagination']);
        $this->load->helper(['url','file','download']);
        $this->load->model('M_audit_log');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        if (!in_array((int)$this->session->userdata('role_id'), [1, 3])) {
            show_error("Akses ditolak", 403);
        }
    }

    public function index()
    {
        $data['title'] = "Riwayat Aktivitas (Audit Log)";

        // 1. Ambil input filter
        $start  = $this->input->get('start_date', true);
        $end    = $this->input->get('end_date', true);
        $user   = $this->input->get('user_id', true);
        $action = $this->input->get('action', true);
        $q      = $this->input->get('q', true);

        // --- FITUR SHOW ENTRIES ---
        // Mengambil input per_page, jika kosong default ke 20
        $per_page_input = $this->input->get('per_page', true);
        $per_page = ($per_page_input) ? (int)$per_page_input : 20;

        // 2. Konfigurasi Pagination
        $config['base_url']             = base_url('audit_log/index');
        $config['total_rows']           = $this->M_audit_log->count_logs($start, $end, $user, $action, $q);
        $config['per_page']             = $per_page; // Dinamis mengikuti input user
        $config['uri_segment']          = 3;
        $config['reuse_query_string']   = TRUE; // Penting agar filter & per_page tidak hilang saat klik angka halaman

        // Styling Pagination Bootstrap 4
        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = 'First';
        $config['last_link']        = 'Last';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '&laquo;';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '&raquo;';
        $config['next_tag_open']    = '<li class="page-item">';
        $config['next_tag_close']   = '</li>';
        $config['last_tag_open']    = '<li class="page-item">';
        $config['last_tag_close']   = '</li>';
        $config['cur_tag_open']     = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close']    = '</a></li>';
        $config['num_tag_open']     = '<li class="page-item">';
        $config['num_tag_close']    = '</li>';
        $config['attributes']       = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // 3. Ambil offset dari URI (segmen 3)
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        // 4. Ambil data dengan Limit dan Offset
        $data['logs'] = $this->M_audit_log->get_logs($start, $end, $user, $action, $q, $config['per_page'], $page);
        $data['users'] = $this->M_audit_log->get_users();
        $data['pagination_links'] = $this->pagination->create_links();

        // 5. Data untuk Grafik (Sesuai filter tanggal)
        $chartData = $this->M_audit_log->get_summary_days($start, $end, 30);
        $data['chart_labels'] = json_encode(array_column($chartData, 'date') ?: []);
        $data['chart_login']  = json_encode(array_column($chartData, 'login') ?: []);
        $data['chart_logout'] = json_encode(array_column($chartData, 'logout') ?: []);
        $data['chart_create'] = json_encode(array_column($chartData, 'create_count') ?: []);
        $data['chart_update'] = json_encode(array_column($chartData, 'update_count') ?: []);
        $data['chart_delete'] = json_encode(array_column($chartData, 'delete_count') ?: []);
        $data['chart_error']  = json_encode(array_column($chartData, 'error_count') ?: []);

        // Load views
        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('audit_log/index', $data);
        $this->load->view('template/footer');
    }

    public function export_csv()
    {
        $start  = $this->input->get('start_date', true);
        $end    = $this->input->get('end_date', true);
        $user   = $this->input->get('user_id', true);
        $action = $this->input->get('action', true);
        $q      = $this->input->get('q', true);

        // Export mengambil semua data terfilter (tanpa limit pagination)
        $logs = $this->M_audit_log->get_logs($start, $end, $user, $action, $q);

        header("Content-type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=audit_log_" . date('Ymd_His') . ".csv");
        echo "\xEF\xBB\xBF"; 

        $output = fopen("php://output", "w");
        fputcsv($output, ["No", "Tanggal", "User", "Username", "Aksi", "Detail"]);

        $no = 1;
        foreach ($logs as $log) {
            fputcsv($output, [
                $no++,
                $log->created_at,
                $log->fullname,
                $log->username,
                $log->action,
                $log->detail
            ]);
        }
        fclose($output);
        exit;
    }

    public function truncate()
    {
        // Proteksi khusus Admin
        if ((int)$this->session->userdata('role_id') !== 1) {
            $this->session->set_flashdata('error', 'Hanya administrator yang diizinkan menghapus log.');
            redirect('audit_log');
        }

        if ($this->input->method() !== 'post') {
            show_error("Metode pengiriman tidak valid", 405);
        }

        $this->M_audit_log->truncate();
        $this->session->set_flashdata('success', 'Semua riwayat aktivitas telah dibersihkan.');
        redirect('audit_log');
    }
}