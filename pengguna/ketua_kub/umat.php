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

                        // Ambil id_kub berdasarkan id_ketua_kub
                        $query_kub = "
SELECT id_kub 
FROM ketua_kub 
WHERE id_ketua_kub = ?";
                        $stmt_kub = $koneksi->prepare($query_kub);
                        $stmt_kub->bind_param("s", $id_ketua_kub);
                        $stmt_kub->execute();
                        $result_kub = $stmt_kub->get_result();
                        $id_kub = null;

                        // Pastikan id_kub ditemukan
                        if ($row_kub = $result_kub->fetch_assoc()) {
                            $id_kub = $row_kub['id_kub'];
                        }

                        // Jika tidak ditemukan id_kub, arahkan pengguna kembali atau tangani sesuai kebutuhan
                        if (!$id_kub) {
                            header("Location: ../../berlangganan/login"); // Atau tampilkan pesan kesalahan
                            exit;
                        }

                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data umat dengan pencarian dan pagination
                        $query = "
    SELECT umat.*, kub.nama_kub 
    FROM umat 
    LEFT JOIN kub ON umat.id_kub = kub.id_kub 
    WHERE umat.id_kub = ? AND (umat.nama_lengkap LIKE ? OR kub.nama_kub LIKE ? OR umat.jk LIKE ? OR umat.alamat LIKE ? OR umat.username LIKE ? OR umat.no_hp LIKE ?) 
    LIMIT ?, ?
";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("sssssssii", $id_kub, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $offset, $limit);
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
                                                            <th style="white-space: nowrap;">Nama KUB</th>
                                                            <th style="white-space: nowrap;">Nama Lengkap</th>
                                                            <th style="white-space: nowrap;">Username</th>
                                                            <th style="white-space: nowrap;">Password</th>
                                                            <th style="white-space: nowrap;">Jenis Kelamin</th>
                                                            <th style="white-space: nowrap;">Alamat</th>
                                                            <th style="white-space: nowrap;">No HP</th>
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
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="openEditModal(
                                                        '<?php echo $row['id_umat']; ?>', '<?php echo $row['id_kub']; ?>', '<?php echo $row['nama_lengkap']; ?>', '<?php echo $row['jk']; ?>', '<?php echo $row['alamat']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['password']; ?>', '<?php echo $row['no_hp']; ?>')">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_umat']; ?>')">Hapus</button>
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



                                <div class="mb-3">
                                    <label for="id_kub" class="form-label">KUB</label>
                                    <select id="id_kub" name="id_kub" class="form-select" required>
                                        <option value="" disabled selected>Pilih KUB</option>
                                        <?php
                                        // Ambil data kub dari database
                                        $query_kub = "SELECT id_kub, nama_kub FROM kub"; // Ganti dengan query yang sesuai
                                        $result_kub = mysqli_query($koneksi, $query_kub);
                                        while ($row_kub = mysqli_fetch_assoc($result_kub)) {
                                            echo '<option value="' . $row_kub['id_kub'] . '">' . $row_kub['nama_kub'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="jk" class="form-label">Jenis Kelamin</label>
                                    <select id="jk" name="jk" class="form-select" required>
                                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="alamat"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" id="password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP</label>
                                    <input type="number" id="no_hp" name="no_hp" class="form-control" required>
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
                                <input type="hidden" id="edit_id" name="id_umat">


                                <div class="mb-3">
                                    <label for="edit_id_kub" class="form-label">KUB</label>
                                    <select id="edit_id_kub" name="id_kub" class="form-select" required>
                                        <option value="" disabled selected>Pilih KUB</option>
                                        <?php
                                        // Ambil data kub dari database
                                        $query_kub = "SELECT id_kub, nama_kub FROM kub"; // Ganti dengan query yang sesuai
                                        $result_kub = mysqli_query($koneksi, $query_kub);
                                        while ($row_kub = mysqli_fetch_assoc($result_kub)) {
                                            echo '<option value="' . $row_kub['id_kub'] . '">' . $row_kub['nama_kub'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_jk" class="form-label">Jenis Kelamin</label>
                                    <select id="edit_jk" name="jk" class="form-select" required>
                                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_alamat" class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="edit_alamat"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_username" class="form-label">Username</label>
                                    <input type="text" id="edit_username" name="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_password" class="form-label">Password</label>
                                    <input type="text" id="edit_password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_no_hp" class="form-label">Nomor HP</label>
                                    <input type="number" id="edit_no_hp" name="no_hp" class="form-control" required>
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
    function openEditModal(id, id_kub, nama_lengkap, jk, alamat, username, password, no_hp) {
        let editModal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_id_kub').value = id_kub;
        document.getElementById('edit_nama_lengkap').value = nama_lengkap;
        document.getElementById('edit_jk').value = jk;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_password').value = password;
        document.getElementById('edit_no_hp').value = no_hp;
        editModal.show();
    }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>