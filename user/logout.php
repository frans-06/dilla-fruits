<?php
/**
 * LOGOUT HANDLER PELANGGAN
 * Lokasi File: user/logout.php
 */
session_start();

// Cukup hapus session yang berkaitan dengan pelanggan saja agar session admin aman
unset($_SESSION['pelanggan_logged']);
unset($_SESSION['id_pelanggan']);
unset($_SESSION['nama_pelanggan']);
unset($_SESSION['email_pelanggan']);

header('Location: index.php');
exit;