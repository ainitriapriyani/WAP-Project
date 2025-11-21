<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama    = trim($_POST['nama']);
    $email   = trim($_POST['email']);
    $telepon = trim($_POST['phone']);
    $alamat  = trim($_POST['address']);

    $stmt = $koneksi->prepare("
        INSERT INTO customers (nama, email, phone, address)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $nama, $email, $telepon, $alamat);
    $stmt->execute();

    header("Location: index.php?status=sukses_tambah");
    exit;
}

include '../includes/header.php';
?>

<div class="container">
    <h1 class="mt-4">Tambah Pelanggan</h1>

    <div class="card shadow-sm p-4">
        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email (opsional)</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
    