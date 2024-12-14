<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page_proses = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Tentukan judul halaman berdasarkan nama file
switch ($current_page_proses) {
    case 'dashboard':
        $page_title_proses = 'dashboard';
        break;
    case 'kub':
        $page_title_proses = 'kub';
        break;
    case 'ketua_kub':
        $page_title_proses = 'ketua_kub';
        break;
    case 'u_p':
        $page_title_proses = 'u_p';
        break;
    case 'pelayanan':
        $page_title_proses = 'pelayanan';
        break;
    case 'pastor':
        $page_title_proses = 'pastor';
        break;
    case 'umat':
        $page_title_proses = 'umat';
        break;
    case 'log_out':
        $page_title_proses = 'Log Out';
        break;
    default:
        $page_title_proses_proses = 'dashboard';
        break;
}
