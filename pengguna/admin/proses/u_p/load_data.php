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
                            SELECT u_p.id_u_p, u_p.status, umat.nama_lengkap, umat.id_umat, u_p.jenis_pelayanan 
                            FROM u_p
                            JOIN umat ON u_p.id_umat = umat.id_umat
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
                                FROM u_p
                                JOIN umat ON u_p.id_umat = umat.id_umat
                                WHERE umat.nama_lengkap LIKE ? OR u_p.jenis_pelayanan LIKE ?";
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
                                                 <td style="display: flex; justify-content: center; gap: 10px;">
                                                     <button class="btn btn-primary btn-sm" onclick="openEditModal(
                                                        '<?php echo $row['id_u_p']; ?>', 
                                                        '<?php echo addslashes($row['id_umat']); ?>', 
                                                        '<?php echo addslashes($row['jenis_pelayanan']); ?>'
                                                    )">Edit</button>
                                                     <button class="btn btn-danger btn-sm"
                                                         onclick="hapus('<?php echo $row['id_u_p']; ?>')">Hapus</button>
                                                     <a href="export/u_p?id_u_p=<?php echo $row['id_u_p']; ?>"
                                                         class="btn btn-warning btn-sm">Export</a>
                                                     <button class="btn btn-info btn-sm"
                                                         onclick="validasi('<?php echo $row['id_u_p']; ?>', '<?php echo $row['status']; ?>')">Validasi</button>
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