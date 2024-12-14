<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_umat = $_POST['id_umat'];
$jenis_pelayanan = $_POST['jenis_pelayanan'];

// Lakukan validasi data
if (empty($id_umat) || empty($jenis_pelayanan)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah no_kub sudah ada
$query_check = "SELECT * FROM u_p WHERE jenis_pelayanan = '$jenis_pelayanan'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada"; // Beri tahu jika no_kub sudah ada
    exit();
}

// Buat query SQL untuk menambahkan data masyarakat ke dalam database
$query = "INSERT INTO u_p (id_umat, jenis_pelayanan) 
          VALUES ('$id_umat', '$jenis_pelayanan')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
