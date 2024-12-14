<?php
// Ambil data bantuan berdasarkan id_bantuan
include '../../../keamanan/koneksi.php';

$id_bantuan = isset($_GET['id_bantuan']) ? $_GET['id_bantuan'] : '';
$query = "
    SELECT btn.id_bantuan, kk.no_kk, kk.nama_kep_kel, 
           GROUP_CONCAT(p.nama SEPARATOR ', ') AS nama_penduduk, 
           btn.nama_bantuan 
    FROM bantuan btn
    JOIN kk ON btn.no_kk = kk.no_kk
    JOIN penduduk p ON btn.no_kk = p.no_kk
    WHERE btn.id_bantuan = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id_bantuan);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data dari database
if ($result->num_rows > 0) {
    $dataBantuan = $result->fetch_assoc();
} else {
    die("Data bantuan tidak ditemukan.");
}

// Ambil data lurah dengan id_lurah = 1
$queryLurah = "SELECT * FROM lurah WHERE id_lurah = 1";
$resultLurah = $koneksi->query($queryLurah);
$dataLurah = $resultLurah->fetch_assoc();

// Ambil konten template surat
$tabelHtml = file_get_contents('bantuan.html');

// Gantikan placeholder dengan data dari database
$htmlContent = str_replace(
    [
        '[No KK]',
        '[Nama Kepala Keluarga]',
        '[Nama Anggota Keluarga]',
        '[Nama Bantuan]',
        '[Tanggal Surat]',
        '[Kantor Lurah]',
        '[Nama Lurah]'
    ],
    [
        htmlspecialchars($dataBantuan['no_kk']),
        htmlspecialchars($dataBantuan['nama_kep_kel']),
        nl2br(str_replace(',', ' , ', htmlspecialchars($dataBantuan['nama_penduduk']))),
        htmlspecialchars($dataBantuan['nama_bantuan']),
        date('d M Y'),
        'Oepura',
        htmlspecialchars($dataLurah['nama_lurah'])
    ],
    $tabelHtml
);

// Buat file HTML sementara di folder sistem
$tmpHtmlFile = tempnam(sys_get_temp_dir(), 'html') . '.html';
file_put_contents($tmpHtmlFile, $htmlContent);

// Nama file output PDF (gunakan direktori sementara untuk menyimpan PDF)
$outputFile = sys_get_temp_dir() . '/hasil_bantuan.pdf';

// Jalankan perintah wkhtmltopdf dan tangkap output/error
$command = "C:/xampp/htdocs/gereja_katolik/wkhtmltopdf/bin/wkhtmltopdf $tmpHtmlFile $outputFile";
exec($command, $output, $return_var);

// Debugging: cek output wkhtmltopdf
if ($return_var != 0) {
    echo "Gagal menghasilkan PDF. Error: <pre>" . print_r($output, true) . "</pre>";
    unlink($tmpHtmlFile); // Hapus file HTML sementara
    exit;
}

// Hapus file HTML sementara
unlink($tmpHtmlFile);

// Cek apakah file PDF benar-benar ada
if (file_exists($outputFile)) {
    // Kirim PDF ke browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="hasil_bantuan.pdf"');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
} else {
    echo "File PDF tidak ditemukan.";
}
