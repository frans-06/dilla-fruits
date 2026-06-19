<?php
/**
 * REVISED DATA BUAH MANAGEMENT WITH EDIT, DELETE & ON/OFF STATUS
 * Lokasi File: admin/data_buah.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

if ($user_level !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='dashboard.php';</script>";
    exit;
}

$query = "SELECT * FROM buah ORDER BY id_buah DESC";
$result = $conn->query($query);
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Kelola Varian Buah</h3>
            <p class="text-xs text-gray-500 mt-0.5">Manajemen inventori master katalog produk dan kontrol display toko</p>
        </div>
        <button onclick="toggleModal('modal-tambah-buah', true)" class="bg-brand hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition flex items-center gap-2 shadow-lg shadow-green-600/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Buah</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-4">Gambar</th>
                        <th class="px-6 py-4">Nama Buah</th>
                        <th class="px-6 py-4">Stok Gudang</th>
                        <th class="px-6 py-4">Harga Jual</th>
                        <th class="px-6 py-4">Status Display</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700" id="buah-table-body">
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr class="hover:bg-gray-50/70 transition" id="row-buah-<?= $row['id_buah']; ?>">
                                <td class="px-6 py-4">
                                    <img src="../assets/uploads/buah/<?= htmlspecialchars($row['gambar']); ?>" alt="Buah" class="w-12 h-12 object-cover rounded-xl border border-gray-100">
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($row['nama_buah']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 <?= ($row['stok'] > 5) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' ?> text-xs font-bold rounded-lg">
                                        <?= htmlspecialchars($row['stok']); ?> Kg
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">Rp <?= number_format($row['harga'], 0, ',', '.'); ?> /Kg</td>
                                <td class="px-6 py-4">
                                    <?php if (!isset($row['status_tampil']) || $row['status_tampil'] === 'on') : ?>
                                        <span class="px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full border border-green-200">🟢 Tampil (ON)</span>
                                    <?php else : ?>
                                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full border border-gray-200">🔴 Hidden (OFF)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($row)); ?>)" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition" title="Edit Data & Status">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button onclick="deleteBuah(<?= $row['id_buah']; ?>, '<?= htmlspecialchars($row['nama_buah']); ?>')" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr id="empty-row-buah">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-sm">Tidak ada data buah di database.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-tambah-buah" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <h4 class="font-bold text-gray-800">Form Isian Buah Baru</h4>
            <button onclick="toggleModal('modal-tambah-buah', false)" class="text-gray-400 hover:text-gray-600 text-lg font-bold transition">&times;</button>
        </div>
        <form action="api/buah_handler.php?action=create" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Nama Buah</label>
                <input type="text" name="nama_buah" required autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm transition">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Harga Jual (/Kg)</label>
                    <input type="number" name="harga" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Stok Awal (Kg)</label>
                    <input type="number" name="stok" required value="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Deskripsi Keterangan</label>
                <textarea name="deskripsi" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm transition resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Foto Gambar Buah</label>
                <input type="file" name="foto_buah" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-brand hover:file:bg-green-100 cursor-pointer">
            </div>
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('modal-tambah-buah', false)" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-100 text-sm font-medium transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand hover:bg-green-700 text-white text-sm font-bold transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edit-buah" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
            <h4 class="font-bold text-gray-800">Ubah Data & Fleksibilitas Display</h4>
            <button onclick="toggleModal('modal-edit-buah', false)" class="text-gray-400 hover:text-gray-600 text-lg font-bold transition">&times;</button>
        </div>
        <form action="api/buah_handler.php?action=update" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto">
            <input type="hidden" name="id_buah" id="edit-id-buah">
            
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Nama Buah</label>
                <input type="text" name="nama_buah" id="edit-nama-buah" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand outline-none text-sm transition">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Harga Baru (/Kg)</label>
                    <input type="number" name="harga" id="edit-harga" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand outline-none text-sm transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Penyesuaian Stok (Kg)</label>
                    <input type="number" name="stok" id="edit-stok" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand outline-none text-sm transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Deskripsi Keterangan</label>
                <textarea name="deskripsi" id="edit-deskripsi" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand outline-none text-sm transition resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-brand mb-2">Saklar Tampil Pada Website Depan</label>
                <select name="status_tampil" id="edit-status-tampil" class="w-full px-4 py-2.5 rounded-xl border border-brand text-sm bg-white outline-none focus:ring-2 focus:ring-brand font-bold text-gray-800">
                    <option value="on">🟢 Tampilkan di Halaman Pelanggan (ON)</option>
                    <option value="off">🔴 Sembunyikan Sementara (OFF)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Ganti Foto Gambar (Kosongkan Jika Tetap)</label>
                <input type="file" name="foto_buah" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 cursor-pointer">
            </div>
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="toggleModal('modal-edit-buah', false)" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-100 text-sm font-medium transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-lg shadow-blue-600/10">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    if (show) modal.classList.remove('hidden');
    else modal.classList.add('hidden');
}

function openEditModal(buah) {
    document.getElementById('edit-id-buah').value = buah.id_buah;
    document.getElementById('edit-nama-buah').value = buah.nama_buah;
    document.getElementById('edit-harga').value = buah.harga;
    document.getElementById('edit-stok').value = buah.stok;
    document.getElementById('edit-deskripsi').value = buah.deskripsi || '';
    document.getElementById('edit-status-tampil').value = buah.status_tampil || 'on';
    
    toggleModal('modal-edit-buah', true);
}

// FUNGSI AJAX FETCH UNTUK DELETE DATA BUAH TANPA RELOAD
async function deleteBuah(id, nama) {
    if (!confirm(`Apakah kamu yakin ingin menghapus data master buah "${nama}"?`)) return;

    try {
        const response = await fetch(`api/buah_handler.php?action=delete&id=${id}`, {
            method: 'GET'
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message);
            // Hapus baris tabel HTML secara langsung menggunakan manipulasi DOM
            const row = document.getElementById(`row-buah-${id}`);
            if (row) row.remove();
            
            // Jika tabel kosong, munculkan teks keterangan kosong
            const tbody = document.getElementById('buah-table-body');
            if (tbody && tbody.children.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">Tidak ada data buah di database.</td></tr>`;
            }
        } else {
            alert('Gagal menghapus: ' + result.message);
        }
    } catch (error) {
        alert('Terjadi kesalahan koneksi saat menghapus data.');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>