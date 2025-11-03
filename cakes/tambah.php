<?php
// Pindahkan seluruh blok pemrosesan form ke paling atas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../koneksi.php';

    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $gambar = '';

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        // Buat nama file unik untuk menghindari konflik
        $new_file_name = uniqid() . '-' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $new_file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        // Validasi tipe file
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $new_file_name;
            } else {
                // Redirect dengan status gagal jika upload error
                header("Location: tambah.php?status=gagal_upload");
                exit();
            }
        } else {
            // Redirect dengan status gagal jika tipe file salah
            header("Location: tambah.php?status=tipe_file_salah");
            exit();
        }
    }

    $query = "INSERT INTO cakes (nama, kategori, harga, stok, gambar) VALUES ('$nama', '$kategori', '$harga', '$stok', '$gambar')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php?status=sukses_tambah");
        exit();
    } else {
        die("ERROR: Gagal menyimpan data. " . mysqli_error($koneksi));
    }
}

// Sertakan header setelah logika
include '../includes/header.php';
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Kue Baru</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Kue</a></li>
        <li class="breadcrumb-item active">Tambah Kue</li>
    </ol>

    <?php
    // Blok untuk menampilkan notifikasi error upload
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'gagal_upload') {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> Terjadi kesalahan saat mengunggah gambar. Silakan coba lagi.
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
            <i class="fas fa-plus-circle me-1"></i>
            Form Tambah Data Kue
        </div>
        <div class="card-body">
            <form action="tambah.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Kue</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" class="form-control" id="kategori" name="kategori" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="harga" name="harga" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Produk</label>
                    <input type="file" class="form-control" id="gambar" name="gambar">
                </div>
                <button type="submit" class="btn btn-primary">Simpan Kue</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

