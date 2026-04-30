<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Active Database Group
|--------------------------------------------------------------------------
*/
$active_group = 'default';
$query_builder = TRUE;

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
| Disesuaikan untuk XAMPP (localhost)
| Database: sistem_sales_order
| Username: root
| Password: (kosong)
|--------------------------------------------------------------------------
*/

$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',       // gunakan IP agar lebih cepat & stabil
    'username' => 'root',
    'password' => '',
    'database' => 'sistem_manajemen_apotek',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,             // hindari persistent connection di lokal
    'db_debug' => (ENVIRONMENT !== 'production'), // tampilkan error hanya saat dev
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => TRUE,              // aktifkan strict mode agar SQL lebih aman
    'failover' => array(),
    'save_queries' => TRUE           // untuk debug query & laporan error
);

/*
|--------------------------------------------------------------------------
| Environment-specific Configuration (Opsional)
|--------------------------------------------------------------------------
| Kamu bisa menambahkan konfigurasi lain misalnya untuk hosting / staging:
| 
| $db['production'] = array(
|     'hostname' => 'your-server.com',
|     'username' => 'db_user',
|     'password' => 'your_pass',
|     'database' => 'sistem_sales_order',
|     'dbdriver' => 'mysqli',
|     'pconnect' => FALSE,
|     'db_debug' => FALSE,
|     'char_set' => 'utf8mb4',
|     'dbcollat' => 'utf8mb4_unicode_ci'
| );
|
|--------------------------------------------------------------------------
*/
