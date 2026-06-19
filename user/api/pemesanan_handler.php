<?php
/**
 * BACKEND PEMESANAN HANDLER (API USER)
 * Lokasi File: user/api/pemesanan_handler.php
 */
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if (!isset($_SESSION['pelanggan_logged'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi login Anda tidak valid!']); exit;
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// --- CASE 1: PROSES CHECKOUT INSERT TRANSAKSI PENJUALAN ONLINE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout') {
    $cart_json = $_POST['cart_json'] ?? '';
    $metode_bayar = sanitizeInput($_POST['metode_pembayaran'] ?? 'transfer');
    $cart_data = json_decode($cart_json, true);

    if (empty($cart_data)) {
        echo json_encode(['success' => false, 'message' => 'Data keranjang belanja kosong.']); exit;
    }

    // Hitung akumulasi total harga riil dari data JSON
    $total_harga = 0;
    foreach ($cart_data as $item) {
        $total_harga += (doubleval($item['harga']) * intval($item['qty']));
    }

    $tanggal_sekarang = date('Y-m-d');

    // Mulai Database Transaction aman (Anti data corrup jika salah satu query macet)
    $conn->begin_transaction();

    try {
        // 1. Masukkan data ke tabel induk header: pemesanan
        $stmt_order = $conn->prepare("INSERT INTO pemesanan (id_pelanggan, tanggal_pemesanan, total_harga, status_pemesanan, metode_pembayaran) VALUES (?, ?, ?, 'menunggu', ?)");
        $stmt_order->bind_param("isds", $id_pelanggan, $tanggal_sekarang, $total_harga, $metode_bayar);
        $stmt_order->execute();
        $id_pemesanan_baru = $conn->insert_id;
        $stmt_order->close();

        // 2. Loop array masukkan item ke tabel detail_pemesanan
        foreach ($cart_data as $item) {
            $id_buah  = intval($item['id_buah']);
            $qty      = intval($item['qty']);
            $subtotal = doubleval($item['harga']) * $qty;

            // Validasi sisa kecukupan stok fisik di database sekali lagi sebelum checkout kunci
            $check_stok = $conn->query("SELECT stok, nama_buah FROM buah WHERE id_buah = $id_buah")->fetch_assoc();
            if ($qty > $check_stok['stok']) {
                throw new Exception("Stok untuk buah '" . $check_stok['nama_buah'] . "' mendadak menipis! Sisa stok terkini: " . $check_stok['stok'] . " Kg. Silakan sesuaikan kembali.");
            }

            $stmt_detail = $conn->prepare("INSERT INTO detail_pemesanan (id_pemesanan, id_buah, jumlah, subtotal) VALUES (?, ?, ?, ?)");
            $stmt_detail->bind_param("iiid", $id_pemesanan_baru, $id_buah, $qty, $subtotal);
            $stmt_detail->execute();
            $stmt_detail->close();
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Berhasil mengirim pesanan online!']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CASE 2: PEMBATALAN ORDER MANDIRI OLEH USER ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'cancel_order') {
    $id_order = intval($_GET['id'] ?? 0);

    if ($id_order > 0) {
        // Proteksi: Hanya bisa membatalkan pesanan miliknya sendiri dan yang berstatus 'menunggu'
        $check = $conn->query("SELECT status_pemesanan FROM pemesanan WHERE id_pemesanan = $id_order AND id_pelanggan = $id_pelanggan LIMIT 1")->fetch_assoc();
        
        if (!$check) {
            echo json_encode(['success' => false, 'message' => 'Data order tidak valid atau milik pengguna lain.']); exit;
        }
        if (strtolower($check['status_pemesanan']) !== 'menunggu') {
            echo json_encode(['success' => false, 'message' => 'Maaf, pesanan Anda tidak bisa dibatalkan karena sudah masuk tahap proses konfirmasi admin/gudang!']); exit;
        }

        $stmt = $conn->prepare("UPDATE pemesanan SET status_pemesanan = 'ditolak' WHERE id_pemesanan = ?");
        $stmt->bind_param("i", $id_order);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Pesanan Anda telah berhasil dibatalkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        $stmt->close();
    }
    exit;
}