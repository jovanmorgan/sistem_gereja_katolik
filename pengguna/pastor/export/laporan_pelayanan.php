<?php
include '../../../keamanan/koneksi.php';

function tanggalIndo($tanggal)
{
    $hariArray = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );

    $bulanArray = array(
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    );

    $hariInggris = date('l', strtotime($tanggal)); // Mengambil hari dalam bahasa Inggris
    $bulanInggris = date('F', strtotime($tanggal)); // Mengambil bulan dalam bahasa Inggris

    $hari = $hariArray[$hariInggris]; // Mengubah ke bahasa Indonesia
    $bulan = $bulanArray[$bulanInggris]; // Mengubah ke bahasa Indonesia
    $tanggalFormatted = date('d', strtotime($tanggal)); // Mengambil tanggal (hari angka)
    $tahun = date('Y', strtotime($tanggal)); // Mengambil tahun

    return $hari . ', ' . $tanggalFormatted . ' ' . $bulan . ' ' . $tahun;
}

// Pencarian dan pagination
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$id_pelayanan = $_GET['id_pelayanan']; // Ambil nilai id_pelayanan dari URL

// Query untuk mendapatkan data pelayanan berdasarkan id_pelayanan
$query = "
        SELECT p.id_pelayanan, p.id_u_p, p.id_pastor, p.hari_tgl, p.tempat, umat.nama_lengkap AS nama_umat, umat.id_umat, u_p.id_u_p, u_p.jenis_pelayanan, u_p.id_umat, pastor.nama_lengkap AS nama_pastor, pastor.id_pastor, u_p.status
        FROM pelayanan p
        JOIN u_p ON p.id_u_p = u_p.id_u_p
        JOIN umat ON u_p.id_umat = umat.id_umat
        JOIN pastor ON p.id_pastor = pastor.id_pastor
        WHERE p.id_pelayanan = ?"; // Filter berdasarkan id_pelayanan

// Siapkan dan eksekusi statement
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pelayanan); // Bind parameter id_pelayanan ke query
$stmt->execute();
$result = $stmt->get_result();

// Hitung total halaman
$total_query = "
    SELECT COUNT(*) as total 
    FROM pelayanan p
    JOIN u_p ON p.id_u_p = u_p.id_u_p
    JOIN umat ON u_p.id_umat = umat.id_umat
    JOIN pastor ON p.id_pastor = pastor.id_pastor
    WHERE umat.nama_lengkap LIKE ? OR pastor.nama_lengkap LIKE ?";
$stmt_total = $koneksi->prepare($total_query);
$stmt_total->bind_param("ss", $search_param, $search_param);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelayanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            background-color: #f9f9f9;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-left {
            display: flex;
            flex-direction: column;
        }

        .header-left h2 {
            font-size: 18pt;
            margin: 0;
        }

        .header-left p {
            margin: 5px 0;
            font-size: 12pt;
        }

        .header img {
            width: 100px;
            height: auto;
            max-width: 80%;
            position: absolute;
            top: 0px;
            right: 0px;
        }

        .header-line {
            border-top: 2px solid #333;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .main-title {
            text-align: center;
            font-size: 22pt;
            margin-top: 10px;
            font-weight: bold;
        }

        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        table td {
            padding: 8px;
            vertical-align: top;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-style: italic;
        }

        .message.approved {
            background-color: #e6ffed;
            color: green;
        }

        .message.rejected {
            background-color: #ffe6e6;
            color: red;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
        }

        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #ccc;
        }

        .signature {
            margin-top: 50px;
        }

        .pastor-name {
            display: inline-block;
            border-bottom: 1px solid black;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-left">
            <br>
            <h2>GEREJA SANTA MARIA ASUMTA</h2>
            <p>Jl. Perintis Kemerdekaan. No. 9 Kayu Putih Kec. Oebobo Kota Kupang</p>
            <div class="header-line"></div>
        </div>
        <img src="http://localhost/gereja_katolik/assets/img/gereja/logo_gereja.png" alt="Logo Gereja" />
    </div>

    <h1 class="main-title">Laporan Pelayanan</h1>

    <div class="content">
        <p>Berikut ini adalah laporan mengenai Pelayanan dari umat untuk berbagai jenis pelayanan di Gereja
            Katolik Santa Maria Asumta.</p>

        <div class="section-title">Detail Pelayanan:</div>

        <?php
        while ($row = $result->fetch_assoc()) {
            $hariTanggal = tanggalIndo($row['hari_tgl']);

            echo '<table>';
            echo '<tr><td>Nama Lengkap Umat</td><td>:</td><td>' . htmlspecialchars($row['nama_umat']) . '</td></tr>';
            echo '<tr><td>Jenis Pelayanan</td><td>:</td><td>' . htmlspecialchars($row['jenis_pelayanan']) . '</td></tr>';
            echo '<tr><td>Hari / Tanggal</td><td>:</td><td>' . htmlspecialchars($hariTanggal) . '</td></tr>';
            echo '<tr><td>Tempat</td><td>:</td><td>' . htmlspecialchars($row['tempat']) . '</td></tr>';
            echo '</table>';

            echo '
            <hr>
            <div class="signature">
                <p>Kupang, ......,......,......</p>
                <p>Kepada Pastor</p>
                <br />
                <p class="pastor-name">' . htmlspecialchars($row['nama_pastor']) . '</p>
            </div>
            ';
        }
        ?>
    </div>
</body>

</html>