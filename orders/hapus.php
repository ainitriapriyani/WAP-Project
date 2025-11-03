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
    $order_id = $_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $koneksi->begin_transaction();

    try {
        // 1. Hapus semua item terkait dari tabel 'order_items'
        $stmt_items = $koneksi->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $stmt_items->close();

        // 2. Hapus pesanan utama dari tabel 'orders'
        $stmt_order = $koneksi->prepare("DELETE FROM orders WHERE id = ?");
        $stmt_order->bind_param("i", $order_id);
        $stmt_order->execute();
        $stmt_order->close();

        // Jika semua berhasil, commit transaksi
        $koneksi->commit();
        header("Location: index.php?status=sukses_hapus");
    } catch (mysqli_sql_exception $exception) {
        // Jika terjadi error, rollback transaksi
        $koneksi->rollback();
        header("Location: index.php?status=gagal_hapus");
    }
} else {
    // Jika tidak ada ID, redirect kembali
    header("Location: index.php");
}

exit();
?>
