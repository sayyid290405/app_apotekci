<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grafik extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_manajer'); // Load the model
    }

    public function index() {
        // Get data from the model
        $data['users'] = $this->Dashboard_manajer->get_users();
        $data['orders'] = $this->Dashboard_manajer->get_orders();
        $data['products'] = $this->Dashboard_manajer->get_products();

        // Load the view and pass the data
        $this->load->view('grafik_view', $data);
    }
}