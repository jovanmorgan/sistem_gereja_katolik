<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_pelayanan = $_POST['id_pelayanan'];
$id_pastor = $_POST['id_pastor'];
$id_u_p = $_POST['id_u_p'];
$hari_tgl = $_POST['hari_tgl'];
$tempat = $_POST['tempat'];

// Lakukan validasi data
if (empty($id_pelayanan) || empty($id_pastor) || empty($id_u_p) || empty($hari_tgl) || empty($tempat)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_u_p sudah ada
$query_check = "SELECT * FROM pelayanan  WHERE id_u_p = '$id_u_p' AND id_pastor = '$id_pastor' AND hari_tgl = '$hari_tgl' AND tempat = '$tempat' AND id_u_p != '$id_u_p'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_u_p sudah ada
    exit();
}

// Buat query SQL untuk mengupdate data
$query_update = "UPDATE pelayanan SET id_pastor = '$id_pastor', id_u_p = '$id_u_p', hari_tgl = '$hari_tgl', tempat = '$tempat' WHERE id_pelayanan = '$id_pelayanan'";

// Jalankan query
if (mysqli_query($koneksi, $query_update)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
