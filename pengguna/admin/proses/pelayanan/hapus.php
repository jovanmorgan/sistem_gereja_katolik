<?php
include '../../../../keamanan/koneksi.php';

// Terima ID pelayanan yang akan dihapus dari formulir HTML
$id_pelayanan = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_pelayanan)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data pelayanan berdasarkan ID
$query_delete_pelayanan = "DELETE FROM pelayanan WHERE id_pelayanan = '$id_pelayanan'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_pelayanan)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);