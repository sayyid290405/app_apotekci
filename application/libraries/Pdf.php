<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Cek apakah Dompdf sudah terinstall
if (!class_exists('Dompdf\Dompdf')) {
    // Coba load dari vendor (Composer)
    $vendor_path = FCPATH . 'vendor/autoload.php';
    if (file_exists($vendor_path)) {
        require_once $vendor_path;
    } else {
        // Jika tidak ada, tampilkan error
        show_error('Dompdf tidak ditemukan. Silakan install dengan: composer require dompdf/dompdf');
    }
}

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf
{
    private $dompdf;
    
    public function __construct()
    {
        // Setup Dompdf Options
        $options = new Options();
        $options->set('defaultFont', 'sans-serif');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        // Buat folder tmp jika belum ada
        $tmp_dir = FCPATH . 'tmp';
        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0777, true);
        }
        $options->set('tempDir', $tmp_dir);
        
        // Inisialisasi Dompdf
        $this->dompdf = new Dompdf($options);
    }
    
    /**
     * METHOD GENERATE - Untuk kemudahan penggunaan (1 method langsung jadi)
     * @param string $html HTML content
     * @param string $filename Nama file (tanpa .pdf)
     * @param string $paper_size Ukuran kertas (A4, A5, Legal, Letter, dll)
     * @param string $orientation Orientasi (portrait/landscape)
     */
    public function generate($html, $filename = 'document', $paper_size = 'A4', $orientation = 'portrait')
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paper_size, $orientation);
        $this->dompdf->render();
        $this->dompdf->stream($filename . '.pdf', array('Attachment' => 0));
    }
    
    /**
     * METHOD GENERATE_DOWNLOAD - Untuk download langsung
     * @param string $html HTML content
     * @param string $filename Nama file (tanpa .pdf)
     * @param string $paper_size Ukuran kertas
     * @param string $orientation Orientasi
     */
    public function generate_download($html, $filename = 'document', $paper_size = 'A4', $orientation = 'portrait')
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paper_size, $orientation);
        $this->dompdf->render();
        $this->dompdf->stream($filename . '.pdf', array('Attachment' => 1));
    }
    
    /**
     * Load HTML content ke PDF
     * @param string $html
     * @return $this
     */
    public function loadHtml($html)
    {
        $this->dompdf->loadHtml($html);
        return $this;
    }
    
    /**
     * Set ukuran kertas
     * @param string|array $size
     * @param string $orientation
     * @return $this
     */
    public function setPaper($size, $orientation = 'portrait')
    {
        $this->dompdf->setPaper($size, $orientation);
        return $this;
    }
    
    /**
     * Render PDF
     * @return $this
     */
    public function render()
    {
        $this->dompdf->render();
        return $this;
    }
    
    /**
     * Tampilkan atau download PDF
     * @param string $filename
     * @param array $options
     */
    public function stream($filename, $options = array())
    {
        // Default: tampilkan di browser (Attachment = 0)
        if (!isset($options['Attachment'])) {
            $options['Attachment'] = 0;
        }
        $this->dompdf->stream($filename, $options);
    }
    
    /**
     * Dapatkan output PDF sebagai string
     * @return string
     */
    public function output()
    {
        return $this->dompdf->output();
    }
    
    /**
     * Simpan PDF ke file
     * @param string $filename
     * @return $this
     */
    public function save($filename)
    {
        file_put_contents($filename, $this->output());
        return $this;
    }
}