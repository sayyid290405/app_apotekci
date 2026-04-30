<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
| Diset otomatis agar tidak perlu diubah di komputer lain.
| Contoh hasil: http://localhost/utspbwrizky/
*/
$root  = "http://" . $_SERVER['HTTP_HOST'];
$root .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
$config['base_url'] = $root;

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
| Kosongkan agar URL menjadi bersih (tanpa index.php)
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
*/
$config['uri_protocol'] = 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| Default Settings
|--------------------------------------------------------------------------
*/
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset']  = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger']   = 'm';
$config['directory_trigger']  = 'd';

/*
|--------------------------------------------------------------------------
| Error Logging
|--------------------------------------------------------------------------
*/
$config['log_threshold'] = 1;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Session Configuration
|--------------------------------------------------------------------------
| Gunakan 'files' agar otomatis tersimpan di folder writable sistem.
| Pastikan folder "application/cache" bisa ditulis (writable).
*/
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = sys_get_temp_dir(); // simpan di temp directory
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookies
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']    = '';
$config['cookie_domain']    = '';
$config['cookie_path']      = '/';
$config['cookie_secure']    = FALSE;
$config['cookie_httponly']  = TRUE;
$config['cookie_samesite']  = 'Lax';

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
| Gunakan key acak untuk keamanan session & CSRF
*/
$config['encryption_key'] = 'PBWRIZKY_2025_SALESORDER_KEY';

/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
| Direkomendasikan diaktifkan untuk form login & input.
*/
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array('api/*'); // contoh: kecualikan API

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Timezone dan Standardisasi
|--------------------------------------------------------------------------
*/
date_default_timezone_set('Asia/Jakarta');
$config['time_reference'] = 'local';
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Proxy Settings
|--------------------------------------------------------------------------
*/
$config['proxy_ips'] = '';
$config['time_reference'] = 'local';
$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
// Pastikan ini diatur ke zona waktu untuk Jakarta/WIB:
date_default_timezone_set('Asia/Jakarta');
