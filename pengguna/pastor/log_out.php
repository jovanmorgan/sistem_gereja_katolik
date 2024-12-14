<?php
session_start();

// Hapus sesi id_pastor jika ada
if (isset($_SESSION['id_pastor'])) {
    unset($_SESSION['id_pastor']);
}

// Redirect pengguna kembali ke halaman login
header("Location: ../../berlangganan/login");
exit;
