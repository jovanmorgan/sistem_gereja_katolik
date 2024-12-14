<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_kub = $_POST['id_kub'];
$nama_lengkap = $_POST['nama_lengkap'];
$jk = $_POST['jk'];
$no_hp = $_POST['no_hp'];
$username = $_POST['username'];
$password = $_POST['password'];
$alamat = $_POST['alamat'];

// Cek apakah nik sudah ada
$query_check = "SELECT * FROM umat WHERE username = '$username'";
$result_check = mysqli_query($koneksi, $query_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "data_sudah_ada";
    exit();
}

// Cek apakah username sudah ada di database
$check_query = "SELECT * FROM admin WHERE username = '$username'";
$result = mysqli_query($koneksi, $check_query);
if (mysqli_num_rows($result) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}
// Cek apakah username sudah ada di database
$check_query_pastor = "SELECT * FROM pastor WHERE username = '$username'";
$result_pastor = mysqli_query($koneksi, $check_query_pastor);
if (mysqli_num_rows($result_pastor) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}
// Cek apakah username sudah ada di database
$check_query_ketua_kub = "SELECT * FROM ketua_kub WHERE username = '$username'";
$result_ketua_kub = mysqli_query($koneksi, $check_query_ketua_kub);
if (mysqli_num_rows($result_ketua_kub) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}


if (strlen($password) < 8) {
    echo "error_password_length"; // Kirim respon "error_password_length" jika panjang password kurang dari 8 karakter
    exit();
}

// Tambahkan logika untuk memeriksa password
if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
    echo "error_password_strength"; // Kirim respon "error_password_strength" jika password tidak memenuhi syarat
    exit();
}

// Buat query SQL untuk menambahkan data RT ke dalam database
$query = "INSERT INTO umat (id_kub, nama_lengkap, username, password, alamat, jk, no_hp) 
          VALUES ('$id_kub', '$nama_lengkap', '$username', '$password', '$alamat', '$jk', '$no_hp')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
