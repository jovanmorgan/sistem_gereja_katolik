<?php
include 'koneksi.php';

function checkpenggunahType($username)
{
    global $koneksi;
    $query_admin = "SELECT * FROM admin WHERE username = '$username'";
    $query_ketua_kub = "SELECT * FROM ketua_kub WHERE username = '$username'";
    $query_pastor = "SELECT * FROM pastor WHERE username = '$username'";
    $query_umat = "SELECT * FROM umat WHERE username = '$username'";

    $result_admin = mysqli_query($koneksi, $query_admin);
    $result_ketua_kub = mysqli_query($koneksi, $query_ketua_kub);
    $result_pastor = mysqli_query($koneksi, $query_pastor);
    $result_umat = mysqli_query($koneksi, $query_umat);

    if (mysqli_num_rows($result_admin) > 0) {
        return "admin";
    } elseif (mysqli_num_rows($result_ketua_kub) > 0) {
        return "ketua_kub";
    } elseif (mysqli_num_rows($result_pastor) > 0) {
        return "pastor";
    } elseif (mysqli_num_rows($result_umat) > 0) {
        return "umat";
    } else {
        return "not_found";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lakukan validasi data
    if (empty($username) && empty($password)) {
        echo "tidak_ada_data";
        exit();
    }
    if (empty($username)) {
        echo "username_tidak_ada";
        exit();
    }

    if (empty($password)) {
        echo "password_tidak_ada";
        exit();
    }


    $penggunahType = checkpenggunahType($username);
    if ($penggunahType !== "not_found") {
        $query_penggunah = "SELECT * FROM $penggunahType WHERE username = '$username'";
        $result_penggunah = mysqli_query($koneksi, $query_penggunah);

        if (mysqli_num_rows($result_penggunah) > 0) {
            $row = mysqli_fetch_assoc($result_penggunah);
            $hashed_password = $row['password'];

            if ($password === $hashed_password) {

                // Process login for other penggunah types
                session_start();
                $_SESSION['username'] = $username;

                switch ($penggunahType) {
                    case "admin":
                        $_SESSION['id_admin'] = $row['id_admin'];
                        break;
                    case "ketua_kub":
                        $_SESSION['id_ketua_kub'] = $row['id_ketua_kub'];
                        $id_ketua_kub = $row['id_ketua_kub'];
                        break;
                    case "pastor":
                        $_SESSION['id_pastor'] = $row['id_pastor'];
                        break;
                    case "umat":
                        $_SESSION['id_umat'] = $row['id_umat'];
                        $id_umat = $row['id_umat'];
                        break;
                    default:
                        break;
                }

                // Success response
                switch ($penggunahType) {
                    case "admin":
                        echo "success:" . $username . ":" . $penggunahType . ":" . "../pengguna/admin/";
                        break;
                    case "ketua_kub":
                        echo "success:" . $username . ":" . $penggunahType . ":" . "../pengguna/ketua_kub/";
                        break;
                    case "pastor":
                        echo "success:" . $username . ":" . $penggunahType . ":" . "../pengguna/pastor/";
                        break;
                    case "umat":
                        echo "success:" . $username . ":" . $penggunahType . ":" . "../pengguna/umat/";
                        break;
                    default:
                        echo "success:" . $username . ":" . $penggunahType . ":" . "../berlangganan/login";
                        break;
                }
            } else {
                echo "error_password";
            }
        } else {
            echo "error_username";
        }
    } else {
        echo "error_username";
    }
}
