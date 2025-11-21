<?php
// Debug opsional
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    include '../koneksi.php';

    // Prevent SQL Injection
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    $query = "
        INSERT INTO customers (nama, alamat, telepon)
        VALUES ('$nama', '$alamat', '$telepon')
    ";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?status=sukses_tambah");
        exit;
    } else {
        die("ERROR: " . mysqli_error($koneksi));
    }
}

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pelanggan Baru</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Form Tambah Data Pelanggan</div>
        <div class="card-body">

            <form action="tambah.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" name="telepon" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
