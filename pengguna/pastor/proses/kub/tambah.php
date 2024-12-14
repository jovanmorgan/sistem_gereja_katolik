<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nama_kub = $_POST['nama_kub'];
$rt = $_POST['rt'];
$rw = $_POST['rw'];
$alamat = $_POST['alamat'];

// Lakukan validasi data
if (empty($nama_kub) || empty($rt) || empty($rw) || empty($alamat)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_kub sudah ada
$query_check = "SELECT * FROM kub WHERE nama_kub = '$nama_kub'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_kub sudah ada
    exit();
}

// Buat query SQL untuk menambahkan data masyarakat ke dalam database
$query = "INSERT INTO kub (nama_kub, rt, rw, alamat) 
          VALUES ('$nama_kub', '$rt', '$rw', '$alamat')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
