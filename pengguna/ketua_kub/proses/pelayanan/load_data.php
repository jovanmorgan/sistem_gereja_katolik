 <div id="load_data">
     <section class="section">
         <div class="row">
             <div class="col-lg-12">
                 <div class="card">
                     <div class="card-body text-center">
                         <!-- Search Form -->
                         <form method="GET" action="">
                             <div class="input-group mt-3">
                                 <input type="text" class="form-control" placeholder="Cari Data..." name="search"
                                     value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                 <button class="btn btn-outline-secondary" type="submit">Cari</button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </section>

     <?php
        include '../../../../keamanan/koneksi.php';

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
                                         <td style="display: flex; justify-content: center; gap: 10px;">
                                             <button class="btn btn-primary btn-sm"
                                                 onclick="openEditModal('<?php echo $row['id_pelayanan']; ?>', '<?php echo $row['id_pastor']; ?>', '<?php echo $row['id_u_p']; ?>', '<?php echo $row['hari_tgl']; ?>', '<?php echo $row['tempat']; ?>')">Edit</button>
                                             <button class="btn btn-danger btn-sm"
                                                 onclick="hapus('<?php echo $row['id_pelayanan']; ?>')">Hapus</button>
                                             <a href="export/pelayanan?id_pelayanan=<?php echo $row['id_pelayanan']; ?>"
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
                                     <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
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