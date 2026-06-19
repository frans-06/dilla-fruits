<?php
/**
 * DAFTAR ANTREAN PEMESANAN ONLINE
 * Lokasi File: admin/pemesanan_online.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

if ($user_level !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='dashboard.php';</script>";
    exit;
}

// Tarik data pemesanan online murni dari database
$query_orders = "
    SELECT p.*, pl.nama_pelanggan, pl.no_hp 
    FROM pemesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    ORDER BY p.id_pemesanan DESC
";
$result_orders = $conn->query($query_orders);
?>

<div class="space-y-6">
    <div>
        <h3 class="text-xl font-bold text-gray-800">Daftar Antrean Pemesanan Online</h3>
        <p class="text-xs text-gray-500 mt-0.5">Validasi status pembayaran dan konfirmasi pengiriman buah pelanggan</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-4">ID Order</th>
                        <th class="px-6 py-4">Nama Pelanggan</th>
                        <th class="px-6 py-4">Tanggal Masuk</th>
                        <th class="px-6 py-4 font-medium">Total Bayar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    <?php if ($result_orders && $result_orders->num_rows > 0) : ?>
                        <?php while ($row = $result_orders->fetch_assoc()) : ?>
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-gray-500">ORD-00<?= $row['id_pemesanan']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($row['nama_pelanggan']); ?></div>
                                    <div class="text-xs text-gray-400"><?= htmlspecialchars($row['no_hp']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    <?= date('d M Y', strtotime($row['tanggal_pemesanan'])); ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $status = strtolower($row['status_pemesanan']);
                                    if ($status === 'menunggu') : ?>
                                        <span class="px-2.5 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-lg uppercase border border-yellow-200">Menunggu</span>
                                    <?php elseif ($status === 'dikonfirmasi') : ?>
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg uppercase border border-blue-200">Dikonfirmasi</span>
                                    <?php elseif ($status === 'selesai') : ?>
                                        <span class="px-2.5 py-1 bg-green-50 text-brand text-xs font-bold rounded-lg uppercase border border-green-200">Selesai</span>
                                    <?php else : ?>
                                        <span class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-lg uppercase border border-red-200"><?= $status; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <?php if ($status === 'menunggu') : ?>
                                            <a href="api/pemesanan_handler.php?id=<?= $row['id_pemesanan']; ?>&aksi=konfirmasi" 
                                               onclick="return confirm('Konfirmasi pesanan ini?')"
                                               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                Setujui
                                            </a>
                                            <a href="api/pemesanan_handler.php?id=<?= $row['id_pemesanan']; ?>&aksi=ditolak" 
                                               onclick="return confirm('Tolak pesanan ini?')"
                                               class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-lg transition border border-red-200">
                                                Tolak
                                            </a>
                                        <?php elseif ($status === 'dikonfirmasi') : ?>
                                            <a href="api/pemesanan_handler.php?id=<?= $row['id_pemesanan']; ?>&aksi=selesai" 
                                               onclick="return confirm('Tandai pesanan ini telah selesai diambil/diterima pelanggan?')"
                                               class="bg-brand hover:bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                                <span>Selesaikan</span>
                                            </a>
                                        <?php else : ?>
                                            <span class="text-xs text-gray-400 font-medium select-none">Tidak Ada Aksi</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                <p class="text-sm">Belum ada antrean masuk dari pemesanan online pembeli.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>