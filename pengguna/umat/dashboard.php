<?php include 'fitur/penggunah.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'fitur/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'fitur/sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'fitur/navbar.php'; ?>
            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                            <h6 class="op-7 mb-2">Halaman Dasboard</h6>
                        </div>
                    </div>

                    <?php
                    include '../../keamanan/koneksi.php';

                    // Mengambil data jumlah umat yang mengajukan jenis pelayanan
                    $query = "SELECT jenis_pelayanan, COUNT(id_umat) AS jumlah FROM u_p GROUP BY jenis_pelayanan";
                    $result = mysqli_query($koneksi, $query);
                    $data_jumlah_pelayanan = [];
                    $labels = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $labels[] = $row['jenis_pelayanan'];
                        $data_jumlah_pelayanan[] = $row['jumlah'];
                    }

                    // Mengambil data jumlah umat yang disetujui
                    $query_status = "SELECT status, COUNT(id_umat) AS jumlah FROM u_p GROUP BY status";
                    $result_status = mysqli_query($koneksi, $query_status);
                    $data_jumlah_status = [];
                    $labels_status = [];
                    while ($row = mysqli_fetch_assoc($result_status)) {
                        $labels_status[] = $row['status'];
                        $data_jumlah_status[] = $row['jumlah'];
                    }

                    $tables = [

                        'pelayanan' => [
                            'label' => 'Pelayanan',
                            'icon' => 'fas fa-hands-helping', // Ikon yang lebih menunjukkan pelayanan dan bantuan
                            'color' => '#DC3545' // Red
                        ],
                        'u_p' => [
                            'label' => 'Usulan Pelayanan',
                            'icon' => 'fas fa-lightbulb', // Ikon yang menunjukkan ide atau usulan
                            'color' => '#0D6EFD' // Blue
                        ]
                    ];

                    $counts = [];

                    foreach ($tables as $table => $details) {
                        $query = "SELECT COUNT(*) as count FROM $table";
                        $result = mysqli_query($koneksi, $query);
                        $row = mysqli_fetch_assoc($result);
                        $counts[$table] = $row['count'];
                        mysqli_free_result($result);
                    }

                    mysqli_close($koneksi);



                    ?>
                    <?php include 'fitur/nama_halaman.php'; ?>

                    <section class="section">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title" style="font-size: 30px;">Selamat Datang</h5>
                                        <p>
                                            Silakan lihat informsi yang kami sajikan pada website ini, Berikut adalah
                                            informasi data pada Halaman
                                            <?= $page_title ?>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="section">
                        <div class="row">

                            <div class="row">
                                <?php foreach ($tables as $table => $details): ?>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="card card-stats card-round">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-icon">
                                                        <div class="icon-big text-center icon-secondary bubble-shadow-small"
                                                            style="background-color: <?= $details['color']; ?>;">
                                                            <i class="<?= $details['icon']; ?>"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col col-stats ms-3 ms-sm-0">
                                                        <div class="numbers">
                                                            <p class="card-category"><?= $details['label']; ?></p>
                                                            <h4 class="card-title"><?= $counts[$table]; ?></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </section>

                    <section class="section">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4>Jumlah Umat Mengajukan Jenis Pelayanan</h4>
                                        <canvas id="chartJenisPelayanan"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4>Status Persetujuan</h4>
                                        <canvas id="chartStatus"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const ctx1 = document.getElementById('chartJenisPelayanan').getContext('2d');
                        const chartJenisPelayanan = new Chart(ctx1, {
                            type: 'bar',
                            data: {
                                labels: <?= json_encode($labels) ?>,
                                datasets: [{
                                    label: 'Jumlah Umat',
                                    data: <?= json_encode($data_jumlah_pelayanan) ?>,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        const ctx2 = document.getElementById('chartStatus').getContext('2d');
                        const chartStatus = new Chart(ctx2, {
                            type: 'pie',
                            data: {
                                labels: <?= json_encode($labels_status) ?>,
                                datasets: [{
                                    label: 'Jumlah Umat',
                                    data: <?= json_encode($data_jumlah_status) ?>,
                                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Status Persetujuan'
                                    }
                                }
                            }
                        });
                    </script>

                </div>
            </div>

            <?php include 'fitur/footer.php'; ?>
        </div>

    </div>
    <?php include 'fitur/js.php'; ?>
</body>

</html>