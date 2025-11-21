<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM customers WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

/* --------------------------------
   RAPIHKAN NOMOR ID MENJADI 1,2,3…
----------------------------------*/
$koneksi->query("SET @num := 0");
$koneksi->query("UPDATE customers SET id = (@num := @num + 1) ORDER BY id");
$koneksi->query("ALTER TABLE customers AUTO_INCREMENT = 1");

header("Location: index.php?status=sukses_hapus");
exit;
