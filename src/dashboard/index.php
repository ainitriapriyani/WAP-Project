<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

// header.php sudah menangani session, koneksi, dan cek login.
include '../includes/header.php';

// --- Mengambil Data Ringkasan ---
$query_cakes = "SELECT COUNT(id) as total_cakes FROM cakes";
$result_cakes = mysqli_query($koneksi, $query_cakes);
$total_cakes = mysqli_fetch_assoc($result_cakes)['total_cakes'];

$query_customers = "SELECT COUNT(id) as total_customers FROM customers";
$result_customers = mysqli_query($koneksi, $query_customers);
$total_customers = mysqli_fetch_assoc($result_customers)['total_customers'];

$query_orders = "SELECT COUNT(id) as total_orders FROM orders";
$result_orders = mysqli_query($koneksi, $query_orders);
$total_orders = mysqli_fetch_assoc($result_orders)['total_orders'];

// --- Mengambil Data untuk Grafik ---
// Ambil jumlah pesanan per hari selama 30 hari terakhir.
$query_chart = "SELECT DATE(tanggal) as order_date, COUNT(id) as order_count 
                FROM orders 
                WHERE tanggal >= CURDATE() - INTERVAL 30 DAY 
                GROUP BY DATE(tanggal) 
                ORDER BY order_date ASC";
$result_chart = mysqli_query($koneksi, $query_chart);

$chart_labels = [];
$chart_data = [];
if (mysqli_num_rows($result_chart) > 0) {
    while($row = mysqli_fetch_assoc($result_chart)) {
        // Format tanggal menjadi '14 Oct'
        $chart_labels[] = date("d M", strtotime($row['order_date']));
        $chart_data[] = $row['order_count'];
    }
}

// Konversi data PHP ke format JSON agar bisa dibaca JavaScript
$json_labels = json_encode($chart_labels);
$json_data = json_encode($chart_data);
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
    </ol>

    <!-- Baris untuk Kartu Ringkasan -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_cakes; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pelanggan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_customers; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pesanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_orders; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris untuk Grafik Penjualan -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan (30 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($chart_labels)): ?>
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted my-5">Belum ada data penjualan dalam 30 hari terakhir untuk ditampilkan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menyertakan footer -->
<?php include '../includes/footer.php'; ?>

<!-- Script khusus untuk halaman ini (HANYA JIKA ADA DATA) -->
<?php if (!empty($chart_labels)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mengambil data dari variabel PHP yang sudah di-encode ke JSON
        const chartLabels = <?= $json_labels; ?>;
        const chartDataPoints = <?= $json_data; ?>;

        const salesData = {
            labels: chartLabels,
            datasets: [{
                label: "Jumlah Pesanan",
                lineTension: 0.3,
                backgroundColor: "rgba(232, 62, 140, 0.05)",
                borderColor: "rgba(232, 62, 140, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(232, 62, 140, 1)",
                pointBorderColor: "rgba(232, 62, 140, 1)",
                pointHoverRadius: 4,
                pointHoverBackgroundColor: "rgba(232, 62, 140, 1)",
                pointHoverBorderColor: "rgba(232, 62, 140, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: chartDataPoints,
            }],
        };

        const chartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, minRotation: 45 }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Memastikan angka di sumbu Y adalah integer
                        callback: function(value) { if (Number.isInteger(value)) { return value; } },
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Jumlah Pesanan: ${context.raw}`;
                        }
                    }
                }
            }
        };

        const ctx = document.getElementById('salesChart').getContext('2d');
        const myLineChart = new Chart(ctx, {
            type: 'line',
            data: salesData,
            options: chartOptions,
        });
    });
</script>
<?php endif; ?>

