<?php
// Memulai session dan menyertakan file koneksi
session_start();
include '../koneksi.php';

// Cek jika pengguna tidak login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Cek apakah ada ID di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data pelanggan
    $query_delete = "DELETE FROM customers WHERE id = $id";

    if (mysqli_query($koneksi, $query_delete)) {
        // Jika berhasil, redirect dengan status sukses
        header("Location: index.php?status=sukses_hapus");
        exit();
    } else {
        // Jika gagal, redirect dengan status gagal
        header("Location: index.php?status=gagal");
        exit();
    }
} else {
    // Jika tidak ada ID, redirect ke halaman utama pelanggan
    header("Location: index.php");
    exit();
}
?>
