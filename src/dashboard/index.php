<?php
// Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =====================================
// SESSION & KONEKSI WAJIB ADA DI AWAL
// =====================================
session_start();

require_once '../koneksi.php'; 
include '../includes/header.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// ===============================
// FUNGSI AMBIL TOTAL DATA
// ===============================
function get_total($koneksi, $table) {
    $query = "SELECT COUNT(id) AS total FROM `$table`"; 
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        return 0;
    }

    $data = mysqli_fetch_assoc($result);
    return $data ? (int)$data['total'] : 0;
}

$total_cakes     = get_total($koneksi, "cakes");
$total_customers = get_total($koneksi, "customers");
$total_orders    = get_total($koneksi, "orders");

// ===============================
// GRAFIK PENJUALAN 30 HARI
// ===============================
$query_chart = "
    SELECT DATE(tanggal) AS order_date, COUNT(id) AS order_count
    FROM orders
    WHERE tanggal >= CURDATE() - INTERVAL 30 DAY
    GROUP BY DATE(tanggal)
    ORDER BY order_date ASC
";

$result_chart = mysqli_query($koneksi, $query_chart);

$chart_labels = [];
$chart_data   = [];

if ($result_chart && mysqli_num_rows($result_chart) > 0) {
    while ($row = mysqli_fetch_assoc($result_chart)) {
        $chart_labels[] = date("d M", strtotime($row['order_date']));
        $chart_data[]   = (int)$row['order_count'];
    }
}

$json_labels = json_encode($chart_labels);
$json_data   = json_encode($chart_data);
?>

<!-- ================================
           DASHBOARD UI
================================= -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!
        </li>
    </ol>

    <div class="row">

        <!-- TOTAL KUE -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="../cakes/index.php" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Kue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_cakes; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- TOTAL PELANGGAN -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="../customers/index.php" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pelanggan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_customers; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- TOTAL PESANAN -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="../orders/index.php" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Pesanan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_orders; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>


    <!-- ================================
              GRAFIK
    ================================= -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan (30 Hari Terakhir)</h6>
                </div>
                <div class="card-body">

                    <?php if (!empty($chart_labels)): ?>
                        <div style="height: 320px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted my-5">
                            Belum ada data penjualan 30 hari terakhir.
                        </p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- ================================
      JAVASCRIPT GRAFIK
================================ -->
<?php if (!empty($chart_labels)): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const ctx = document.getElementById("salesChart").getContext("2d");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: <?= $json_labels; ?>,
            datasets: [{
                label: "Jumlah Pesanan",
                data: <?= $json_data; ?>,
                borderColor: "rgba(232, 62, 140, 1)",
                backgroundColor: "rgba(232, 62, 140, 0.1)",
                fill: true,
                tension: 0.3,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

});
</script>
<?php endif; ?>
