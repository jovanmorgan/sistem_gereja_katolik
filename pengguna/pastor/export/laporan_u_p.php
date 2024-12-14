<?php
// Koneksi ke database
include '../../../keamanan/koneksi.php';

$id_u_p = $_GET['id_u_p']; // Ambil nilai id_u_p dari URL

// Query untuk mengambil data u_p berdasarkan id_u_p yang dikirimkan
$query = "
        SELECT u_p.id_u_p, u_p.status, umat.nama_lengkap, umat.id_umat, u_p.jenis_pelayanan 
        FROM u_p
        JOIN umat ON u_p.id_umat = umat.id_umat
        WHERE u_p.id_u_p = ?"; // Tambahkan kondisi WHERE untuk id_u_p

// Siapkan dan eksekusi statement
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $id_u_p); // Bind parameter id_u_p ke query
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Usulan Permohonan</title>
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

        .status-orange {
            color: orange;
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

        .message.orange {
            background-color: #fff6e6;
            color: orange;
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

    <h1 class="main-title">Laporan Usulan Permohonan</h1>

    <div class="content">
        <p>Berikut ini adalah laporan mengenai usulan permohonan dari umat untuk berbagai jenis pelayanan di Gereja
            Katolik Santa Maria Asumta.</p>

        <div class="section-title">Detail Usulan Permohonan:</div>

        <?php
        // Inisialisasi nomor urut
        while ($row = $result->fetch_assoc()) {
            echo '<table>';
            echo '<tr><td>Nama Lengkap Umat</td><td>:</td><td>' . htmlspecialchars($row['nama_lengkap']) . '</td></tr>';
            echo '<tr><td>Jenis Pelayanan</td><td>:</td><td>' . htmlspecialchars($row['jenis_pelayanan']) . '</td></tr>';
            echo '</table>';

            // Modifikasi pesan berdasarkan status
            if ($row['status'] == 'Telah Disetujui') {
                echo '<p class="status-approved">Status: Telah Disetujui</p>';
                echo '<p class="message approved">Usulan Pelayanan Anda telah disetujui. Silakan tunggu data pelayanan lebih lanjut.</p>';
            } else if ($row['status'] == 'Sementara Diproses') {
                echo '<p class="status-orange">Status: Sementara Diproses</p>';
                echo '<p class="message orange">Usulan Pelayanan Anda Sementara Diproses. Silakan tunggu informasi lebih lanjut.</p>';
            } else {
                echo '<p class="status-rejected">Status: Tidak Disetujui</p>';
                echo '<p class="message rejected">Maaf, usulan pelayanan Anda tidak disetujui.</p>';
            }
        }
        ?>

        <hr>
        <div class="signature">
            <p>Hormat kami,</p>
            <p><strong>Pengurus Gereja Katolik Santa Maria Asumta</strong></p>
        </div>
    </div>

</body>

</html>