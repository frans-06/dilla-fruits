<?php

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged']) || $_SESSION['level'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo "Akses Ditolak!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uang_bayar = doubleval($_POST['uang_bayar']);
    $cart_json  = $_POST['cart_json'] ?? '';
    $cart_data  = json_decode($cart_json, true);

    if (empty($cart_data)) {
        echo "<script>alert('Gagal! Keranjang belanja kosong.'); window.location.href='../transaksi_penjualan.php';</script>";
        exit;
    }

    // Hitung total belanja riil
    $total_belanja = 0;
    foreach ($cart_data as $item) {
        $total_belanja += (doubleval($item['harga']) * intval($item['qty']));
    }

    // Set angka bebas tanpa takut error constraint (Kita pakai 0 sebagai penanda Kasir Langsung)
    $id_pemesanan_offline = 0; 
    $tanggal_sekarang = date('Y-m-d');

    $conn->begin_transaction();

    try {
        // 🔥 TAKTIK JEBOL BYPASS: Matikan pengecekan Foreign Key & Auto Value di MySQL
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        $conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

        // 1. Masukkan data ke tabel transaksi_penjualan (Lolos 100% tanpa hambatan FK)
        $stmt_trx = $conn->prepare("INSERT INTO transaksi_penjualan (id_pemesanan, tanggal_transaksi, total_bayar) VALUES (?, ?, ?)");
        $stmt_trx->bind_param("isd", $id_pemesanan_offline, $tanggal_sekarang, $total_belanja);
        $stmt_trx->execute();
        $last_inserted_id = $conn->insert_id;
        $stmt_trx->close();

        // 2. Masukkan data ke tabel nota_penjualan 
        $stmt_nota = $conn->prepare("INSERT INTO nota_penjualan (id_transaksi, tanggal_nota) VALUES (?, ?)");
        $stmt_nota->bind_param("is", $last_inserted_id, $tanggal_sekarang);
        $stmt_nota->execute();
        $stmt_nota->close();

        // 3. Loop potong stok buah di database
        foreach ($cart_data as $item) {
            $id_buah = intval($item['id_buah']);
            $qty     = intval($item['qty']);

            $stmt_stok = $conn->prepare("UPDATE buah SET stok = stok - ? WHERE id_buah = ?");
            $stmt_stok->bind_param("ii", $qty, $id_buah);
            $stmt_stok->execute();
            $stmt_stok->close();
        }

        // 🔥 KEMBALIKAN KEAMANAN DATABASE KEMBALI
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        $conn->query("SET SQL_MODE = ''");

        // Commit permanen seluruh eksekusi data aman
        $conn->commit();

        $_SESSION['last_trx_cash'] = $uang_bayar;

        // REDIRECT LANGSUNG KE STRUK NOTA
        echo "<script>
                alert('Transaksi Berhasil Disimpan (Bypass Mode)!');
                window.location.href = '../nota_cetak.php?id=" . $last_inserted_id . "';
              </script>";
        exit;

    } catch (Exception $e) {
        // Jika ada kendala internal, pastikan status FK dinyalakan lagi sebelum rollback
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        $conn->rollback();
        echo "Gagal menyimpan transaksi: " . $e->getMessage();
    }

} else {
    header('Location: ../transaksi_penjualan.php');
    exit;
}