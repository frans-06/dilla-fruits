<?php
/**
 * BACKEND HANDLER: UPDATE STATUS ORDER ONLINE (SINKRON DATA DDL)
 * Lokasi File: admin/api/pemesanan_status.php
 */
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged']) || $_SESSION['level'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo "Akses Ditolak!";
    exit;
}

$id_pemesanan = intval($_GET['id'] ?? 0);
$aksi         = sanitizeInput($_GET['aksi'] ?? '');

if ($id_pemesanan > 0 && !empty($aksi)) {
    
    // Tentukan string status baru berdasarkan pilihan tombol di UI
    $status_baru = 'menunggu';
    if ($aksi === 'konfirmasi') {
        $status_baru = 'dikonfirmasi';
    } elseif ($aksi === 'ditolak') {
        $status_baru = 'ditolak';
    } elseif ($aksi === 'selesai') {
        $status_baru = 'selesai';
    }

    // Eksekusi query update murni menyasar kolom status_pemesanan sesuai DDL SQL kamu
    $stmt = $conn->prepare("UPDATE pemesanan SET status_pemesanan = ? WHERE id_pemesanan = ?");
    $stmt->bind_param("si", $status_baru, $id_pemesanan);

    if ($stmt->execute()) {
        
        // ⚠️ OTOMATISASI PENJUALAN: Jika status diubah ke 'selesai', masukkan rekapnya ke tabel transaksi_penjualan!
        if ($status_baru === 'selesai') {
            
            // Tarik nominal total_harga dari order tersebut
            $get_price = $conn->query("SELECT total_harga FROM pemesanan WHERE id_pemesanan = $id_pemesanan");
            $order_data = $get_price->fetch_assoc();
            $total_bayar = $order_data['total_harga'] ?? 0;
            $tanggal_sekarang = date('Y-m-d');

            // Masukkan log data ke tabel transaksi_penjualan agar grafik dashboard mendeteksi pemasukan online ini
            $stmt_trx = $conn->prepare("INSERT INTO transaksi_penjualan (id_pemesanan, tanggal_transaksi, total_bayar) VALUES (?, ?, ?)");
            $stmt_trx->bind_param("isd", $id_pemesanan, $tanggal_sekarang, $total_bayar);
            $stmt_trx->execute();
            $stmt_trx->close();
        }

        echo "<script>
                alert('Status pesanan online berhasil diperbarui!');
                window.location.href = '../pemesanan_online.php';
              </script>";
        exit;
    } else {
        echo "Gagal memperbarui status data: " . $conn->error;
    }
    $stmt->close();

} else {
    header('Location: ../pemesanan_online.php');
    exit;
}