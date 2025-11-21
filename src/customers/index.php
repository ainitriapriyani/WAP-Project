<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama     = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga    = intval($_POST['harga']);
    $stok     = intval($_POST['stok']);
    $gambar   = '';

    /* -----------------------------
       VALIDASI & UPLOAD GAMBAR
    ------------------------------ */
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {

        $folder = "../assets/img/";
        $nama_file = uniqid() . "-" . basename($_FILES['gambar']['name']);
        $lokasi = $folder . $nama_file;

        // Ekstensi yang diizinkan
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($lokasi, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            header("Location: tambah.php?status=tipe_file_salah");
            exit;
        }

        // Pindahkan file
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $lokasi)) {
            $gambar = $nama_file;
        }
    }

    /* -----------------------------
       INSERT DATA (PREPARED STATEMENT)
    ------------------------------ */
    $query = "INSERT INTO cakes (nama, kategori, harga, stok, gambar) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssiss", $nama, $kategori, $harga, $stok, $gambar);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?status=sukses_tambah");
    exit;
}

include '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">Tambah Kue</h1>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">

        <div class="mb-3">
            <label class="form-label">Nama Kue</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar</label>
            <input type="file" name="gambar" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary mt-2">Simpan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
