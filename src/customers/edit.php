<?php
include '../koneksi.php';

// --- Ambil data ---
$id = intval($_GET['id'] ?? 0);

if ($id < 1) {
    header("Location: index.php?status=id_tidak_valid");
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM customers WHERE id = $id");
$c = mysqli_fetch_assoc($result);

if (!$c) {
    header("Location: index.php?status=data_tidak_ada");
    exit;
}

// --- Proses update ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);

    $query = "
        UPDATE customers 
        SET nama='$nama', alamat='$alamat', telepon='$telepon'
        WHERE id=$id
    ";

    mysqli_query($koneksi, $query);

    header("Location: index.php?status=sukses_edit");
    exit;
}

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Pelanggan</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama" value="<?= $c['nama'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control" name="alamat" required><?= $c['alamat'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" class="form-control" name="telepon" value="<?= $c['telepon'] ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
