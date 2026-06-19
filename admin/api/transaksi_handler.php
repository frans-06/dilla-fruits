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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $uang_bayar = doubleval($_POST['uang_bayar']);
    $cart_json  = $_POST['cart_json'] ?? '';
    $cart_data  = json_decode($cart_json, true);

    if (empty($cart_data)) {
        echo "<script>alert('Gagal! Keranjang kosong.'); window.location.href='../transaksi_penjualan.php';</script>";
        exit;
    }

    $total_belanja = 0;
    foreach ($cart_data as $item) {
        $total_belanja += (doubleval($item['harga']) * intval($item['qty']));
    }

    // Jika transaksi offline langsung dari kasir, id_pemesanan diisi NULL (Sesuai perbaikan DB kita)
    $id_pemesanan = null; 
    $tanggal_sekarang = date('Y-m-d');

    $conn->begin_transaction();

    try {
        // 1. Masukkan data ke transaksi_penjualan dengan aman dan bersih
        $stmt_trx = $conn->prepare("INSERT INTO transaksi_penjualan (id_pemesanan, tanggal_transaksi, total_bayar) VALUES (?, ?, ?)");
        // Karena id_pemesanan bisa NULL, kita pakai jenis type data "i" tapi kirim nilainya null
        $stmt_trx->bind_param("isd", $id_pemesanan, $tanggal_sekarang, $total_belanja);
        $stmt_trx->execute();
        $last_inserted_id = $conn->insert_id;
        $stmt_trx->close();

        // 2. Masukkan data ke tabel nota_penjualan
        $stmt_nota = $conn->prepare("INSERT INTO nota_penjualan (id_transaksi, tanggal_nota) VALUES (?, ?)");
        $stmt_nota->bind_param("is", $last_inserted_id, $tanggal_sekarang);
        $stmt_nota->execute();
        $stmt_nota->close();

        // 3. Loop potong stok buah
        foreach ($cart_data as $item) {
            $id_buah = intval($item['id_buah']);
            $qty     = intval($item['qty']);

            $stmt_stok = $conn->prepare("UPDATE buah SET stok = stok - ? WHERE id_buah = ?");
            $stmt_stok->bind_param("ii", $qty, $id_buah);
            $stmt_stok->execute();
            $stmt_stok->close();
        }

        $conn->commit();
        $_SESSION['last_trx_cash'] = $uang_bayar;

        echo "<script>
                alert('Transaksi Berhasil Disimpan!');
                window.location.href = '../nota_cetak.php?id=" . $last_inserted_id . "';
              </script>";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "Gagal menyimpan transaksi: " . $e->getMessage();
    }
} else {
    header('Location: ../transaksi_penjualan.php');
    exit;
}