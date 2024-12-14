<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_kub = $_POST['id_kub'];
$rt = $_POST['rt'];
$rw = $_POST['rw'];
$nama_kub = $_POST['nama_kub'];
$alamat = $_POST['alamat'];

// Lakukan validasi data
if (empty($id_kub) || empty($rt) || empty($rw) || empty($nama_kub) || empty($alamat)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_kub sudah ada
$query_check = "SELECT * FROM kub WHERE nama_kub = '$nama_kub' AND id_kub != '$id_kub'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_kub sudah ada
    exit();
}

// Buat query SQL untuk mengupdate data
$query_update = "UPDATE kub SET rt = '$rt', rw = '$rw', nama_kub = '$nama_kub', alamat = '$alamat' WHERE id_kub = '$id_kub'";

// Jalankan query
if (mysqli_query($koneksi, $query_update)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
