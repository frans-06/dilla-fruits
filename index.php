<?php
/**
 * ROOT REDIRECTOR GATEWAY
 * Lokasi File: /index.php (Root Project)
 * Fungsi: Mengarahkan traffic utama domain/localhost langsung ke halaman pelanggan (user page)
 */

// Tendang traffic pengunjung otomatis ke dalam sub-folder user page
header('Location: user/index.php');
exit;