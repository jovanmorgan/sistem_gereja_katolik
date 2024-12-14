<?php
// Ambil data penduduk_tidak_mampu berdasarkan id_ptm
include '../../../keamanan/koneksi.php';

$id_ptm = isset($_GET['id_ptm']) ? $_GET['id_ptm'] : '';
$query = "
    SELECT ptm.id_ptm, kk.no_kk, kk.nama_kep_kel, 
           GROUP_CONCAT(p.nama SEPARATOR ', ') AS nama_penduduk, 
           ptm.status 
    FROM penduduk_tidak_mampu ptm
    JOIN kk ON ptm.no_kk = kk.no_kk
    JOIN penduduk p ON ptm.no_kk = p.no_kk
    WHERE ptm.id_ptm = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id_ptm);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data dari database
if ($result->num_rows > 0) {
    $dataPenduduk = $result->fetch_assoc();
} else {
    die("Data penduduk tidak ditemukan.");
}

// Ambil data lurah dengan id_lurah = 1
$queryLurah = "SELECT * FROM lurah WHERE id_lurah = 1";
$resultLurah = $koneksi->query($queryLurah);
$dataLurah = $resultLurah->fetch_assoc();

// Ambil konten template surat
$tabelHtml = file_get_contents('penduduk_tidak_mampu.html');

// Gantikan placeholder dengan data dari database
$statusMessage = '';

if (htmlspecialchars($dataPenduduk['status']) === 'Disetujui') {
    $statusMessage = 'Dengan ini, kami informasikan bahwa keluarga Anda telah disetujui sebagai keluarga kurang mampu dan berhak mendapatkan bantuan yang telah disediakan oleh pemerintah. Kami berharap bantuan ini dapat meringankan beban dan mendukung kesejahteraan keluarga Anda.';
} else {
    $statusMessage = 'Status permohonan keluarga Anda saat ini belum disetujui. Mohon untuk menunggu informasi lebih lanjut.';
}

$htmlContent = str_replace(
    [
        '[No KK]',
        '[Nama Kepala Keluarga]',
        '[Nama Anggota Keluarga]',
        '[Status]',
        '[Data Status]',
        '[Kantor Lurah]',
        '[Nama Kecamatan]',
        '[Nama Kabupaten]',
        '[Nomor Surat]',
        '[Tanggal Surat]',
        '[Nama Lurah]',
        '[Username Lurah]'
    ],
    [
        htmlspecialchars($dataPenduduk['no_kk']),
        htmlspecialchars($dataPenduduk['nama_kep_kel']),
        nl2br(str_replace(',', ' , ', htmlspecialchars($dataPenduduk['nama_penduduk']))),  // Memisahkan nama anggota keluarga dengan " - "
        $statusMessage,  // Mengganti placeholder status dengan pesan status
        htmlspecialchars($dataPenduduk['status']),
        'Oepura',
        'Kecamatan Maulafa',
        'Kota Kupang',
        '..../..../......',
        date('d M Y'),
        htmlspecialchars($dataLurah['nama_lurah']),
        htmlspecialchars($dataLurah['username'])
    ],
    $tabelHtml
);

// Buat file HTML sementara di folder sistem
$tmpHtmlFile = tempnam(sys_get_temp_dir(), 'html') . '.html';
file_put_contents($tmpHtmlFile, $htmlContent);

// Nama file output PDF (gunakan direktori sementara untuk menyimpan PDF)
$outputFile = sys_get_temp_dir() . '/hasil_tidak_mampu.pdf';

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
    header('Content-Disposition: inline; filename="hasil_tidak_mampu.pdf"');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
} else {
    echo "File PDF tidak ditemukan.";
}
