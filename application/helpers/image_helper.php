<?php
function get_gambar($gambar){

    if(empty($gambar)){
        return base_url('assets/no-image.png');
    }

    // jika URL (http/https)
    if(filter_var($gambar, FILTER_VALIDATE_URL)){
        return $gambar;
    }

    // jika file lokal
    if(file_exists(FCPATH . 'uploads/' . $gambar)){
        return base_url('uploads/' . $gambar);
    }

    // fallback
    return base_url('assets/no-image.png');
}