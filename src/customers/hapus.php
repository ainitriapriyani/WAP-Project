<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Validasi ID
$id = intval($_GET['id'] ?? 0);

if ($id < 1) {
    header("Location: index.php?status=id_tidak_valid");
    exit;
}

// Hapus data pelanggan
$query = "DELETE FROM customers WHERE id = $id";

if (mysqli_query($koneksi, $query)) {
    header("Location: index.php?status=sukses_hapus");
} else {
    header("Location: index.php?status=gagal");
}

exit;
