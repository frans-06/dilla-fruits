<?php
/**
 * DETAIL INFORMASI BUAH & ADD TO CART
 * Lokasi File: user/detail_buah.php
 */
require_once __DIR__ . '/../config/database.php';

$id_buah = intval($_GET['id'] ?? 0);

// Ambil info buah murni yang berstatus tampil dan stok di atas 0 Kg
$query = $conn->prepare("SELECT * FROM buah WHERE id_buah = ? AND status_tampil = 'on' AND stok > 0 LIMIT 1");
$query->bind_param("i", $id_buah);
$query->execute();
$buah = $query->get_result()->fetch_assoc();
$query->close();

if (!$buah) {
    echo "<script>alert('Produk buah tidak ditemukan atau sedang tidak tersedia!'); window.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= htmlspecialchars($buah['nama_buah']); ?> - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-4xl w-full mx-auto px-4 py-12 flex-1">
        <a href="index.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-brand transition mb-6">&larr; Kembali ke Katalog</a>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
            <!-- Sisi Gambar Buah -->
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 border border-gray-100">
                <img src="../assets/uploads/images/<?= htmlspecialchars($buah['gambar']); ?>" alt="Buah" class="w-full h-full object-cover">
            </div>

            <!-- Sisi Informasi & Form Order -->
            <div class="flex flex-col justify-between space-y-6">
                <div class="space-y-2">
                    <span class="px-2.5 py-0.5 bg-green-50 text-brand text-xs font-bold rounded-md border border-green-200">Kondisi Prima (Segar)</span>
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900"><?= htmlspecialchars($buah['nama_buah']); ?></h2>
                    <div class="text-2xl font-black text-brand">Rp <?= number_format($buah['harga'], 0, ',', '.'); ?><span class="text-xs font-medium text-gray-400"> /Kg</span></div>
                    <div class="text-xs font-semibold text-gray-500 pt-1">Sisa Stok Gudang: <span class="text-gray-900 font-bold"><?= $buah['stok']; ?> Kg</span></div>
                </div>

                <div class="border-t border-gray-100 pt-4 flex-1">
                    <h4 class="text-xs font-bold uppercase text-gray-400 mb-2">Deskripsi Keterangan</h4>
                    <p class="text-sm text-gray-500 leading-relaxed font-medium"><?= nl2br(htmlspecialchars($buah['deskripsi'] ?? 'Tidak ada keterangan tambahan untuk komoditas buah segar ini.')); ?></p>
                </div>

                <!-- Input Berat Kilo Belanja -->
                <div class="border-t border-gray-100 pt-4 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-28">
                            <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Berat Pemesanan</label>
                            <div class="flex items-center border border-gray-300 rounded-xl px-3 py-2 bg-white">
                                <input type="number" id="input-qty" min="1" max="<?= $buah['stok']; ?>" value="1" 
                                    class="w-full text-sm font-bold text-gray-800 outline-none text-center">
                                <span class="text-xs font-bold text-gray-400 ml-1">Kg</span>
                            </div>
                        </div>
                        <div class="flex-1 pt-4">
                            <button onclick="addToCart(<?= $buah['id_buah']; ?>, '<?= htmlspecialchars($buah['nama_buah']); ?>', <?= $buah['harga']; ?>, <?= $buah['stok']; ?>)"
                                class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition flex justify-center items-center gap-2 shadow-lg shadow-green-600/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                <span>Masukkan Keranjang Belanja</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function addToCart(id, nama, harga, maxStok) {
            // Cek proteksi login pelanggan via PHP Session lewat variabel global
            const isLoggedIn = <?= isset($_SESSION['pelanggan_logged']) ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                alert('Gagal! Kamu harus masuk / login akun pelanggan terlebih dahulu untuk mulai memesan.');
                window.location.href = 'login.php';
                return;
            }

            const qty = parseInt(document.getElementById('input-qty').value) || 0;
            if (qty <= 0) return alert('Berat pembelian buah minimal adalah 1 Kg!');
            if (qty > maxStok) return alert('Stok gudang tidak mencukupi kebutuhan pesanan Anda!');

            // Ambil array data keranjang lama di LocalStorage browser
            let cart = JSON.parse(localStorage.getItem('cart_user')) || [];
            
            const existingItem = cart.find(item => item.id_buah === id);
            if (existingItem) {
                if ((existingItem.qty + qty) > maxStok) return alert('Total akumulasi belanjaan Anda melebihi sisa stok kami!');
                existingItem.qty += qty;
                existingItem.subtotal = existingItem.qty * harga;
            } else {
                cart.push({ id_buah: id, nama_buah: nama, harga: harga, qty: qty, subtotal: qty * harga });
            }

            localStorage.setItem('cart_user', JSON.stringify(cart));
            alert(`Sukses! ${nama} sebanyak ${qty} Kg berhasil dimasukkan ke keranjang.`);
            window.location.href = 'index.php';
        }

        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </script>
</body>
</html>