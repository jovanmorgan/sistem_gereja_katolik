<?php
// Lakukan koneksi ke database
include '../../../../keamanan/koneksi.php';

// Cek apakah terdapat data yang dikirimkan melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data yang dikirimkan melalui form
    $id_pastor = $_POST['id_pastor'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Lakukan validasi data
    if (empty($nama_lengkap) || empty($password)) {
        echo "data tidak lengkap";
        exit();
    }
    // Cek apakah username sudah ada di database
    $check_query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "error_username_exists"; // Kirim respon "error_username_exists" jika email sudah terdaftar
        exit();
    }
    // Cek apakah username sudah ada di database
    $check_query_umat = "SELECT * FROM umat WHERE username = '$username'";
    $result_umat = mysqli_query($koneksi, $check_query_umat);
    if (mysqli_num_rows($result_umat) > 0) {
        echo "error_username_exists"; // Kirim respon "error_username_exists" jika email sudah terdaftar
        exit();
    }
    // Cek apakah username sudah ada di database
    $check_query_ketua_kub = "SELECT * FROM ketua_kub WHERE username = '$username'";
    $result_ketua_kub = mysqli_query($koneksi, $check_query_ketua_kub);
    if (mysqli_num_rows($result_ketua_kub) > 0) {
        echo "error_username_exists"; // Kirim respon "error_username_exists" jika email sudah terdaftar
        exit();
    }

    // Cek apakah username sudah ada di database
    $check_query_pastor = "SELECT * FROM pastor WHERE username = '$username' AND id_pastor != '$id_pastor'";
    $result_pastor = mysqli_query($koneksi, $check_query_pastor);
    if (mysqli_num_rows($result_pastor) > 0) {
        echo "error_username_exists"; // Kirim respon "error_username_exists" jika email sudah terdaftar
        exit();
    }
    // Query SQL untuk update data foto profile
    $query = "UPDATE pastor SET password='$password', nama_lengkap='$nama_lengkap', username='$username' WHERE id_pastor='$id_pastor'";

    // Lakukan proses update data foto profile di database
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        echo "success";
        exit();
    } else {
        // Jika terjadi kesalahan saat melakukan proses update, tampilkan pesan kesalahan
        echo "Gagal melakukan proses update data foto profile: " . mysqli_error($koneksi);
    }
} else {
    // Jika metode request bukan POST, berikan respons yang sesuai
    echo "Invalid request method";
    exit();
}

// Tutup koneksi ke database
mysqli_close($koneksi);
