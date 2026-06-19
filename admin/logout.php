<?php
/**
 * BACKEND HANDLER: LOGOUT OPERASIONAL MANAGEMENT
 * Lokasi File: admin/logout.php
 */
session_start();

// Bersihkan data session administrasi global
unset($_SESSION['admin_logged']);
unset($_SESSION['username']);
unset($_SESSION['level']);

// Hancurkan session total secara aman
session_destroy();

// Kembalikan pengguna ke gerbang form login manajemen admin
header('Location: login.php');
exit;