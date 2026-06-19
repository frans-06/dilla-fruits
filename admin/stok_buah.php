<?php
/**
 * PURE STOK MANAGEMENT
 * Lokasi File: admin/stok_buah.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

if ($user_level !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='dashboard.php';</script>";
    exit;
}

$query = "SELECT id_buah, nama_buah, stok, updated_at FROM buah ORDER BY stok ASC, nama_buah ASC";
$result = $conn->query($query);
?>

<div class="space-y-6">
    <div>
        <h3 class="text-xl font-bold text-gray-800">Manajemen Pembaruan Stok</h3>
        <p class="text-xs text-gray-500 mt-0.5">Pantau kuantitas sisa produk real-time di database</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-4">Nama Buah</th>
                        <th class="px-6 py-4">Sisa Stok</th>
                        <th class="px-6 py-4">Status Ketersediaan</th>
                        <th class="px-6 py-4">Pembaruan Terakhir</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($row['nama_buah']); ?></td>
                                <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($row['stok']); ?> Kg</td>
                                <td class="px-6 py-4">
                                    <?php if ($row['stok'] <= 0) : ?>
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg uppercase">Habis Total</span>
                                    <?php elseif ($row['stok'] <= 5) : ?>
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-lg uppercase">Kritis</span>
                                    <?php else : ?>
                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg uppercase">Aman</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?= date('d M Y - H:i', strtotime($row['updated_at'])); ?> WIB
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="openRestockModal(<?= $row['id_buah']; ?>, '<?= htmlspecialchars($row['nama_buah']); ?>', <?= $row['stok']; ?>)" 
                                        class="text-brand hover:bg-green-50 px-3 py-1.5 rounded-xl transition font-semibold text-xs border border-green-200 flex items-center gap-1 mx-auto">
                                        <span>Restock</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-sm">Belum ada komoditas buah di database.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-restock" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <h4 class="font-bold text-gray-800">Pengisian Ulang Stok</h4>
            <button onclick="closeModal('modal-restock')" class="text-gray-400 hover:text-gray-600 text-xl font-bold transition">&times;</button>
        </div>
        <form action="api/stok_handler.php?action=restock" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id_buah" id="modal-id-buah">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Produk</label>
                <input type="text" id="modal-nama-buah" disabled class="w-full px-4 py-2 bg-gray-100 rounded-xl text-sm font-semibold text-gray-700 border border-gray-200">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Stok Lama</label>
                    <input type="text" id="modal-stok-lama" disabled class="w-full px-4 py-2 bg-gray-100 rounded-xl text-sm text-gray-600 border border-gray-200">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-brand mb-1">Tambahan (Kg)</label>
                    <input type="number" name="jumlah_tambah" required min="1" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm transition font-bold text-gray-800">
                </div>
            </div>
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-restock')" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-100 text-sm font-medium transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand hover:bg-green-700 text-white text-sm font-bold transition shadow-lg shadow-green-600/10">Update Stok</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestockModal(id, nama, stokSekarang) {
    document.getElementById('modal-id-buah').value = id;
    document.getElementById('modal-nama-buah').value = nama;
    document.getElementById('modal-stok-lama').value = stokSekarang + ' Kg';
    document.getElementById('modal-restock').classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>