<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

include '../koneksi.php'; // Koneksi dibutuhkan untuk kedua proses

$id = null;
// Ambil ID dari POST (saat form disubmit) atau dari GET (saat halaman pertama kali dibuka)
if (isset($_POST['id'])) {
    $id = $_POST['id'];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Jika tidak ada ID sama sekali, redirect ke halaman index
if (!$id || !is_numeric($id)) {
    header("Location: index.php?status=id_tidak_valid");
    exit();
}


// Blok untuk memproses update data (Method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $gambar_lama = $_POST['gambar_lama'];
    $gambar_untuk_db = $gambar_lama; // Default menggunakan gambar lama

    // Proses jika ada gambar baru yang diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0 && !empty($_FILES['gambar']['name'])) {
        $target_dir = "../assets/img/";
        $new_file_name = uniqid() . '-' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $new_file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        if (in_array($file_type, $allowed_types)) {
            // Coba upload file baru
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // Jika berhasil, hapus gambar lama (jika ada)
                if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                    unlink($target_dir . $gambar_lama);
                }
                $gambar_untuk_db = $new_file_name; // Gunakan gambar baru
            } else {
                header("Location: edit.php?id=$id&status=gagal_upload");
                exit();
            }
        } else {
            header("Location: edit.php?id=$id&status=tipe_file_salah");
            exit();
        }
    }

    // Query untuk update data
    $query_update = "UPDATE cakes SET nama = '$nama', kategori = '$kategori', harga = '$harga', stok = '$stok', gambar = '$gambar_untuk_db' WHERE id = $id";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: index.php?status=sukses_edit");
        exit();
    } else {
        die("ERROR: Gagal mengupdate data. " . mysqli_error($koneksi));
    }
}

// Blok untuk mengambil data yang akan diedit (selalu dijalankan untuk mengisi form)
$query_get = "SELECT * FROM cakes WHERE id = $id";
$result_get = mysqli_query($koneksi, $query_get);
$cake = mysqli_fetch_assoc($result_get);

if (!$cake) {
    header("Location: index.php?status=data_tidak_ditemukan");
    exit();
}

// Sertakan header setelah semua logika
include '../includes/header.php';
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Data Kue</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Kue</a></li>
        <li class="breadcrumb-item active">Edit Kue</li>
    </ol>

    <?php
    // Blok untuk menampilkan notifikasi error upload
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'gagal_upload') {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> Terjadi kesalahan saat mengunggah gambar baru. Data tidak berubah.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } elseif ($_GET['status'] == 'tipe_file_salah') {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> Tipe file tidak diizinkan. Harap unggah file JPG, JPEG, PNG, atau GIF.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Form Edit Kue
        </div>
        <div class="card-body">
            <form action="edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $cake['id']; ?>">
                <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($cake['gambar']); ?>">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kue</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($cake['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" class="form-control" id="kategori" name="kategori" value="<?= htmlspecialchars($cake['kategori']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="<?= $cake['harga']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" value="<?= $cake['stok']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gambar Produk Saat Ini</label><br>
                    <?php if (!empty($cake['gambar']) && file_exists("../assets/img/" . $cake['gambar'])) : ?>
                        <img src="../assets/img/<?= htmlspecialchars($cake['gambar']); ?>" alt="<?= htmlspecialchars($cake['nama']); ?>" width="150" class="img-thumbnail mb-2">
                    <?php else : ?>
                        <p class="text-muted">Tidak ada gambar.</p>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Ganti Gambar Produk</label>
                    <input type="file" class="form-control" id="gambar" name="gambar">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

