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

                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data rt dengan pencarian dan pagination
                        $query = "SELECT * FROM kub WHERE rt LIKE ? OR rw LIKE ? OR nama_kub LIKE ? OR alamat LIKE ? LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman
                        $total_query = "SELECT COUNT(*) as total FROM kub WHERE rt LIKE ? OR rw LIKE ? OR nama_kub LIKE ? OR alamat LIKE ?";
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
                        $stmt_total->execute();
                        $total_result = $stmt_total->get_result();
                        $total_row = $total_result->fetch_assoc();
                        $total_pages = ceil($total_row['total'] / $limit);
                        ?>

                        <!-- Tabel Data RT -->
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
                                                            <th style="white-space: nowrap;">ID KUB</th>
                                                            <th style="white-space: nowrap;">Nama RT</th>
                                                            <th style="white-space: nowrap;">Nama RW</th>
                                                            <th style="white-space: nowrap;">Nama KUB</th>
                                                            <th style="white-space: nowrap;">Alamat</th>
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
                                                            <td><?php echo htmlspecialchars($row['id_kub']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['rt']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['rw']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['nama_kub']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm"
                                                                    onclick="openEditModal('<?php echo $row['id_kub']; ?>', '<?php echo $row['nama_kub']; ?>', '<?php echo addslashes($row['rt']); ?>', '<?php echo addslashes($row['rw']); ?>', '<?php echo addslashes($row['alamat']); ?>')">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_kub']; ?>')">Hapus</button>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                                <?php else: ?>
                                                <p class="text-center mt-4">Data tidak ditemukan.</p>
                                                <?php endif; ?>
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
                                    <label for="rt" class="form-label">Nama RT</label>
                                    <input type="number" min="1" id="rt" name="rt" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="rw" class="form-label">Nama RW</label>
                                    <input type="number" min="1" id="rw" name="rw" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_kub" class="form-label">Nama KUB</label>
                                    <input type="text" id="nama_kub" name="nama_kub" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="alamat"></textarea>
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
                                <input type="hidden" id="edit_id" name="id_kub">

                                <div class="mb-3">
                                    <label for="rt" class="form-label">Nama RT</label>
                                    <input type="number" min="1" id="edit_rt" name="rt" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="rw" class="form-label">Nama RW</label>
                                    <input type="number" min="1" id="edit_rw" name="rw" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_kub" class="form-label">Nama KUB</label>
                                    <input type="text" id="edit_nama_kub" name="nama_kub" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_alamat" class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" id="edit_alamat"></textarea>
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
    function openEditModal(id, nama_kub, rt, rw, alamat) {
        let editModal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama_kub').value = nama_kub;
        document.getElementById('edit_rt').value = rt;
        document.getElementById('edit_rw').value = rw;
        document.getElementById('edit_alamat').value = alamat;
        editModal.show();
    }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>