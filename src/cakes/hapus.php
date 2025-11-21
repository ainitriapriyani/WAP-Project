<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    // 1. Ambil nama file gambar
    $stmt = $koneksi->prepare("SELECT gambar FROM cakes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $gambar = $stmt->get_result()->fetch_assoc();

    if (!$gambar) {
        header("Location: index.php?status=id_tidak_ditemukan");
        exit();
    }

    $file_gambar = $gambar['gambar'];

    // 2. Hapus dari database
    $stmt_delete = $koneksi->prepare("DELETE FROM cakes WHERE id=?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();

    // 3. Hapus file gambar
    $file_path = "../assets/img/" . $file_gambar;
    if (!empty($file_gambar) && file_exists($file_path)) {
        unlink($file_path);
    }

    // 4. Reorder ID (biar tetap berurutan)
    // --------------------------------------
    // Reset numbering mulai dari 1
    $koneksi->query("SET @new_id = 0");
    $koneksi->query("
        UPDATE cakes SET id = (@new_id := @new_id + 1) ORDER BY id
    ");

    // Perbaiki ulang AUTO_INCREMENT
    $koneksi->query("
        ALTER TABLE cakes AUTO_INCREMENT = 1
    ");
    // --------------------------------------

    header("Location: index.php?status=sukses_hapus");
    exit();

} else {
    header("Location: index.php?status=tidak_ada_id");
    exit();
}
?>
