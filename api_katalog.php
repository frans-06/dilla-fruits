<?php
/**
 * DATA BRIDGE: JSON ENDPOINT UNTUK KATALOG USERPAGE
 * Lokasi File: api_katalog.php (Folder Utama)
 */
require_once __DIR__ . '/config/database.php';

// Atur header respon menjadi tipe data JSON agar bisa dibaca mulus oleh JavaScript Fetch API
header('Content-Type: application/json; charset=utf-8');

try {
    // 📊 KUNCI FILTER UTAMA SKRIPSI: Hanya mengambil status_tampil = 'on' DAN stok > 0
    $query = "SELECT id_buah, nama_buah, harga, stok, deskripsi, gambar FROM buah WHERE status_tampil = 'on' AND stok > 0 ORDER BY id_buah DESC";
    $result = $conn->query($query);

    $daftar_buah = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $daftar_buah[] = [
                'id_buah'   => intval($row['id_buah']),
                'nama_buah' => $row['nama_buah'],
                'harga'     => doubleval($row['harga']),
                'stok'      => intval($row['stok']),
                'deskripsi' => $row['deskripsi'],
                'gambar'    => $row['gambar']
            ];
        }
    }

    // Kembalikan response JSON standard sukses
    echo json_encode([
        'success' => true,
        'message' => 'Berhasil mengambil katalog aktif.',
        'data'    => $daftar_buah
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal sistem API: ' . $e->getMessage(),
        'data'    => []
    ]);
    exit;
}