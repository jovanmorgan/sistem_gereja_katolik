<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Fungsi untuk mendapatkan ikon yang sesuai dengan halaman
function getIconForPage($page)
{
    switch ($page) {
        case 'dashboard':
            return 'fas fa-home';
        case 'kub':
            return 'fas fa-people-carry';
        case 'u_p':
            return 'fas fa-lightbulb';
        case 'pelayanan':
            return 'fas fa-hands-helping';
        case 'pastor':
            return 'fas fa-user';
        case 'ketua_kub':
            return 'fas fa-user-tie';
        case 'umat':
            return 'fas fa-users';
        case 'profile':
            return 'fas fa-user';
        case 'log_out':
            return 'fas fa-sign-out-alt';
        default:
            return 'fas fa-file'; // Ikon default jika halaman tidak dikenal
    }
}
?>

<!-- Sidebar -->
<div class="sidebar" data-background-color="white">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="dasboard" class="logo">
                <img src="../../assets/img/gereja/logo_gereja.png" alt="navbar brand" class="navbar-brand" height="60px"
                    style="position: relative; right: 25px;" />
                <h5 class="text-black" style="font-size: 15px; position: relative; right: 25px; margin-top: 5px">Gereja
                    Katolik</h5>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-veumatical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner" data-background-color="white">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                    <a href="dashboard">
                        <i class="<?php echo getIconForPage('dashboard'); ?>"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <ja class="text-section">Sistem Gereja</ja>
                </li>
                <li class="nav-item <?php echo ($current_page == 'u_p') ? 'active' : ''; ?>">
                    <a href="u_p">
                        <i class="<?php echo getIconForPage('u_p'); ?>"></i>
                        <p>Usulan Pelayanan</p>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_page == 'pelayanan') ? 'active' : ''; ?>">
                    <a href="pelayanan">
                        <i class="<?php echo getIconForPage('pelayanan'); ?>"></i>
                        <p>Jadwal Pelayanan</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Profile</h4>
                </li>
                <li class="nav-item <?php echo ($current_page == 'profile') ? 'active' : ''; ?>">
                    <a href="profile">
                        <i class="<?php echo getIconForPage('profile'); ?>"></i>
                        <p>Profile Saya</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="log_out">
                        <i class="<?php echo getIconForPage('log_out'); ?>"></i>
                        <p>Log Out</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->