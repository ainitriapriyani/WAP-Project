<?php
include '../koneksi.php';
include '../includes/header.php';

// Ambil semua data kue
$query = "SELECT * FROM cakes ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Kue</h1>

    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $messages = [
            'sukses_tambah' => ['success', 'Data kue berhasil ditambahkan!'],
            'sukses_edit' => ['success', 'Data kue berhasil diperbarui!'],
            'sukses_hapus' => ['success', 'Data kue berhasil dihapus!'],
            'gagal' => ['danger', 'Terjadi kesalahan saat memproses data'],
            'gagal_upload' => ['danger', 'Gagal mengunggah gambar'],
        ];

        if (isset($messages[$status])) {
            echo "<div class='alert alert-{$messages[$status][0]}'>
                    {$messages[$status][1]}
                  </div>";
        }
    }
    ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Data Kue</span>
            <a href="tambah.php" class="btn btn-primary btn-sm">Tambah Kue</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($cake = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><img src="../assets/img/<?= $cake['gambar']; ?>" width="80"></td>
                            <td><?= $cake['nama']; ?></td>
                            <td><?= $cake['kategori']; ?></td>
                            <td>Rp <?= number_format($cake['harga']); ?></td>
                            <td><?= $cake['stok']; ?></td>
                            <td>
                                <a href="edit.php?id=<?= $cake['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="hapus.php?id=<?= $cake['id']; ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
