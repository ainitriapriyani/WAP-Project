<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

include '../includes/header.php';

// Mengambil semua data kue dari database
$query = "SELECT * FROM cakes ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Kue</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item active">Daftar Kue</li>
    </ol>

    <?php
    // Menampilkan notifikasi berdasarkan status dari URL
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = '';
        $alert_type = '';

        switch ($status) {
            case 'sukses_tambah':
                $message = '<strong>Berhasil!</strong> Data kue baru telah ditambahkan.';
                $alert_type = 'success';
                break;
            case 'sukses_edit':
                $message = '<strong>Berhasil!</strong> Data kue telah diperbarui.';
                $alert_type = 'success';
                break;
            case 'sukses_hapus':
                $message = '<strong>Berhasil!</strong> Data kue telah dihapus.';
                $alert_type = 'success';
                break;
            case 'gagal_upload':
                $message = '<strong>Gagal!</strong> Terjadi masalah saat mengunggah gambar.';
                $alert_type = 'danger';
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
                <i class="fas fa-birthday-cake me-1"></i>
                Data Kue Tersedia
            </span>
            <a href="tambah.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Tambah Kue Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Gambar</th>
                            <th>Nama Kue</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php $no = 1; ?>
                            <?php while ($cake = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td>
                                        <img src="../assets/img/<?= htmlspecialchars($cake['gambar']); ?>" alt="<?= htmlspecialchars($cake['nama']); ?>" width="80" class="img-thumbnail">
                                    </td>
                                    <td><?= htmlspecialchars($cake['nama']); ?></td>
                                    <td><?= htmlspecialchars($cake['kategori']); ?></td>
                                    <td>Rp <?= number_format($cake['harga'], 0, ',', '.'); ?></td>
                                    <td><?= $cake['stok']; ?></td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?= $cake['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $cake['id']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data kue ini?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data kue.</td>
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

