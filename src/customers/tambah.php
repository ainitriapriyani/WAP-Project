<?php
// --- KODE DEBUGGING ---
// Tambahkan baris ini di paling atas untuk menampilkan error PHP secara detail
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

// Pindahkan seluruh blok pemrosesan form ke paling atas
// agar header() bisa dieksekusi sebelum ada output HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koneksi diperlukan di sini karena 'header.php' belum di-include
    include '../koneksi.php';

    // Ambil dan bersihkan data dari form
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    // Query untuk memasukkan data
    $query = "INSERT INTO customers (nama, alamat, telepon) VALUES ('$nama', '$alamat', '$telepon')";

    // Eksekusi query dan tambahkan penanganan error
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, redirect ke halaman index dengan status sukses
        header("Location: index.php?status=sukses_tambah");
        exit(); // Penting: Hentikan eksekusi skrip setelah redirect
    } else {
        // Jika gagal, tampilkan pesan error SQL dan hentikan skrip
        die("ERROR: Gagal menyimpan data. " . mysqli_error($koneksi));
    }
}

// Sertakan header setelah logika pemrosesan form selesai.
// Jika redirect terjadi, baris di bawah ini tidak akan pernah dieksekusi.
include '../includes/header.php';
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pelanggan Baru</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Pelanggan</a></li>
        <li class="breadcrumb-item active">Tambah Pelanggan</li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i>
            Form Tambah Data Pelanggan
        </div>
        <div class="card-body">
            <form action="tambah.php" method="POST">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

