<?php
/**
 * AJAX ENDPOINT: AMBIL DAFTAR KATALOG BUAH (READ-ONLY)
 * Lokasi File: user/api_katalog.php
 */
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Arsitektur query dasar: Wajib berstatus TAMPIL (ON) dan stok tersedia di atas 0 Kg
$query = "SELECT id_buah, nama_buah, harga, stok, deskripsi, gambar FROM buah WHERE status_tampil = 'on' AND stok > 0";

if (!empty($search)) {
    $query .= " AND nama_buah LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

$data_buah = [];
while ($row = $result->fetch_assoc()) {
    $data_buah[] = $row;
}

$stmt->close();

// Kembalikan data dalam format enkapsulasi JSON murni yang rapi
echo json_encode([
    'success' => true,
    'count'   => count($data_buah),
    'data'    => $data_buah
]);
exit;