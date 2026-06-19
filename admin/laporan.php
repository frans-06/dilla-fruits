<?php
/**
 * MANAJEMEN LAPORAN SINKRON BAB 4 (PENJUALAN & PERSEDIAAN)
 * Lokasi File: admin/laporan.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$bulan_ini = date('m');
$tahun_ini = date('Y');

// Parameter Filter
$filter_jenis = $_GET['jenis_laporan'] ?? 'penjualan';
$filter_bulan = $_GET['bulan'] ?? $bulan_ini;
$filter_tahun = $_GET['tahun'] ?? $tahun_ini;

// Array Bulan
$arr_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-6">
    <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-3">Filter Laporan Dilla Fruit's</h3>
    
    <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Jenis Laporan</label>
            <select name="jenis_laporan" class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-brand font-medium">
                <option value="penjualan" <?= ($filter_jenis == 'penjualan') ? 'selected' : ''; ?>>Laporan Penjualan</option>
                <option value="persediaan" <?= ($filter_jenis == 'persediaan') ? 'selected' : ''; ?>>Laporan Persediaan Buah</option>
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Bulan</label>
            <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-brand">
                <?php foreach ($arr_bulan as $num => $name) : ?>
                    <option value="<?= $num; ?>" <?= ($num === $filter_bulan) ? 'selected' : ''; ?>><?= $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1">
            <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Tahun</label>
            <select name="tahun" class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-brand">
                <?php for($i = 2024; $i <= date('Y')+1; $i++) : ?>
                    <option value="<?= $i; ?>" <?= ($i == $filter_tahun) ? 'selected' : ''; ?>><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="px-6 py-2 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg transition">Tampilkan</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-800">
            <?= ($filter_jenis === 'penjualan') ? "Data Penjualan : {$arr_bulan[$filter_bulan]} {$filter_tahun}" : "Data Persediaan Buah Terkini"; ?>
        </h3>
        
        <a href="laporan_cetak.php?jenis=<?= $filter_jenis; ?>&bln=<?= $filter_bulan; ?>&thn=<?= $filter_tahun; ?>" target="_blank" 
           class="text-xs px-4 py-2 bg-brand text-white font-bold rounded-xl hover:bg-green-700 transition">
            Cetak Dokumen
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white text-xs uppercase tracking-wider text-gray-400 border-b border-gray-200">
                    <?php if($filter_jenis === 'penjualan') : ?>
                        <th class="p-4 font-bold">Tanggal</th>
                        <th class="p-4 font-bold">ID Transaksi</th>
                        <th class="p-4 font-bold">Sumber Order</th>
                        <th class="p-4 font-bold text-right">Total Pemasukan</th>
                    <?php else : ?>
                        <th class="p-4 font-bold">ID Buah</th>
                        <th class="p-4 font-bold">Nama Komoditas</th>
                        <th class="p-4 font-bold">Harga Jual</th>
                        <th class="p-4 font-bold">Sisa Stok Fisik</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <?php
                if ($filter_jenis === 'penjualan') {
                    // LOGIKA TABEL PENJUALAN
                    $q_jual = "SELECT * FROM transaksi_penjualan WHERE MONTH(tanggal_transaksi) = '$filter_bulan' AND YEAR(tanggal_transaksi) = '$filter_tahun' ORDER BY tanggal_transaksi DESC";
                    $res_jual = $conn->query($q_jual);
                    $total_omset = 0;

                    if ($res_jual && $res_jual->num_rows > 0) {
                        while ($row = $res_jual->fetch_assoc()) {
                            $total_omset += $row['total_bayar'];
                            $isOnline = !empty($row['id_pemesanan']) ? 'Online (Web)' : 'Offline (Toko)';
                            $badgeColor = !empty($row['id_pemesanan']) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700';
                            
                            echo "<tr class='hover:bg-gray-50 transition'>";
                            echo "<td class='p-4 text-gray-500 font-medium'>" . date('d M Y', strtotime($row['tanggal_transaksi'])) . "</td>";
                            echo "<td class='p-4 font-bold text-gray-800'>TRX-" . $row['id_transaksi'] . "</td>";
                            echo "<td class='p-4'><span class='px-2.5 py-1 text-[10px] font-bold rounded-lg {$badgeColor}'>" . $isOnline . "</span></td>";
                            echo "<td class='p-4 font-black text-brand text-right'>Rp " . number_format($row['total_bayar'], 0, ',', '.') . "</td>";
                            echo "</tr>";
                        }
                        echo "<tr class='bg-green-50/50'><td colspan='3' class='p-4 text-right font-bold text-gray-600'>TOTAL PENDAPATAN:</td><td class='p-4 font-black text-brand text-xl text-right border-t border-green-200'>Rp " . number_format($total_omset, 0, ',', '.') . "</td></tr>";
                    } else {
                        echo "<tr><td colspan='4' class='p-8 text-center text-gray-400'>Tidak ada data transaksi pada periode ini.</td></tr>";
                    }

                } else {
                    // LOGIKA TABEL PERSEDIAAN STOK BUAH
                    $q_stok = "SELECT * FROM buah ORDER BY nama_buah ASC";
                    $res_stok = $conn->query($q_stok);
                    if ($res_stok && $res_stok->num_rows > 0) {
                        while ($row = $res_stok->fetch_assoc()) {
                            $stokColor = ($row['stok'] <= 5) ? 'text-red-600' : 'text-gray-800';
                            echo "<tr class='hover:bg-gray-50 transition'>";
                            echo "<td class='p-4 font-mono font-bold text-gray-500'>B-" . $row['id_buah'] . "</td>";
                            echo "<td class='p-4 font-bold text-gray-800'>" . htmlspecialchars($row['nama_buah']) . "</td>";
                            echo "<td class='p-4 font-medium text-gray-600'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                            echo "<td class='p-4 font-black {$stokColor}'>" . $row['stok'] . " Kg</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='p-8 text-center text-gray-400'>Data persediaan buah kosong.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>