<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

include '../includes/header.php';

// Mengambil semua data pesanan dengan join ke tabel pelanggan
$query = "SELECT orders.id, customers.nama as customer_name, orders.tanggal, orders.total 
          FROM orders 
          JOIN customers ON orders.customer_id = customers.id 
          ORDER BY orders.tanggal DESC, orders.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Pesanan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item active">Daftar Pesanan</li>
    </ol>

    <?php
    // Menampilkan notifikasi berdasarkan status dari URL
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = '';
        $alert_type = '';

        switch ($status) {
            case 'sukses_tambah':
                $message = '<strong>Berhasil!</strong> Pesanan baru telah ditambahkan.';
                $alert_type = 'success';
                break;
            case 'sukses_edit':
                $message = '<strong>Berhasil!</strong> Data pesanan telah diperbarui.';
                $alert_type = 'success';
                break;
            case 'sukses_hapus':
                $message = '<strong>Berhasil!</strong> Data pesanan telah dihapus.';
                $alert_type = 'success';
                break;
            case 'gagal':
                $message = '<strong>Gagal!</strong> Terjadi kesalahan saat memproses data.';
                $alert_type = 'danger';
                break;
        }

        if ($message) {
            echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show" role="alert">'
                . $message .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        }
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-shopping-cart me-1"></i>
                Data Pesanan
            </span>
            <a href="tambah.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Buat Pesanan Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ID Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while ($order = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="text-center"><?= $order['id']; ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?= date('d F Y', strtotime($order['tanggal'])); ?></td>
                                    <td>Rp <?= number_format($order['total'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="detail.php?id=<?= $order['id']; ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $order['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $order['id']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini? Semua item terkait akan ikut terhapus.');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data pesanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Menyertakan footer -->
<?php include '../includes/footer.php'; ?>

