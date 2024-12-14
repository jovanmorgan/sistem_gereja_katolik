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
                                                    <input type="text" class="form-control"
                                                        placeholder="Cari Ketua KUB..." name="search"
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

                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data ketua_kub dengan pencarian dan pagination
                        $query = "
        SELECT ketua_kub.*, kub.nama_kub 
        FROM ketua_kub 
        LEFT JOIN kub ON ketua_kub.id_kub = kub.id_kub 
        WHERE ketua_kub.nama_lengkap LIKE ? OR kub.nama_kub LIKE ? OR ketua_kub.username LIKE ? 
        LIMIT ?, ?
    ";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman
                        $total_query = "
        SELECT COUNT(*) as total 
        FROM ketua_kub 
        LEFT JOIN kub ON ketua_kub.id_kub = kub.id_kub 
        WHERE ketua_kub.nama_lengkap LIKE ? OR kub.nama_kub LIKE ? OR ketua_kub.username LIKE ?
    ";
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("sss", $search_param, $search_param, $search_param);
                        $stmt_total->execute();
                        $total_result = $stmt_total->get_result();
                        $total_row = $total_result->fetch_assoc();
                        $total_pages = ceil($total_row['total'] / $limit);
                        ?>

                        <!-- Tabel Data Ketua KUB -->
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
                                                            <th>Nomor</th>
                                                            <th>Nama KUB</th>
                                                            <th>Nama Lengkap</th>
                                                            <th>Username</th>
                                                            <th>Password</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $nomor = $offset + 1;
                                                            while ($row = $result->fetch_assoc()) :
                                                            ?>
                                                        <tr>
                                                            <td><?php echo $nomor++; ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_kub']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['password']); ?></td>
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="openEditModal('<?php echo $row['id_ketua_kub']; ?>', '<?php echo $row['id_kub']; ?>', '<?php echo $row['nama_lengkap']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['password']; ?>')">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_ketua_kub']; ?>')">Hapus</button>
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
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" id="password" name="password" class="form-control" required>
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
                                <input type="hidden" id="edit_id" name="id_ketua_kub">


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
                                    <label for="edit_username" class="form-label">Username</label>
                                    <input type="text" id="edit_username" name="username" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_password" class="form-label">Password</label>
                                    <input type="text" id="edit_password" name="password" class="form-control" required>
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
    function openEditModal(id, id_kub, nama_lengkap, username, password) {
        let editModal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_id_kub').value = id_kub;
        document.getElementById('edit_nama_lengkap').value = nama_lengkap;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_password').value = password;
        editModal.show();
    }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>