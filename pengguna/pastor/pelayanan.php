<?php include 'fitur/penggunah.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'fitur/head.php'; ?>
<?php include 'fitur/nama_halaman.php'; ?>
<?php include 'fitur/nama_halaman_proses.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'fitur/sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'fitur/navbar.php'; ?>
            <div class="container">
                <div class="page-inner">
                    <?php include 'fitur/papan_halaman.php'; ?>

                    <div id="load_data">
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <!-- Search Form -->
                                            <form method="GET" action="">
                                                <div class="input-group mt-3">
                                                    <input type="text" class="form-control" placeholder="Cari Data..."
                                                        name="search"
                                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <button class="btn btn-outline-secondary"
                                                        type="submit">Cari</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php
                        include '../../keamanan/koneksi.php';

                        // Pencarian dan pagination
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data u_p dengan pencarian dan pagination
                        $query = "
                                SELECT p.id_pelayanan, p.id_u_p, p.id_pastor, p.hari_tgl, p.tempat, umat.nama_lengkap AS nama_umat, umat.id_umat, u_p.id_u_p, u_p.jenis_pelayanan, u_p.id_umat, pastor.nama_lengkap AS nama_pastor, pastor.id_pastor
                                FROM pelayanan p
                                JOIN u_p ON p.id_u_p = u_p.id_u_p
                                JOIN umat ON u_p.id_umat = umat.id_umat
                                JOIN pastor ON p.id_pastor = pastor.id_pastor
                                WHERE umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?
                                LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);
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

                        <!-- Tabel Data U_P -->
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body" style="overflow-x: hidden;">
                                            <div style="overflow-x: auto;">
                                                <?php if ($result->num_rows > 0): ?>
                                                <table class="table table-hover text-center mt-3"
                                                    style="border-collapse: separate; border-spacing: 0;">
                                                    <thead>
                                                        <tr>
                                                            <th style="white-space: nowrap;">Nomor</th>
                                                            <th style="white-space: nowrap;">ID Pelayanan</th>
                                                            <th style="white-space: nowrap;">Nama Pastor</th>
                                                            <th style="white-space: nowrap;">Nama Umat</th>
                                                            <th style="white-space: nowrap;">Jenis Pelayanan</th>
                                                            <th style="white-space: nowrap;">Hari & Tanggal</th>
                                                            <th style="white-space: nowrap;">Tempat</th>
                                                            <th style="white-space: nowrap;">Aksi</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                            $nomor = $offset + 1; // Mulai nomor urut dari $offset + 1
                                                            while ($row = $result->fetch_assoc()) :
                                                            ?>
                                                        <tr>
                                                            <td><?php echo $nomor++; ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_pelayanan']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['nama_pastor']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['nama_umat']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['jenis_pelayanan']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['hari_tgl']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tempat']); ?></td>
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="openEditModal('<?php echo $row['id_pelayanan']; ?>', '<?php echo $row['id_pastor']; ?>', '<?php echo $row['id_u_p']; ?>', '<?php echo $row['hari_tgl']; ?>', '<?php echo $row['tempat']; ?>')">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_pelayanan']; ?>')">Hapus</button>
                                                                <a href="export/<?= $page_title_proses ?>?id_pelayanan=<?php echo $row['id_pelayanan']; ?>"
                                                                    class="btn btn-warning btn-sm">Export</a>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                                <?php else: ?>
                                                <p class="text-center mt-4">Data tidak ditemukan.</p>
                                                <?php endif; ?>

                                                <!-- Pagination -->
                                                <nav>
                                                    <ul class="pagination justify-content-center">
                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li
                                                            class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                                            <a class="page-link"
                                                                href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                                        </li>
                                                        <?php endfor; ?>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Pagination Section -->
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <!-- Pagination with icons -->
                                            <nav aria-label="Pagxample" style="margin-top: 2.2rem;">
                                                <ul class="pagination justify-content-center">
                                                    <li class="page-item <?php if ($page <= 1) {
                                                                                echo 'disabled';
                                                                            } ?>">
                                                        <a class="page-link" href="<?php if ($page > 1) {
                                                                                        echo "?page=" . ($page - 1) . "&search=" . $search;
                                                                                    } ?>" aria-label="Previous">
                                                            <span aria-hidden="true">&laquo;</span>
                                                        </a>
                                                    </li>
                                                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                                    <li class="page-item <?php if ($i == $page) {
                                                                                    echo 'active';
                                                                                } ?>">
                                                        <a class="page-link"
                                                            href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                                    </li>
                                                    <?php } ?>
                                                    <li class="page-item <?php if ($page >= $total_pages) {
                                                                                echo 'disabled';
                                                                            } ?>">
                                                        <a class="page-link" href="<?php if ($page < $total_pages) {
                                                                                        echo "?page=" . ($page + 1) . "&search=" . $search;
                                                                                    } ?>" aria-label="Next">
                                                            <span aria-hidden="true">&raquo;</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <!-- End Pagination with icons -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!-- bagian pop up edit dan tambah -->

            <!-- Modal -->
            <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tambahDataModalLabel">Tambah <?= $page_title ?></h5>
                            <button type="button" class="btn-close" id="closeTambahModal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="tambahForm" method="POST" action="proses/<?= $page_title_proses ?>/tambah.php"
                                enctype="multipart/form-data">

                                <input type="hidden" name="id_pastor" id="id_pastor" value="<?php echo $id_pastor; ?>">

                                <div class="mb-3">
                                    <label for="id_u_p" class="form-label">Usulan Pelayanan</label>
                                    <select id="id_u_p" name="id_u_p" class="form-select" required>
                                        <option value="" disabled selected>Pilih Usulan Pelayanan</option>
                                        <?php
                                        // Ambil data u_p dari database
                                        $query_u_p = "SELECT u_p.id_u_p, u_p.id_umat, u_p.jenis_pelayanan, umat.nama_lengkap FROM u_p
                                        JOIN umat ON u_p.id_umat = umat.id_umat"; // Ganti dengan query yang sesuai
                                        $result_u_p = mysqli_query($koneksi, $query_u_p);
                                        while ($row_u_p = mysqli_fetch_assoc($result_u_p)) {
                                            echo '<option value="' . $row_u_p['id_u_p'] . '">' . $row_u_p['nama_lengkap'] . ' (' . $row_u_p['jenis_pelayanan'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hari_tgl" class="form-label">Hari/Tanggal</label>
                                    <input type="date" id="hari_tgl" name="hari_tgl" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tempat" class="form-label">Tempat</label>
                                    <textarea name="tempat" class="form-control" id="tempat"></textarea>
                                </div>

                                <!-- Wrapper for the submit button to align it to the right -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDataModalLabel">Edit <?= $page_title ?></h5>
                            <button type="button" class="btn-close" id="closeEditModal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" method="POST" action="proses/<?= $page_title_proses ?>/edit.php"
                                enctype="multipart/form-data">
                                <input type="hidden" id="edit_id" name="id_pelayanan">

                                <input type="hidden" name="id_pastor" id="edit_id_pastor">

                                <div class="mb-3">
                                    <label for="edit_id_u_p" class="form-label">Usulan Pelayanan</label>
                                    <select id="edit_id_u_p" name="id_u_p" class="form-select" required>
                                        <option value="" disabled selected>Pilih Usulan Pelayanan</option>
                                        <?php
                                        // Ambil data u_p dari database
                                        $query_u_p = "SELECT u_p.id_u_p, u_p.id_umat, u_p.jenis_pelayanan, umat.nama_lengkap FROM u_p
                                        JOIN umat ON u_p.id_umat = umat.id_umat"; // Ganti dengan query yang sesuai
                                        $result_u_p = mysqli_query($koneksi, $query_u_p);
                                        while ($row_u_p = mysqli_fetch_assoc($result_u_p)) {
                                            echo '<option value="' . $row_u_p['id_u_p'] . '">' . $row_u_p['nama_lengkap'] . ' (' . $row_u_p['jenis_pelayanan'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_hari_tgl" class="form-label">Hari/Tanggal</label>
                                    <input type="date" id="edit_hari_tgl" name="hari_tgl" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_tempat" class="form-label">Tempat</label>
                                    <textarea name="tempat" class="form-control" id="edit_tempat"></textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'fitur/footer.php'; ?>
        </div>
    </div>
    <script>
    function openEditModal(id, id_pastor, id_u_p, hari_tgl, tempat) {
        let editModal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_id_pastor').value = id_pastor;
        document.getElementById('edit_id_u_p').value = id_u_p;
        document.getElementById('edit_hari_tgl').value = hari_tgl;
        document.getElementById('edit_tempat').value = tempat;
        editModal.show();
    }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>