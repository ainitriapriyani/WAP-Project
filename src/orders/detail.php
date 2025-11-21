<?php
// Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =====================================
// SESSION & KONEKSI
// =====================================
session_start();
require_once '../koneksi.php';
include '../includes/header.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Pastikan ID ada
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = intval($_GET['id']);

// =====================
// AMBIL DETAIL PESANAN (PERBAIKAN DISINI)
// =====================
$query_order = "
    SELECT 
        orders.id,
        orders.tanggal,
        orders.total,
        orders.status,
        orders.metode_pembayaran,
        orders.catatan,
        customers.nama AS customer_name,
        customers.address,
        customers.phone
    FROM orders
    JOIN customers ON customers.id = orders.customer_id
    WHERE orders.id = $order_id
";

$result_order = mysqli_query($koneksi, $query_order);
$order = mysqli_fetch_assoc($result_order);

if (!$order) {
    header("Location: index.php");
    exit;
}

// =====================
// AMBIL ITEM PESANAN
// =====================
$query_items = "
    SELECT 
        cakes.nama AS cake_name,
        cakes.harga,
        cakes.gambar AS cake_image,
        order_items.jumlah,
        order_items.subtotal
    FROM order_items
    JOIN cakes ON cakes.id = order_items.cake_id
    WHERE order_items.order_id = $order_id
";


$result_items = mysqli_query($koneksi, $query_items);

// Format ID menjadi INV-00001
$invoice_no = "INV-" . str_pad($order['id'], 5, "0", STR_PAD_LEFT);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Pesanan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Daftar Pesanan</a></li>
        <li class="breadcrumb-item active"><?= $invoice_no ?></li>
    </ol>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-receipt me-1"></i>
                Faktur Pesanan <?= $invoice_no ?>
            </span>
            <!-- <a href="print.php?id=<?= $order_id ?>" class="btn btn-danger btn-sm" target="_blank">
    <i class="fas fa-file-pdf me-1"></i> Download PDF
</a> -->

            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a> 
        </div>

        <div class="card-body">

            <!-- DATA PELANGGAN -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Data Pelanggan</h5>
                    
                    <p><strong>Nama:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>

                    <p><strong>Alamat:</strong> 
                        <?= htmlspecialchars($order['address'] ?: '-'); ?>
                    </p>

                    <p><strong>Telepon:</strong> 
                        <?= htmlspecialchars($order['phone'] ?: '-'); ?>
                    </p>

                </div>

                <div class="col-md-6 text-md-end">
                    <h5>Informasi Pesanan</h5>
                    <p><strong>ID Pesanan:</strong> <?= $invoice_no ?></p>
                    <p><strong>Tanggal:</strong> <?= date("d F Y", strtotime($order['tanggal'])); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            <?php 
                                echo ($order['status'] == 'Selesai' ? 'bg-success' :
                                      ($order['status'] == 'Diproses' ? 'bg-primary' :
                                      ($order['status'] == 'Dibatalkan' ? 'bg-danger' : 'bg-warning'))); 
                            ?>">
                            <?= htmlspecialchars($order['status']); ?>
                        </span>
                    </p>
                    <!-- <p><strong>Metode Pembayaran:</strong> 
                        <?= htmlspecialchars($order['metode_pembayaran']); ?>
                    </p> -->
                </div>
            </div>

            <!-- CATATAN -->
            <?php if (!empty($order['catatan'])) : ?>
                <div class="alert alert-info">
                    <strong>Catatan Pelanggan:</strong><br>
                    <?= nl2br(htmlspecialchars($order['catatan'])); ?>
                </div>
            <?php endif; ?>

            <!-- TABEL ITEM -->
          <div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Gambar</th>
                <th>Nama Kue</th>
                <th class="text-end">Harga</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($item = mysqli_fetch_assoc($result_items)) : ?>
            <tr>
                <td><?= $no++; ?></td>

                <!-- Gambar Produk (TANPA UBAH SQL) -->
                <td>
                   <?php 
$img = !empty($item['cake_image']) ? $item['cake_image'] : 'default.png';
?>
<img src="../uploads/cakes/<?= htmlspecialchars($img); ?>"
     alt="Foto Kue"
     style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">

                </td>

                <td><?= htmlspecialchars($item['cake_name']); ?></td>
                <td class="text-end">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                <td class="text-center"><?= $item['jumlah']; ?></td>
                <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="text-end fs-5">Total</th>
                <th class="text-end fs-5">Rp <?= number_format($order['total'], 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>
</div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
