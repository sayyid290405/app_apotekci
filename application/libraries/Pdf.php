<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 🔥 pakai composer
require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {

    public function generate($html, $filename='', $paper = 'A4', $orientation = 'portrait') {

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $dompdf->stream($filename . ".pdf", ["Attachment" => 0]);
    }
}