<?php

include 'includes/header.php'; // Header sudah menangani koneksi dan session

// Pastikan admin sudah login dan memiliki ID di session
if (!isset($_SESSION['admin_id'])) {
    // Jika tidak ada ID, mungkin sesi lama, paksa logout untuk keamanan
    header("Location: logout.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$message = '';
$alert_type = '';

// Proses form jika data dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil username dengan aman
    $username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');

    // Ambil password HANYA jika diisi, jika tidak, biarkan kosong
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Validasi dasar
    if (empty($username)) {
        $message = "Username tidak boleh kosong.";
        $alert_type = "danger";
    } else {
        // Cek jika password diisi dan cocok
        if (!empty($password_baru)) {
            if ($password_baru !== $konfirmasi_password) {
                $message = "Konfirmasi password tidak cocok.";
                $alert_type = "danger";
            } else {
                // Enkripsi password baru menggunakan password_hash()
                $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
                $query = "UPDATE admin SET username = ?, password = ? WHERE id = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ssi", $username, $hashed_password, $admin_id);
            }
        } else {
            // Jika password tidak diubah, hanya update username
            $query = "UPDATE admin SET username = ? WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "si", $username, $admin_id);
        }

        // Jalankan query jika tidak ada error sebelumnya
        if (empty($message)) {
            if (mysqli_stmt_execute($stmt)) {
                $message = "Profil berhasil diperbarui. Username baru Anda adalah '<strong>$username</strong>'.";
                $alert_type = "success";
                // Update username di session agar langsung berubah di header
                $_SESSION['username'] = $username;
            } else {
                $message = "Gagal memperbarui profil: " . mysqli_error($koneksi);
                $alert_type = "danger";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Ambil data admin terbaru untuk ditampilkan di form
$query_admin = "SELECT username FROM admin WHERE id = ?";
$stmt_admin = mysqli_prepare($koneksi, $query_admin);
mysqli_stmt_bind_param($stmt_admin, "i", $admin_id);
mysqli_stmt_execute($stmt_admin);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_admin));
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Ubah Profil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item active">Ubah Profil</li>
    </ol>

    <?php if ($message): ?>
    <div class="alert alert-<?= $alert_type; ?> alert-dismissible fade show" role="alert">
        <?= $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Formulir Profil Admin
        </div>
        <div class="card-body">
            <form action="profile.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']); ?>" required>
                </div>
                <hr>
                <p class="text-muted">Isi bagian di bawah ini hanya jika Anda ingin mengubah password.</p>
                <div class="mb-3">
                    <label for="password_baru" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password_baru" name="password_baru">
                </div>
                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

