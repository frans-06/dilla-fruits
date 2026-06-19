<?php
/**
 * DOKUMEN CETAK LAPORAN FORMAL (A4 READY)
 * Lokasi File: admin/laporan_cetak.php
 */
require_once __DIR__ . '/../config/database.php';

session_start();
if (!isset($_SESSION['admin_logged'])) {
    exit('Akses Ditolak!');
}

$bulan_pilihan = isset($_GET['bulan']) ? str_pad($_GET['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Query hitung total keseluruhan untuk lembar tanda tangan formal
$query_rekap = "SELECT COUNT(id_transaksi) as total_trx, SUM(total_bayar) as total_omset FROM transaksi_penjualan WHERE MONTH(tanggal_transaksi) = ? AND YEAR(tanggal_transaksi) = ?";
$stmt = $conn->prepare($query_rekap);
$stmt->bind_param("si", $bulan_pilihan, $tahun_pilihan);
$stmt->execute();
$rekap = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Query rincian tabel
$query_list = "SELECT * FROM transaksi_penjualan WHERE MONTH(tanggal_transaksi) = ? AND YEAR(tanggal_transaksi) = ? ORDER BY id_transaksi ASC";
$stmt_list = $conn->prepare($query_list);
$stmt_list->bind_param("si", $bulan_pilihan, $tahun_pilihan);
$stmt_list->execute();
$result_list = $stmt_list->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_Penjualan_<?= $bulan_pilihan; ?>_<?= $tahun_pilihan; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-8">

    <div class="max-w-4xl mx-auto bg-white p-10 shadow-sm border border-gray-200 rounded-xl">
        
        <div class="text-center border-b-4 border-gray-900 pb-5 mb-6 relative">
            <h1 class="text-2xl font-black uppercase tracking-wider text-gray-900">DILLA FRUIT'S PADANG</h1>
            <p class="text-xs text-gray-500 font-medium mt-1">Sistem Informasi Manajemen Penjualan Buah Berbasis Web</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Jl. Kuranji No. 45, Kota Padang, Sumatera Barat | Telp: 0812-3456-7890</p>
        </div>

        <div class="text-center space-y-1 mb-8">
            <h2 class="text-base font-bold uppercase underline tracking-wide text-gray-800">LAPORAN REKAPITULASI PENDAPATAN TOKO</h2>
            <p class="text-xs text-gray-500 font-medium">Periode Bulan: <?= $nama_bulan[$bulan_pilihan]; ?> <?= $tahun_pilihan; ?></p>
        </div>

        <table class="w-full text-left border border-gray-300 text-xs mb-8">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-700 uppercase">
                    <th class="px-4 py-3 border-r border-gray-300 text-center w-16">No</th>
                    <th class="px-4 py-3 border-r border-gray-300">ID Transaksi</th>
                    <th class="px-4 py-3 border-r border-gray-300">Tanggal Rekap</th>
                    <th class="px-4 py-3 text-right">Nilai Pendapatan (Omset)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300 text-gray-700">
                <?php 
                $no = 1;
                if ($result_list && $result_list->num_rows > 0) : 
                    while ($row = $result_list->fetch_assoc()) :
                ?>
                    <tr>
                        <td class="px-4 py-2.5 border-r border-gray-300 text-center"><?= $no++; ?></td>
                        <td class="px-4 py-2.5 border-r border-gray-300 font-mono font-bold text-gray-500">TRX-00<?= $row['id_transaksi']; ?></td>
                        <td class="px-4 py-2.5 border-r border-gray-300"><?= date('d-m-Y', strtotime($row['tanggal_transaksi'])); ?></td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-900">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                    </tr>
                <?php 
                    endwhile; 
                else : 
                ?>
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400 font-medium">Tidak ada rincian rekap transaksi pada bulan ini.</td>
                    </tr>
                <?php endif; ?>
                
                <tr class="bg-gray-50 font-bold border-t border-gray-300 text-gray-900">
                    <td colspan="3" class="px-4 py-3 border-r border-gray-300 text-right uppercase tracking-wider">Total Akumulasi Pembukuan:</td>
                    <td class="px-4 py-3 text-right text-sm text-green-700">Rp <?= number_format($rekap['total_omset'] ?? 0, 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end text-xs text-gray-800 mt-12">
            <div class="w-64 text-center space-y-16">
                <div>
                    <p class="text-gray-500">Padang, <?= date('d F Y'); ?></p>
                    <p class="font-bold text-gray-800 mt-0.5">Pemilik Toko Dilla Fruit's</p>
                </div>
                <div>
                    <p class="font-black text-gray-900 underline uppercase">Dilla Rahmawati</p>
                    <p class="text-gray-400 text-[11px] font-medium mt-0.5">NIDN. Owner-Management</p>
                </div>
            </div>
        </div>

        <div class="no-print mt-12 pt-6 border-t border-gray-100 flex justify-end gap-3">
            <button onclick="window.close()" class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold transition">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-lg shadow-blue-600/10">
                Cetak Cetak Dokumen
            </button>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>
</html>