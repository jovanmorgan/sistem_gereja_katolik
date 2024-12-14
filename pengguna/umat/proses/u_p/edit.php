<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_u_p = $_POST['id_u_p'];
$id_umat = $_POST['id_umat'];
$jenis_pelayanan = $_POST['jenis_pelayanan'];

// Lakukan validasi data
if (empty($id_u_p) || empty($id_umat) || empty($jenis_pelayanan)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_u_p sudah ada
$query_check = "SELECT * FROM u_p WHERE jenis_pelayanan = '$jenis_pelayanan' AND id_u_p != '$id_u_p'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_u_p sudah ada
    exit();
}

// Buat query SQL untuk mengupdate data
$query_update = "UPDATE u_p SET id_umat = '$id_umat', jenis_pelayanan = '$jenis_pelayanan' WHERE id_u_p = '$id_u_p'";

// Jalankan query
if (mysqli_query($koneksi, $query_update)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
