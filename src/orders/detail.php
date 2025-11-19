<?php
// Memulai session dan menyertakan file-file penting
session_start();
include '../koneksi.php';
include '../includes/header.php';


// Cek jika pengguna tidak login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

// Query untuk mengambil detail pesanan utama
$query_order = "SELECT orders.id, customers.nama AS customer_name, customers.alamat, customers.telepon, orders.tanggal, orders.total 
                FROM orders 
                JOIN customers ON orders.customer_id = customers.id 
                WHERE orders.id = $order_id";
$result_order = mysqli_query($koneksi, $query_order);
$order = mysqli_fetch_assoc($result_order);

// Jika pesanan tidak ditemukan, redirect
if (!$order) {
    header("Location: index.php");
    exit();
}

// Query untuk mengambil item-item dalam pesanan
$query_items = "SELECT cakes.nama AS cake_name, cakes.harga, order_items.jumlah, order_items.subtotal 
                FROM order_items 
                JOIN cakes ON order_items.cake_id = cakes.id 
                WHERE order_items.order_id = $order_id";
$result_items = mysqli_query($koneksi, $query_items);
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Pesanan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Daftar Pesanan</a></li>
        <li class="breadcrumb-item active">Detail Pesanan #<?php echo htmlspecialchars($order['id']); ?></li>
    </ol>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-receipt me-1"></i>
                Faktur Pesanan #<?php echo htmlspecialchars($order['id']); ?>
            </span>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <!-- Informasi Pelanggan dan Pesanan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Pelanggan:</h5>
                    <p class="mb-1"><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p class="mb-1"><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat']); ?></p>
                    <p class="mb-1"><strong>Telepon:</strong> <?php echo htmlspecialchars($order['telepon']); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Detail Pesanan:</h5>
                    <p class="mb-1"><strong>ID Pesanan:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
                    <p class="mb-1"><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($order['tanggal'])); ?></p>
                </div>
            </div>

            <!-- Tabel Item Pesanan -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Kue</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $nomor = 1;
                        while ($item = mysqli_fetch_assoc($result_items)) : ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($item['cake_name']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($item['jumlah']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end fs-5">Total Pesanan</th>
                            <th class="text-end fs-5">Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Menyertakan footer -->
<?php include '../includes/footer.php'; ?>
