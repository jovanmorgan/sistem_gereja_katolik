<?php
include '../../../../keamanan/koneksi.php';

// Terima ID pastor yang akan dihapus dari formulir HTML
$id_pastor = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_pastor)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data pastor berdasarkan ID
$query_delete_pastor = "DELETE FROM pastor WHERE id_pastor = '$id_pastor'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_pastor)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
