<?php
/**
 * TRACKING RIWAYAT ORDER PELANGGAN
 * Lokasi File: user/detail_pesanan.php
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['pelanggan_logged'])) {
    header('Location: login.php'); exit;
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil riwayat pemesanan online milik pembeli yang bersangkutan
$query = "SELECT * FROM pemesanan WHERE id_pelanggan = $id_pelanggan ORDER BY id_pemesanan DESC";
$result_orders = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Saya - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-4xl w-full mx-auto px-4 py-10 flex-1 space-y-6">
        <div>
            <h3 class="text-xl font-black text-gray-900">Riwayat Pesanan Saya</h3>
            <p class="text-xs text-gray-400 mt-0.5">Pantau status konfirmasi antrean dan proses kirim buah segarmu secara real-time</p>
        </div>

        <div class="space-y-4">
            <?php if ($result_orders && $result_orders->num_rows > 0) : ?>
                <?php while ($order = $result_orders->fetch_assoc()) : ?>
                    <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 transition hover:border-brand/40">
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-gray-600 text-sm">ORD-00<?= $order['id_pemesanan']; ?></span>
                                <span class="text-gray-400 font-medium"><?= date('d M Y', strtotime($order['tanggal_pemesanan'])); ?></span>
                            </div>
                            <p class="text-sm font-black text-gray-900">Total Belanja: <span class="text-brand">Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></span></p>
                            <p class="text-gray-400 font-medium">Metode: <span class="uppercase font-bold text-gray-600"><?= $order['metode_pembayaran']; ?></span></p>
                            
                            <!-- Dropdown List rincian item buah di dalam pesanan tersebut -->
                            <div class="text-[11px] text-gray-400 pt-1 font-medium">
                                <span class="font-bold text-gray-500">Rincian Komoditas:</span>
                                <ul class="list-disc list-inside mt-0.5 text-gray-600 pl-1 space-y-0.5">
                                    <?php
                                    $id_p = $order['id_pemesanan'];
                                    $res_items = $conn->query("SELECT dp.*, b.nama_buah FROM detail_pemesanan dp JOIN buah b ON dp.id_buah = b.id_buah WHERE dp.id_pemesanan = $id_p");
                                    while($item = $res_items->fetch_assoc()) {
                                        echo "<li>" . htmlspecialchars($item['nama_buah']) . " (" . $item['jumlah'] . " Kg)</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Status Badge & Tombol Aksi Pembatalan -->
                        <div class="flex items-center gap-4 justify-between md:justify-end">
                            <div>
                                <?php 
                                $status = strtolower($order['status_pemesanan']);
                                if ($status === 'menunggu') : ?>
                                    <span class="px-2.5 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-lg border border-yellow-200 uppercase">Menunggu</span>
                                <?php elseif ($status === 'dikonfirmasi') : ?>
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-200 uppercase">Dikonfirmasi</span>
                                <?php elseif ($status === 'selesai') : ?>
                                    <span class="px-2.5 py-1 bg-green-50 text-brand text-xs font-bold rounded-lg border border-green-200 uppercase">Selesai</span>
                                <?php else : ?>
                                    <span class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-lg border border-red-200 uppercase">Batal / Ditolak</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($status === 'menunggu') : ?>
                                <button onclick="cancelOrder(<?= $order['id_pemesanan']; ?>)" 
                                    class="px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl text-xs transition">
                                    Batalkan Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="bg-white rounded-2xl border border-gray-200 text-center py-16 text-gray-400">
                    <p class="text-sm font-medium">Anda belum memiliki riwayat pemesanan online.</p>
                    <a href="index.php" class="text-brand font-bold text-xs mt-2 inline-block hover:underline">&rarr; Belanja Buah Segar Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        async function cancelOrder(id) {
            if (!confirm('Apakah Anda yakin ingin membatalkan pesanan online ini secara sepihak?')) return;

            try {
                const response = await fetch(`api/pemesanan_handler.php?action=cancel_order&id=${id}`);
                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Gagal membatalkan: ' + data.message);
                }
            } catch (err) {
                alert('Gangguan koneksi sistem pembatalan.');
            }
        }
    </script>
</body>
</html>