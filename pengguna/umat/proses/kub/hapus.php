<?php
include '../../../../keamanan/koneksi.php';

// Terima ID kub yang akan dihapus dari formulir HTML
$id_kub = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_kub)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data kub berdasarkan ID
$query_delete_kub = "DELETE FROM kub WHERE id_kub = '$id_kub'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_kub)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
