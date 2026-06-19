<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged']) || $_SESSION['level'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Akses Ditolak!');
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'restock') {
    $id_buah       = intval($_POST['id_buah']);
    $jumlah_tambah = intval($_POST['jumlah_tambah']);

    if ($id_buah > 0 && $jumlah_tambah > 0) {
        $stmt = $conn->prepare("UPDATE buah SET stok = stok + ? WHERE id_buah = ?");
        $stmt->bind_param("ii", $jumlah_tambah, $id_buah);

        if ($stmt->execute()) {
            echo "<script>alert('Stok buah berhasil diperbarui!'); window.location.href = '../stok_buah.php';</script>";
        } else {
            echo "Gagal memperbarui stok: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Data input tidak valid.";
    }
} else {
    header('Location: ../stok_buah.php');
    exit;
}