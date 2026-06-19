<?php
/**
 * PELANGGAN AUTH GUARD
 * Lokasi File: user/includes/auth_check.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah session login pelanggan sudah terbentuk atau belum
if (!isset($_SESSION['pelanggan_logged']) || $_SESSION['pelanggan_logged'] !== true) {
    echo "<script>
            alert('Akses Terbatas! Silakan login terlebih dahulu untuk mengakses halaman ini.');
            window.location.href = 'login.php';
          </script>";
    exit;
}