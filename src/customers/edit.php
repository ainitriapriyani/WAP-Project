<?php
// Memulai session dan menyertakan file-file penting
session_start();
include '../koneksi.php';


// Cek jika pengguna tidak login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Inisialisasi variabel
$id = '';
$nama = '';
$alamat = '';
$telepon = '';
$error_message = '';

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'];

// Logika untuk memproses form edit data saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_post = $_POST['id'];
    $nama_post = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat_post = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $telepon_post = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    // Query untuk update data
    $query_update = "UPDATE customers SET nama='$nama_post', alamat='$alamat_post', telepon='$telepon_post' WHERE id=$id_post";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: index.php?status=sukses_edit");
        exit();
    } else {
        $error_message = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}

// Ambil data pelanggan yang akan diedit dari database
$query_get = "SELECT * FROM customers WHERE id = $id";
$result_get = mysqli_query($koneksi, $query_get);

if (mysqli_num_rows($result_get) == 1) {
    $customer = mysqli_fetch_assoc($result_get);
    $nama = $customer['nama'];
    $alamat = $customer['alamat'];
    $telepon = $customer['telepon'];
} else {
    // Jika data tidak ditemukan, redirect
    header("Location: index.php?status=gagal");
    exit();
}

include '../includes/header.php';
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Data Pelanggan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Daftar Pelanggan</a></li>
        <li class="breadcrumb-item active">Edit Pelanggan</li>
    </ol>

    <?php if (!empty($error_message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Form Edit Pelanggan
        </div>
        <div class="card-body">
            <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($alamat); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($telepon); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<!-- Menyertakan footer -->
<?php include '../includes/footer.php'; ?>
