<?php
function cek_login(){
    $CI =& get_instance();

    if(!$CI->session->userdata('logged_in')){
        redirect('auth');
    }
}

function cek_role($roles = []){
    $CI =& get_instance();

    if(!in_array($CI->session->userdata('role'), $roles)){
        redirect('auth');
    }
}