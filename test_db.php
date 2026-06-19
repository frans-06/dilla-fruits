<?php
/**
 * FILE TEST KONEKSI DATABASE
 * Lokasi: dilla-fruits/test_db.php
 */

// Panggil file konfigurasi database yang sudah dibuat sebelumnya
require_once __DIR__ . '/config/database.php';

// Lakukan query uji coba sederhana untuk memeriksa versi MySQL
try {
    // Menggunakan variabel koneksi $conn dari database.php
    $result = $conn->query("SELECT VERSION() AS versi");
    $row = $result->fetch_assoc();
    
    echo "<div style='font-family: sans-serif; padding: 20px; max-width: 500px; margin: 50px auto; border: 1px solid #22c55e; background-color: #f0fdf4; color: #166534; border-radius: 8px;'>";
    echo "<h3 style='margin-top: 0;'>🎉 Koneksi Database Sukses!</h3>";
    echo "<p>Aplikasi PHP berhasil terhubung ke MySQL Workbench / XAMPP.</p>";
    echo "<p style='font-family: monospace; background: #dcfce7; padding: 6px; border-radius: 4px;'>Versi MySQL: " . $row['versi'] . "</p>";
    echo "</div>";

} catch (Exception $e) {
    // Bagian ini otomatis tereksekusi jika Catch Error di database.php terpicu
    echo "<div style='font-family: sans-serif; padding: 20px; max-width: 500px; margin: 50px auto; border: 1px solid #ef4444; background-color: #fef2f2; color: #991b1b; border-radius: 8px;'>";
    echo "<h3 style='margin-top: 0;'>❌ Koneksi Database Gagal!</h3>";
    echo "<p>Periksa kembali apakah MySQL di XAMPP/Laragon sudah berstatus <b>Running</b>.</p>";
    echo "</div>";
}