<?php
/**
 * LAYOUT STRUK NOTA PENJUALAN KASIR (SINKRON DATA RIIL)
 * Lokasi File: admin/nota_cetak.php
 */
require_once __DIR__ . '/../config/database.php';

session_start();
if (!isset($_SESSION['admin_logged'])) {
    exit('Akses Ditolak!');
}

$id_trx = intval($_GET['id'] ?? 0);

// Tarik data kolaborasi dari tabel transaksi dan nota sekaligus sesuai skema DDL kamu
$query_trx = "
    SELECT t.*, n.id_nota 
    FROM transaksi_penjualan t
    LEFT JOIN nota_penjualan n ON t.id_transaksi = n.id_transaksi
    WHERE t.id_transaksi = ? 
    LIMIT 1
";
$stmt = $conn->prepare($query_trx);
$stmt->bind_param("i", $id_trx);
$stmt->execute();
$result_trx = $stmt->get_result();
$trx = $result_trx->fetch_assoc();
$stmt->close();

if (!$trx) {
    exit('Data Transaksi Tidak Ditemukan!');
}

// Ambil nilai uang cash kasir dari session, lalu hapus sesudahnya
$uang_bayar = $_SESSION['last_trx_cash'] ?? $trx['total_bayar'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nota #<?= $trx['id_nota']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
        }
    </style>
</head>
<body class="bg-gray-50 font-mono text-xs text-gray-800 p-4">

    <div class="max-w-xs mx-auto bg-white p-4 border border-gray-200 shadow-sm rounded-xl">
        <div class="text-center space-y-1 mb-4">
            <h2 class="text-sm font-black uppercase tracking-wide">DILLA FRUIT'S PADANG</h2>
            <p class="text-[10px] text-gray-500">Jl. Kuranji No. 45, Kota Padang</p>
        </div>

        <div class="border-b border-dashed border-gray-300 my-2"></div>

        <div class="space-y-0.5 text-[11px] text-gray-600 mb-3">
            <div class="flex justify-between"><span>No. Nota:</span><span class="font-bold text-gray-900">NTA-00<?= $trx['id_nota']; ?></span></div>
            <div class="flex justify-between"><span>ID Transaksi:</span><span>TRX-00<?= $trx['id_transaksi']; ?></span></div>
            <div class="flex justify-between"><span>Tanggal:</span><span><?= date('d/m/Y', strtotime($trx['tanggal_transaksi'])); ?></span></div>
            <div class="flex justify-between"><span>Kasir:</span><span class="capitalize"><?= $_SESSION['username']; ?></span></div>
        </div>

        <div class="border-b border-dashed border-gray-300 my-2"></div>

        <div class="space-y-2 mb-4 text-[11px]">
            <p class="font-bold text-gray-500 uppercase text-[9px] tracking-wider">Item Belanja</p>
            <div class="space-y-1">
                <div class="flex justify-between font-medium text-gray-900">
                    <span>Pembelian Buah Kasir Offline</span>
                </div>
                <div class="flex justify-between text-gray-500 text-[10px]">
                    <span>Total Rekap Komoditas</span>
                    <span>Rp <?= number_format($trx['total_bayar'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <div class="border-b border-dashed border-gray-300 my-2"></div>

        <div class="space-y-1 text-[11px] mb-6">
            <div class="flex justify-between font-bold text-gray-900 text-xs">
                <span>GRAND TOTAL:</span>
                <span>Rp <?= number_format($trx['total_bayar'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>TUNAI BAYAR:</span>
                <span>Rp <?= number_format($uang_bayar, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between font-medium text-gray-800 pt-1">
                <span>KEMBALIAN:</span>
                <span>Rp <?= number_format($uang_bayar - $trx['total_bayar'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="border-b border-dashed border-gray-300 my-2"></div>

        <div class="text-center text-[10px] text-gray-400 mt-4 space-y-0.5">
            <p class="font-medium">Terima Kasih Atas Kunjungan Anda</p>
            <p>Buah Segar, Harga Bersahabat Setiap Hari</p>
        </div>

        <div class="no-print mt-6 pt-4 border-t border-gray-100 flex gap-2">
            <a href="transaksi_penjualan.php" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs transition">
                Kembali Kasir
            </a>
            <button onclick="window.print()" class="flex-1 bg-brand hover:bg-green-700 text-white font-bold py-2 rounded-xl text-xs transition">
                Cetak Struk
            </button>
        </div>
    </div>

   <script>
        window.addEventListener('DOMContentLoaded', () => {
            // 1. Pancing jendela cetak printer Windows otomatis setelah render selesai
            setTimeout(() => {
                window.print();
            }, 500);

            // 2. Timer Otomatis: Tunggu 15 detik, lalu tendang balik ke halaman kasir
            let sisaWaktu = 15;
            const infoTimer = document.createElement('div');
            
            // Pasang teks notifikasi melayang kecil di layar sebagai penanda visual
            infoTimer.className = "no-print fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-xl text-[11px] shadow-lg font-sans font-semibold border border-gray-800 z-50 animate-bounce";
            infoTimer.id = "countdown-box";
            infoTimer.innerText = "Kembali ke Kasir dalam " + sisaWaktu + " detik...";
            document.body.appendChild(infoTimer);

            const intervalTimer = setInterval(() => {
                sisaWaktu--;
                if (sisaWaktu <= 0) {
                    clearInterval(intervalTimer);
                    // Lempar kembali ke halaman transaksi kasir utama
                    window.location.href = 'transaksi_penjualan.php';
                } else {
                    document.getElementById('countdown-box').innerText = "Kembali ke Kasir dalam " + sisaWaktu + " detik...";
                }
            }, 1000);
        });
    </script>
</body>
</html>     