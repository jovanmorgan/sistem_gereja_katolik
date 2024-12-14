<?php
session_start();

// Periksa apakah pengguna sudah masuk atau belum
if (!isset($_SESSION['id_pastor'])) {
    // Pengguna belum masuk, arahkan kembali ke halaman masuk.php
    header("Location: ../../berlangganan/login");
    exit; // Pastikan untuk menghentikan eksekusi skrip setelah mengarahkan
}

$id_pastor = $_SESSION['id_pastor'];