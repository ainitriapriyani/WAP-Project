<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Urutkan ID dari kecil ke besar
$result = mysqli_query($koneksi, "SELECT * FROM customers ORDER BY id ASC");
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Pelanggan</h1>

    <a href="tambah.php" class="btn btn-primary mb-3">+ Tambah Pelanggan</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= $c['nama'] ?></td>
                        <td><?= $c['email'] ?: '-' ?></td>
                        <td><?= $c['phone'] ?></td>
                        <td><?= $c['address'] ?></td>
                        <td width="170">
                            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Hapus pelanggan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
