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

                        // Pencarian dan pagination
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data u_p dengan pencarian dan pagination
                        $query = "
SELECT u_p.id_u_p, u_p.status, umat.nama_lengkap, umat.id_umat, u_p.jenis_pelayanan 
FROM u_p
JOIN umat ON u_p.id_umat = umat.id_umat
WHERE umat.id_kub = ? AND (umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?)
LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';

                        // Perhatikan bahwa tipe data untuk bind_param adalah "ssiii" karena id_kub adalah string, sedangkan offset dan limit adalah integer
                        $stmt->bind_param("ssiii", $id_kub, $search_param, $search_param, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman
                        $total_query = "
SELECT COUNT(*) as total 
FROM u_p
JOIN umat ON u_p.id_umat = umat.id_umat
WHERE umat.id_kub = ? AND (umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?)";
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("sss", $id_kub, $search_param, $search_param);
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
                                                                <th style="white-space: nowrap;">ID Usulan Pelayanan</th>
                                                                <th style="white-space: nowrap;">Nama Lengkap</th>
                                                                <th style="white-space: nowrap;">Jenis Pelayanan</th>
                                                                <th style="white-space: nowrap;">Status</th>
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
                                                                    <td><?php echo htmlspecialchars($row['id_u_p']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['jenis_pelayanan']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                                    <td
                                                                        style="display: flex; justify-content: center; gap: 10px;">
                                                                        <a href="export/<?= $page_title_proses ?>?id_u_p=<?php echo $row['id_u_p']; ?>"
                                                                            class="btn btn-warning btn-sm">Export</a>
                                                                        <button class="btn btn-info btn-sm"
                                                                            onclick="validasi('<?php echo $row['id_u_p']; ?>', '<?php echo $row['status']; ?>')">Validasi</button>
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

            <!-- bagian pop up edit dan validasi -->

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
                                    <label for="id_umat" class="form-label">Umat</label>
                                    <select id="id_umat" name="id_umat" class="form-select" required>
                                        <option value="" disabled selected>Pilih umat</option>
                                        <?php
                                        // Ambil data umat dari database
                                        $query_umat = "SELECT id_umat, nama_lengkap FROM umat"; // Ganti dengan query yang sesuai
                                        $result_umat = mysqli_query($koneksi, $query_umat);
                                        while ($row_umat = mysqli_fetch_assoc($result_umat)) {
                                            echo '<option value="' . $row_umat['id_umat'] . '">' . $row_umat['nama_lengkap'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_pelayanan" class="form-label">Jenis Pelayanan</label>
                                    <select id="jenis_pelayanan" name="jenis_pelayanan" class="form-select" required>
                                        <option value="" disabled selected>Pilih Jenis Pelayanan</option>
                                        <option value="Sakramen Perkawinan">Sakramen Perkawinan</option>
                                        <option value="Sakramen Baptis">Sakramen Baptis</option>
                                        <option value="Sakramen Pengakuan Dosa">Sakramen Pengakuan Dosa</option>
                                        <option value="Sakramen Ekaristi">Sakramen Ekaristi</option>
                                        <option value="Sakramen Krisma">Sakramen Krisma</option>
                                        <option value="Kunjungan Orang Sakit">Kunjungan Orang Sakit</option>
                                        <option value="Pemberkatan Rumah">Pemberkatan Rumah</option>
                                        <option value="Misa Arwah">Misa Arwah</option>
                                        <option value="Doa Umat">Doa Umat</option>
                                    </select>
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
                                <input type="hidden" id="edit_id" name="id_u_p">

                                <div class="mb-3">
                                    <label for="id_umat" class="form-label">Umat</label>
                                    <select id="edit_id_umat" name="id_umat" class="form-select" required>
                                        <option value="" disabled selected>Pilih umat</option>
                                        <?php
                                        // Ambil data umat dari database
                                        $query_umat = "SELECT id_umat, nama_lengkap FROM umat"; // Ganti dengan query yang sesuai
                                        $result_umat = mysqli_query($koneksi, $query_umat);
                                        while ($row_umat = mysqli_fetch_assoc($result_umat)) {
                                            echo '<option value="' . $row_umat['id_umat'] . '">' . $row_umat['nama_lengkap'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_pelayanan" class="form-label">Jenis Pelayanan</label>
                                    <select id="edit_jenis_pelayanan" name="jenis_pelayanan" class="form-select"
                                        required>
                                        <option value="" disabled selected>Pilih Jenis Pelayanan</option>
                                        <option value="Sakramen Perkawinan">Sakramen Perkawinan</option>
                                        <option value="Sakramen Baptis">Sakramen Baptis</option>
                                        <option value="Sakramen Pengakuan Dosa">Sakramen Pengakuan Dosa</option>
                                        <option value="Sakramen Ekaristi">Sakramen Ekaristi</option>
                                        <option value="Sakramen Krisma">Sakramen Krisma</option>
                                        <option value="Kunjungan Orang Sakit">Kunjungan Orang Sakit</option>
                                        <option value="Pemberkatan Rumah">Pemberkatan Rumah</option>
                                        <option value="Misa Arwah">Misa Arwah</option>
                                        <option value="Doa Umat">Doa Umat</option>
                                    </select>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editModalValidasi" tabindex="-1" aria-labelledby="editDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDataModalLabel">Validasi <?= $page_title ?></h5>
                            <button type="button" class="btn-close" id="closeValidasi" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="validasiDataEdit" method="POST"
                                action="proses/<?= $page_title_proses ?>/validasi.php" enctype="multipart/form-data">
                                <input type="hidden" id="edit_id_validasi" name="id_u_p">

                                <div class="mb-3">
                                    <label for="status" class="form-label">Jenis Status Validasi</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="" disabled selected>Pilih Status Validasi</option>
                                        <option value="Telah Disetujui">Setujui Usulan</option>
                                        <option value="Tidak Disetujui">Tidak Setujui Usulan</option>
                                    </select>
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
        function openEditModal(id, id_umat, jenis_pelayanan) {
            let editModal = new bootstrap.Modal(document.getElementById('editModal'));
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_id_umat').value = id_umat;
            document.getElementById('edit_jenis_pelayanan').value = jenis_pelayanan;
            editModal.show();
        }

        function validasi(id, status) {
            let editValidasiModal = new bootstrap.Modal(document.getElementById('editModalValidasi'));
            document.getElementById('edit_id_validasi').value = id;
            document.getElementById('status').value = status;
            editValidasiModal.show();
        }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>