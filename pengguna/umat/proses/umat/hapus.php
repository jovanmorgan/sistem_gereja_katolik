<?php
include '../../../../keamanan/koneksi.php';

// Terima ID umat yang akan dihapus dari formulir HTML
$id_umat = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_umat)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data umat berdasarkan ID
$query_delete_umat = "DELETE FROM umat WHERE id_umat = '$id_umat'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_umat)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
