<?php include '../fitur/nama_halaman.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_export.php'; ?>

<body translate="no">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h3 class="text-center">Data Export <?= $page_title ?> </h3>
                    </div>
                    <?php
                    // Ambil data checkout dari database
                    include '../../../keamanan/koneksi.php';
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    // Query untuk mendapatkan data penduduk dengan pencarian dan pagination
                    $query = "
                            SELECT p.id_penduduk, p.nik, p.nama, p.tpt_lahir, p.tgl_lahir, p.jk, p.alamat, 
                                p.kel_des, p.kec, p.kewarganegaraan, p.agama, p.stts_perkawinan, 
                                p.pendidikan, p.pekerjaan, p.status, 
                                kk.no_kk, rw.nama_rw, rw.id_rw, rt.nama_rt, rt.id_rt
                            FROM penduduk p 
                            JOIN kk ON p.no_kk = kk.no_kk 
                            JOIN rw ON p.id_rw = rw.id_rw 
                            JOIN rt ON p.id_rt = rt.id_rt 
                            WHERE p.nik LIKE ? OR p.nama LIKE ? 
                            LIMIT ?, ?";
                    $stmt = $koneksi->prepare($query);
                    $search_param = '%' . $search . '%';
                    $stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Hitung total halaman
                    $total_query = "
                                SELECT COUNT(*) as total 
                                FROM penduduk p 
                                JOIN kk ON p.no_kk = kk.no_kk 
                                JOIN rw ON p.id_rw = rw.id_rw 
                                JOIN rt ON p.id_rt = rt.id_rt 
                                WHERE p.nik LIKE ? OR p.nama LIKE ?";
                    $stmt_total = $koneksi->prepare($total_query);
                    $stmt_total->bind_param("ss", $search_param, $search_param);
                    $stmt_total->execute();
                    $total_result = $stmt_total->get_result();
                    $total_row = $total_result->fetch_assoc();
                    $total_pages = ceil($total_row['total'] / $limit);
                    ?>

                    <div class="card-body">
                        <div class="table-responsive">

                            <?php if ($result->num_rows > 0): ?>
                            <table id="example" class="table table-hover text-center mt-3"
                                style="border-collapse: separate; border-spacing: 0;">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Nomor KK</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Tempat Lahir</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Alamat</th>
                                        <th>RT</th>
                                        <th>RW</th>
                                        <th>Kel/Desa</th>
                                        <th>Kecamatan</th>
                                        <th>Kewarganegaraan</th>
                                        <th>Agama</th>
                                        <th>Status Perkawinan</th>
                                        <th>Pendidikan</th>
                                        <th>Pekerjaan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $nomor = $offset + 1;
                                        while ($row = $result->fetch_assoc()) :
                                        ?>
                                    <tr>
                                        <td><?php echo $nomor++; ?></td>
                                        <td><?php echo htmlspecialchars($row['no_kk']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tpt_lahir']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tgl_lahir']); ?></td>
                                        <td><?php echo htmlspecialchars($row['jk']); ?></td>
                                        <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_rt']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_rw']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kel_des']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kec']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kewarganegaraan']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['agama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['stts_perkawinan']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['pendidikan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pekerjaan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <?php else: ?>
                            <p class="text-center mt-4">Data tidak ditemukan 😖.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Pagination -->
                </div>
            </div>
        </div>
    </div>

    <?php include '../fitur/js_export.php'; ?>

</body>

</html>