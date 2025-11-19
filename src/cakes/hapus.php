<?php
// Memulai session dan menyertakan file koneksi
session_start();
include '../koneksi.php';

// Cek jika pengguna tidak login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Ambil nama file gambar dari database sebelum menghapus record
    $query_get = "SELECT gambar FROM cakes WHERE id = $id";
    $result_get = mysqli_query($koneksi, $query_get);
    
    if ($result_get && mysqli_num_rows($result_get) > 0) {
        $cake = mysqli_fetch_assoc($result_get);
        $gambar_file = $cake['gambar'];

        // 2. Hapus record kue dari database
        $query_delete = "DELETE FROM cakes WHERE id = $id";
        if (mysqli_query($koneksi, $query_delete)) {
            
            // 3. Hapus file gambar dari folder assets/img/
            $file_path = "../assets/img/" . $gambar_file;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Redirect dengan status sukses
            header("Location: index.php?status=sukses_hapus");
            exit();
        } else {
            // Redirect dengan status gagal jika query delete gagal
            header("Location: index.php?status=gagal");
            exit();
        }
    } else {
        // Redirect jika data kue tidak ditemukan
        header("Location: index.php?status=gagal");
        exit();
    }
} else {
    // Redirect jika tidak ada ID
    header("Location: index.php");
    exit();
}
?>
