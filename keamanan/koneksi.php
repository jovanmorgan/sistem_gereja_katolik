<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "gereja_katolik";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
	die("Koneksi Gagal:" . mysqli_connect_error());
}
