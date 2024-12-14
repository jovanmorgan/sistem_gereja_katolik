<?php
// Ambil data kematian berdasarkan id_kematian
include '../../../keamanan/koneksi.php';

$id_kematian = isset($_GET['id_kematian']) ? $_GET['id_kematian'] : '';
$query = "
    SELECT kmtn.id_kematian, p.no_kk, p.nik, p.nama, p.tpt_lahir, p.tgl_lahir, 
           p.jk, p.alamat, p.agama, kmtn.tpt_kematian, 
           kmtn.tgl_kematian, kmtn.tgl_kubur, kmtn.tpt_kubur,
           rt.nama_rt, rw.nama_rw
    FROM kematian kmtn
    JOIN penduduk p ON kmtn.id_penduduk = p.id_penduduk
    LEFT JOIN rt ON p.id_rt = rt.id_rt
    LEFT JOIN rw ON p.id_rw = rw.id_rw
    WHERE kmtn.id_kematian = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $id_kematian);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data dari database
if ($result->num_rows > 0) {
    $dataKematian = $result->fetch_assoc();
} else {
    die("Data kematian tidak ditemukan.");
}

// Ambil data lurah dengan id_lurah = 1
$queryLurah = "SELECT * FROM lurah WHERE id_lurah = 1";
$resultLurah = $koneksi->query($queryLurah);
$dataLurah = $resultLurah->fetch_assoc();

// Ambil konten template surat
$tabelHtml = file_get_contents('kematian.html');

// Gantikan placeholder dengan data dari database
$htmlContent = str_replace(
    [
        '[No KK]',
        '[NIK]',
        '[Nama]',
        '[Tempat Lahir]',
        '[Tanggal Lahir]',
        '[Jenis Kelamin]',
        '[Tanggal Kematian]',
        '[Tempat Kematian]',
        '[Tanggal Kubur]',
        '[Tempat Kubur]',
        '[Tanggal Surat]',
        '[Kantor Lurah]',
        '[Nama Lurah]'
    ],
    [
        htmlspecialchars($dataKematian['no_kk']),
        htmlspecialchars($dataKematian['nik']),
        htmlspecialchars($dataKematian['nama']),
        htmlspecialchars($dataKematian['tpt_lahir']),
        htmlspecialchars(date('d M Y', strtotime($dataKematian['tgl_lahir']))),
        htmlspecialchars($dataKematian['jk']),
        htmlspecialchars(date('d M Y', strtotime($dataKematian['tgl_kematian']))),
        htmlspecialchars($dataKematian['tpt_kematian']),
        htmlspecialchars(date('d M Y', strtotime($dataKematian['tgl_kubur']))),
        htmlspecialchars($dataKematian['tpt_kubur']),
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
$outputFile = sys_get_temp_dir() . '/hasil_kematian.pdf';

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
    header('Content-Disposition: inline; filename="hasil_kematian.pdf"');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
} else {
    echo "File PDF tidak ditemukan.";
}
