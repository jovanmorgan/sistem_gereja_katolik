<?php
include '../../../../keamanan/koneksi.php';

// Terima ID u_p yang akan dihapus dari formulir HTML
$id_u_p = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_u_p)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data u_p berdasarkan ID
$query_delete_u_p = "DELETE FROM u_p WHERE id_u_p = '$id_u_p'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_u_p)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
