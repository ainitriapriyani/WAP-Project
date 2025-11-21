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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: #495057;
        }
        #sidebarCollapse {
            background-color: transparent;
            border: none;
            color: #e83e8c;
            font-size: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 500;
        }
        .border-left-primary { border-left: .25rem solid #4e73df!important; }
        .border-left-success { border-left: .25rem solid #1cc88a!important; }
        .border-left-info { border-left: .25rem solid #36b9cc!important; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Page Content -->
    <div id="content">

        <nav class="navbar navbar-expand-lg navbar-light navbar-custom mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="fas fa-align-left"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>/profile.php"><i class="fas fa-user-edit me-2"></i>Ubah Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
