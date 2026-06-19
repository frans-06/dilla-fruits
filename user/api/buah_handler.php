<?php
/**
 * DATA ENDPOINT: buah_handler.php
 * Lokasi File: user/api/buah_handler.php
 */
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // Tarik data murni Workbench filter ON
    $query = "SELECT id_buah, nama_buah, harga, stok, deskripsi, gambar FROM buah WHERE status_tampil = 'on' AND stok > 0 ORDER BY id_buah DESC";
    $result = $conn->query($query);

    $daftar_buah = [];
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

    echo json_encode(['success' => true, 'data' => $daftar_buah]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'data' => []]);
    exit;
}