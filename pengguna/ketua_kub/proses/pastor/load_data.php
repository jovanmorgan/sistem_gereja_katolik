  <div id="load_data">
      <section class="section">
          <div class="row">
              <div class="col-lg-12">
                  <div class="card">
                      <div class="card-body text-center">
                          <!-- Search Form -->
                          <form method="GET" action="">
                              <div class="input-group mt-3">
                                  <input type="text" class="form-control" placeholder="Cari Ketua KUB..." name="search"
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

        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Query untuk mendapatkan data pastor dengan pencarian dan pagination
        $query = "
    SELECT * 
    FROM pastor
    WHERE nama_lengkap LIKE ? OR no_hp LIKE ? OR username LIKE ?
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
    FROM pastor 
    WHERE nama_lengkap LIKE ? OR no_hp LIKE ? OR username LIKE ?
";
        $stmt_total = $koneksi->prepare($total_query);
        $stmt_total->bind_param("sss", $search_param, $search_param, $search_param);
        $stmt_total->execute();
        $total_result = $stmt_total->get_result();
        $total_row = $total_result->fetch_assoc();
        $total_pages = ceil($total_row['total'] / $limit);
        ?>

      <!-- Tabel Data Pastor -->
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
                                              <th>Nama Lengkap</th>
                                              <th>No HP</th>
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
                                                  <td><?php echo htmlspecialchars($row['nama_lengkap']); ?>
                                                  </td>
                                                  <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                  <td><?php echo htmlspecialchars($row['password']); ?></td>
                                                  <td>
                                                      <button class="btn btn-primary btn-sm"
                                                          onclick="openEditModal('<?php echo $row['id_pastor']; ?>', '<?php echo $row['nama_lengkap']; ?>', '<?php echo $row['username']; ?>', '<?php echo $row['password']; ?>', '<?php echo $row['no_hp']; ?>')">Edit</button>
                                                      <button class="btn btn-danger btn-sm"
                                                          onclick="hapus('<?php echo $row['id_pastor']; ?>')">Hapus</button>
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
  </div>