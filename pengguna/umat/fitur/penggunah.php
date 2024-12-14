<?php
session_start();

// Periksa apakah pengguna sudah masuk atau belum
if (!isset($_SESSION['id_umat'])) {
    // Pengguna belum masuk, arahkan kembali ke halaman masuk.php
    header("Location: ../../berlangganan/login");
    exit; // Pastikan untuk menghentikan eksekusi skrip setelah mengarahkan
}

// Ambil data umat dari database
$id_umat = $_SESSION['id_umat'];
