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

                        // Query untuk mendapatkan data u_p dengan pencarian dan pagination hanya untuk id_umat dari sesi
                        $query = "
    SELECT u_p.id_u_p, u_p.status, umat.nama_lengkap, umat.id_umat, u_p.jenis_pelayanan 
    FROM u_p
    JOIN umat ON u_p.id_umat = umat.id_umat
    WHERE (umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?)
    AND u_p.id_umat = ?  
    LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("ssiii", $search_param, $search_param, $id_umat, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman untuk data sesuai id_umat dari sesi
                        $total_query = "
    SELECT COUNT(*) as total 
    FROM u_p
    JOIN umat ON u_p.id_umat = umat.id_umat
    WHERE (umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?)
    AND u_p.id_umat = ?";  // Hanya data yang sesuai dengan id_umat dari sesi
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("ssi", $search_param, $search_param, $id_umat);
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
                                                            <td><?php echo htmlspecialchars($row['status']); ?>
                                                            </td>
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm" onclick="openEditModal(
                                                        '<?php echo $row['id_u_p']; ?>', 
                                                        '<?php echo addslashes($row['id_umat']); ?>', 
                                                        '<?php echo addslashes($row['jenis_pelayanan']); ?>'
                                                    )">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_u_p']; ?>')">Hapus</button>
                                                                <a href="export/<?= $page_title_proses ?>?id_u_p=<?php echo $row['id_u_p']; ?>"
                                                                    class="btn btn-warning btn-sm">Export</a>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                                <?php else: ?>
                                                <p class=" text-center mt-4">Data tidak ditemukan.</p>
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

                                <input type="hidden" name="id_umat" id="id_umat" value="<?php echo $id_umat; ?>">

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
                                <input type="hidden" id="edit_id_umat" name="id_umat">

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tambahForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Menghentikan aksi default form submit

            var form = this;
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'proses/<?= $page_title_proses ?>/tambah.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    console.log(response); // Debugging

                    if (response === 'success') {
                        form.reset();
                        document.getElementById('closeTambahModal').click();
                        location.reload()

                        Swal.fire({
                            title: "Berhasil!",
                            text: "Data berhasil ditambahkan",
                            icon: "success",
                            timer: 1200, // 1,2 detik
                            showConfirmButton: false, // Tidak menampilkan tombol OK
                        });
                    } else if (response === 'data_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data data sudah ada, silakan pilih data lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_length') {
                        Swal.fire({
                            title: "Error",
                            text: "Password minimal 8 karakter",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_strength') {
                        Swal.fire({
                            title: "Error",
                            text: "Password harus mengandung angka, huruf kecil dan huruf besar",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'data_tidak_lengkap') {
                        Swal.fire({
                            title: "Error",
                            text: "Data yang anda masukan belum lengkap",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal menambahkan data",
                            icon: "error",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat mengirim data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            };
            xhr.send(formData);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Menghentikan aksi default form submit

            var form = this;
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'proses/<?= $page_title_proses ?>/edit.php', true);
            xhr.onload = function() {

                if (xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    console.log(response); // Debugging

                    if (response === 'success') {
                        form.reset();
                        document.getElementById('closeEditModal').click();
                        location.reload()

                        Swal.fire({
                            title: "Berhasil!",
                            text: "Data berhasil diperbarui",
                            icon: "success",
                            timer: 1200, // 1,2 detik
                            showConfirmButton: false, // Tidak menampilkan tombol OK
                        });
                    } else if (response === 'data_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data data sudah dipromosikan, silakan pilih data data lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_length') {
                        Swal.fire({
                            title: "Error",
                            text: "Password minimal 8 karakter",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_strength') {
                        Swal.fire({
                            title: "Error",
                            text: "Password harus mengandung angka, huruf kecil dan huruf besar",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'data_tidak_lengkap') {
                        Swal.fire({
                            title: "Error",
                            text: "Data yang anda masukan belum lengkap",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal memperbarui data",
                            icon: "error",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat mengirim data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            };
            xhr.send(formData);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('validasiDataEdit').addEventListener('submit', function(event) {
            event.preventDefault(); // Menghentikan aksi default form submit

            var form = this;
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'proses/<?= $page_title_proses ?>/validasi.php', true);
            xhr.onload = function() {

                if (xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    console.log(response); // Debugging

                    if (response === 'success') {
                        form.reset();
                        document.getElementById('closeValidasi').click();
                        location.reload()

                        Swal.fire({
                            title: "Berhasil!",
                            text: "Data berhasil diperbarui",
                            icon: "success",
                            timer: 1200, // 1,2 detik
                            showConfirmButton: false, // Tidak menampilkan tombol OK
                        });
                    } else if (response === 'data_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data data sudah dipromosikan, silakan pilih data data lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_length') {
                        Swal.fire({
                            title: "Error",
                            text: "Password minimal 8 karakter",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'error_password_strength') {
                        Swal.fire({
                            title: "Error",
                            text: "Password harus mengandung angka, huruf kecil dan huruf besar",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'no_kk_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_harus_16') {
                        Swal.fire({
                            title: "Error",
                            text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'nik_sudah_ada') {
                        Swal.fire({
                            title: "Error",
                            text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else if (response === 'data_tidak_lengkap') {
                        Swal.fire({
                            title: "Error",
                            text: "Data yang anda masukan belum lengkap",
                            icon: "info",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal memperbarui data",
                            icon: "error",
                            timer: 2000, // 2 detik
                            showConfirmButton: false,
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Terjadi kesalahan saat mengirim data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            };
            xhr.send(formData);
        });
    });

    function hapus(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Setelah dihapus, Anda tidak akan dapat memulihkan data ini!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            dangerMode: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi untuk menghapus
                var xhr = new XMLHttpRequest();

                xhr.open('POST', 'proses/<?= $page_title_proses ?>/hapus.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {

                    if (xhr.status === 200) {
                        var response = xhr.responseText.trim();
                        if (response === 'success') {
                            location.reload()
                            Swal.fire({
                                title: 'Sukses!',
                                text: 'Data berhasil dihapus.',
                                icon: 'success',
                                timer: 1200, // 1,2 detik
                                showConfirmButton: false // Menghilangkan tombol OK
                            }).then(() => {
                                location.reload()
                            })
                        } else if (response === 'error') {
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal menghapus Data.',
                                icon: 'error',
                                timer: 2000, // 2 detik
                                showConfirmButton: false // Menghilangkan tombol OK
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mengirim data.',
                                icon: 'error',
                                timer: 2000, // 2 detik
                                showConfirmButton: false // Menghilangkan tombol OK
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengirim data.',
                            icon: 'error',
                            timer: 2000, // 2 detik
                            showConfirmButton: false // Menghilangkan tombol OK
                        });
                    }
                };
                xhr.send("id=" + id);
            } else {
                // Jika pengguna membatalkan penghapusan
                Swal.fire({
                    title: 'Penghapusan dibatalkan',
                    icon: 'info',
                    timer: 1500, // 1,5 detik
                    showConfirmButton: false // Menghilangkan tombol OK
                });
            }
        });
    }

    function loadTable() {
        // Get current page and search query from URL
        var currentPage = new URLSearchParams(window.location.search).get('page') || 1;
        var searchQuery = new URLSearchParams(window.location.search).get('search') || '';

        var xhrTable = new XMLHttpRequest();
        xhrTable.onreadystatechange = function() {
            if (xhrTable.readyState == 4 && xhrTable.status == 200) {
                document.getElementById('load_data').innerHTML = xhrTable.responseText;
            }
        };

        // Send request with current page and search query
        xhrTable.open('GET', 'proses/<?= $page_title_proses ?>/load_data.php?page=' + currentPage + '&search=' +
            encodeURIComponent(
                searchQuery), true);
        xhrTable.send();
    }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!--   Core JS Files   -->
    <script src="../../assets/js/core/jquery-3.7.1.min.js?v=<?= time(); ?>"></script>
    <script src="../../assets/js/core/popper.min.js?v=<?= time(); ?>"></script>
    <script src="../../assets/js/core/bootstrap.min.js?v=<?= time(); ?>"></script>

    <!-- jQuery Scrollbar -->
    <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js?v=<?= time(); ?>"></script>

    <!-- Chart JS -->
    <script src="../../assets/js/plugin/chart.js/chart.min.js?v=<?= time(); ?>"></script>

    <!-- jQuery Sparkline -->
    <script src="../../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js?v=<?= time(); ?>"></script>

    <!-- Chart Circle -->
    <script src="../../assets/js/plugin/chart-circle/circles.min.js?v=<?= time(); ?>"></script>

    <!-- Datatables -->
    <script src="../../assets/js/plugin/datatables/datatables.min.js?v=<?= time(); ?>"></script>

    <!-- Bootstrap Notify -->
    <script src="../../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js?v=<?= time(); ?>"></script>

    <!-- jQuery Vector Maps -->
    <script src="../../assets/js/plugin/jsvectormap/jsvectormap.min.js?v=<?= time(); ?>"></script>
    <script src="../../assets/js/plugin/jsvectormap/world.js?v=<?= time(); ?>"></script>

    <!-- Sweet Alert -->
    <script src="../../assets/js/plugin/sweetalert/sweetalert.min.js?v=<?= time(); ?>"></script>

    <!-- Kaiadmin JS -->
    <script src="../../assets/js/kaiadmin.min.js?v=<?= time(); ?>"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../../assets/js/setting-demo.js?v=<?= time(); ?>"></script>
    <script src="../../assets/js/demo.js?v=<?= time(); ?>"></script>
    <script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
    });
    </script>
</body>

</html>