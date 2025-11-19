<?php
// Konfigurasi koneksi ke database
$host = "mysql";       // default untuk XAMPP
$username = "root";        // default username XAMPP
$password = "root";            // default password XAMPP (kosong)
$database = "cake_shop";   // sesuaikan sama nama database hasil import

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Memeriksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone agar sesuai dengan waktu di Indonesia
date_default_timezone_set('Asia/Jakarta');
?>