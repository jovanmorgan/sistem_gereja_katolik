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

                    // Query untuk mendapatkan data umat dengan pencarian dan pagination
                    $query = "
    SELECT umat.*, kub.nama_kub 
    FROM umat 
    LEFT JOIN kub ON umat.id_kub = kub.id_kub 
    WHERE umat.nama_lengkap LIKE ? OR kub.nama_kub LIKE ? OR umat.jk LIKE ? OR umat.alamat LIKE ? OR umat.username LIKE ? OR umat.no_hp LIKE ? 
    LIMIT ?, ?
";
                    $stmt = $koneksi->prepare($query);
                    $search_param = '%' . $search . '%';
                    $stmt->bind_param("ssssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $offset, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Hitung total halaman
                    $total_query = "
    SELECT COUNT(*) as total 
    FROM umat 
    LEFT JOIN kub ON umat.id_kub = kub.id_kub 
    WHERE umat.nama_lengkap LIKE ? OR kub.nama_kub LIKE ? OR umat.jk LIKE ? OR umat.alamat LIKE ? OR umat.username LIKE ? OR umat.no_hp LIKE ?
";
                    $stmt_total = $koneksi->prepare($total_query);
                    $stmt_total->bind_param("ssssss", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
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
                                        <th style="white-space: nowrap;">Nomor</th>
                                        <th style="white-space: nowrap;">Nama KUB</th>
                                        <th style="white-space: nowrap;">Nama Lengkap</th>
                                        <th style="white-space: nowrap;">Username</th>
                                        <th style="white-space: nowrap;">Password</th>
                                        <th style="white-space: nowrap;">Jenis Kelamin</th>
                                        <th style="white-space: nowrap;">Alamat</th>
                                        <th style="white-space: nowrap;">No HP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $nomor = $offset + 1;
                                        while ($row = $result->fetch_assoc()) :
                                        ?>
                                    <tr>
                                        <td><?php echo $nomor++; ?></td>
                                        <td><?php echo !empty($row['nama_kub']) ? htmlspecialchars($row['nama_kub']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['nama_lengkap']) ? htmlspecialchars($row['nama_lengkap']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['username']) ? htmlspecialchars($row['username']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['password']) ? htmlspecialchars($row['password']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['jk']) ? htmlspecialchars($row['jk']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['alamat']) ? htmlspecialchars($row['alamat']) : 'Data belum dilengkapi'; ?>
                                        </td>
                                        <td><?php echo !empty($row['no_hp']) ? htmlspecialchars($row['no_hp']) : 'Data belum dilengkapi'; ?>
                                        </td>
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