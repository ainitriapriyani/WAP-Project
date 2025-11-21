<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

$result = mysqli_query($koneksi, "SELECT * FROM customers WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php?status=data_tidak_ditemukan");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $telepon = trim($_POST['phone']);
    $alamat  = trim($_POST['address']);

    $stmt = $koneksi->prepare("
        UPDATE customers 
        SET name=?, email=?, phone=?, address=?
        WHERE id=?
    ");

    $stmt->bind_param("ssssi", $nama, $email, $telepon, $alamat, $id);
    $stmt->execute();

    header("Location: index.php?status=sukses_edit");
    exit;
}

include '../includes/header.php';
?>

<div class="container">
    <h1 class="mt-4">Edit Pelanggan</h1>

    <div class="card shadow-sm p-4">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="<?= $data['name'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email (opsional)</label>
                <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control" value="<?= $data['phone'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" required><?= $data['address'] ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
