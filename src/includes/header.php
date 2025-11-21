<?php
// Mulai session HANYA jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menyertakan file koneksi
require_once __DIR__ . '/../koneksi.php';

// Base path proyek
$base_path = '/cake_shop';

// Cek login untuk semua halaman kecuali index.php dan menu.php
$script_name = basename($_SERVER['SCRIPT_NAME']);
$allowed_pages = ['index.php', 'menu.php'];

if (!isset($_SESSION['username']) && !in_array($script_name, $allowed_pages)) {
    header("Location: {$base_path}/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Shop Management</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
 /* === Apple Minimal Pastel Navbar === */
.navbar-custom {
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);

    border-bottom: 1px solid rgba(255, 170, 200, 0.25);

    /* Soft floating shadow */
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);

    padding: 10px 16px;
    border-radius: 0 0 18px 18px;

    transition: all 0.25s ease;
}

.navbar-custom:hover {
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.07);
}

/* Navbar Text */
.navbar-custom .nav-link,
.navbar-custom .navbar-brand {
    color: #4a4a4a;
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Sidebar toggle button */
#sidebarCollapse {
    border: none;
    font-size: 1.5rem;
    color: #e83e8c;
    background: transparent;

    transition: all 0.2s ease;
}

#sidebarCollapse:hover {
    transform: scale(1.1);
    color: #ff5fa8;
}

/* User dropdown */
.navbar-custom .dropdown-menu {
    border-radius: 12px;
    border: none;
    padding: 8px 0;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.navbar-custom .dropdown-item {
    padding: 10px 16px;
    font-weight: 500;
    border-radius: 8px;
}

.navbar-custom .dropdown-item:hover {
    background: #ffe6f4;
    color: #e83e8c;
}

.navbar-custom .nav-link i {
    color: #e83e8c;
}


    </style>
</head>
<body>
<div class="wrapper">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Page Content -->
    <div id="content">

      <nav class="navbar navbar-expand-lg navbar-light navbar-custom mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Tombol Sidebar -->
        <button type="button" id="sidebarCollapse" class="btn">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Profil User -->
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center"
               href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">

                <i class="fas fa-user-circle fa-lg me-2"></i>
                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></span>

            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?php echo $base_path; ?>/profile.php">
                        <i class="fas fa-user-edit me-2"></i>
                        Ubah Password
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item" href="<?php echo $base_path; ?>/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>

    