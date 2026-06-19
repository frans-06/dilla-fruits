<?php
/**
 * SAFE REAL-TIME DASHBOARD MANAGEMENT
 * Lokasi File: admin/dashboard.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$total_pendapatan = 0;
$total_varian     = 0;
$total_antrean    = 0;
$total_kritis     = 0;
$chart_months     = [];
$chart_totals     = [];

// 1. HITUNG OMSET REAL-TIME (Murni dari total_bayar kasir offline & online)
$res_omset = $conn->query("SELECT SUM(total_bayar) as total FROM transaksi_penjualan");
if ($res_omset) {
    $data_omset = $res_omset->fetch_assoc();
    $total_pendapatan = $data_omset['total'] ?? 0;
}

// 2. HITUNG TOTAL VARIAN JENIS BUAH
$res_buah = $conn->query("SELECT COUNT(*) as total_varian FROM buah");
if ($res_buah) {
    $data_buah = $res_buah->fetch_assoc();
    $total_varian = $data_buah['total_varian'] ?? 0;
}

// 3. HITUNG ANTREAN ORDER ONLINE (Status: menunggu & dikonfirmasi)
$res_order = $conn->query("SELECT COUNT(*) as total FROM pemesanan WHERE status_pemesanan IN ('menunggu', 'dikonfirmasi')");
if ($res_order) {
    $total_antrean = $res_order->fetch_assoc()['total'] ?? 0;
}

// 4. HITUNG JUMLAH STOK KRITIS (<= 5 Kg)
$res_kritis = $conn->query("SELECT COUNT(*) as total FROM buah WHERE stok <= 5");
if ($res_kritis) {
    $total_kritis = $res_kritis->fetch_assoc()['total'] ?? 0;
}

// 5. TARIK DATA GRAFIK BULANAN TAHUN BERJALAN (FIX ONLY_FULL_GROUP_BY BUG)
$query_chart = "
    SELECT DATE_FORMAT(tanggal_transaksi, '%M') as bulan, SUM(total_bayar) as total 
    FROM transaksi_penjualan 
    WHERE YEAR(tanggal_transaksi) = YEAR(CURDATE())
    GROUP BY MONTH(tanggal_transaksi), DATE_FORMAT(tanggal_transaksi, '%M')
    ORDER BY MONTH(tanggal_transaksi) ASC
";
$res_chart = $conn->query($query_chart);
if ($res_chart) {
    while ($row_chart = $res_chart->fetch_assoc()) {
        $chart_months[] = $row_chart['bulan'];
        $chart_totals[] = (double)$row_chart['total'];
    }
}
?>

<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Total Penjualan</span>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
            </div>
            <div class="p-3 bg-green-50 text-brand rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Varian Buah</span>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $total_varian; ?> Jenis</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Order Antrean</span>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $total_antrean; ?> Pesanan</h3>
            </div>
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
        </div>

        <a href="stok_buah.php" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:border-red-300 transition">
            <div>
                <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Peringatan Stok</span>
                <h3 class="text-2xl font-bold <?= ($total_kritis > 0) ? 'text-red-600' : 'text-gray-800'; ?> mt-1"><?= $total_kritis; ?> Kritis</h3>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Grafik Tren Penjualan</h3>
        <p class="text-xs text-gray-400 mb-6">Monitoring komparasi rekap omset pendapatan asli dari database</p>
        
        <div class="relative h-80 w-full">
            <?php if (!empty($chart_months)) : ?>
                <canvas id="realtimeChart"></canvas>
            <?php else : ?>
                <div class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex flex-col items-center justify-center text-center p-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
                    <p class="text-sm font-semibold text-gray-500">Belum Ada Data Transaksi Bulanan</p>
                    <p class="text-xs text-gray-400 mt-0.5">Grafik otomatis terbentuk setelah rekap penjualan kasir terekam di database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../assets/js/utils.js"></script>

<script src="../assets/js/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const monthsData = <?= json_encode($chart_months); ?>;
    const totalsData = <?= json_encode($chart_totals); ?>;
    
    // Fungsi dipanggil setelah semua dependensi library di atas siap 100%
    if (monthsData.length > 0) {
        initSalesTrendChart('realtimeChart', monthsData, totalsData);
    }
});
</script>

<?php 
require_once __DIR__ . '/includes/footer.php'; 
?>