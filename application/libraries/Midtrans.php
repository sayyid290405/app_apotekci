<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

class Midtrans {

    public function __construct()
    {
        $CI =& get_instance();

        $CI->load->config('midtrans');

        \Midtrans\Config::$serverKey = $CI->config->item('server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }
}