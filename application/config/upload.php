<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['upload_bukti'] = array(
    'upload_path'   => './uploads/bukti/',
    'allowed_types' => 'jpg|jpeg|png',
    'max_size'      => 2048,
    'encrypt_name'  => TRUE
);

$this->load->library('upload', $config['upload_bukti']);