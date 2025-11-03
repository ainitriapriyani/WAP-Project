<?php
// Selalu mulai session di awal
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
if (session_destroy()) {
    // Jika berhasil, redirect ke halaman login
    header("Location: index.php");
    exit();
} else {
    // Jika ada masalah (jarang terjadi), tampilkan pesan error
    echo "Gagal untuk logout.";
}
?>
