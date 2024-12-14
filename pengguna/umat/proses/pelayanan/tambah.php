<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_pastor = $_POST['id_pastor'];
$id_u_p = $_POST['id_u_p'];
$hari_tgl = $_POST['hari_tgl'];
$tempat = $_POST['tempat'];

// Lakukan validasi data
if (empty($id_pastor) || empty($id_u_p) || empty($hari_tgl) || empty($tempat)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_kub sudah ada
$query_check = "SELECT * FROM pelayanan WHERE id_u_p = '$id_u_p' and id_pastor = '$id_pastor' and hari_tgl = '$hari_tgl' and tempat = '$tempat'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_kub sudah ada
    exit();
}

// Buat query SQL untuk menambahkan data masyarakat ke dalam database
$query = "INSERT INTO pelayanan (id_pastor, id_u_p, hari_tgl, tempat) 
          VALUES ('$id_pastor', '$id_u_p', '$hari_tgl', '$tempat')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
