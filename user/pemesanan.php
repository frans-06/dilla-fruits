<?php
/**
 * SHOPPING CART & CHECKOUT PAGE
 * Lokasi File: user/pemesanan.php
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['pelanggan_logged'])) {
    header('Location: login.php'); exit;
}

// Ambil info data profil instan untuk ditampilkan di form pengiriman
$id_pelanggan = $_SESSION['id_pelanggan'];
$get_user = $conn->query("SELECT nama_pelanggan, no_hp, alamat FROM pelanggan WHERE id_pelanggan = $id_pelanggan");
$user = $get_user->fetch_assoc(); ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja Saya - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-5xl w-full mx-auto px-4 py-10 flex-1 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Detail Item Belanja -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
                    <h3 class="text-lg font-bold text-gray-900">Daftar Buah dalam Keranjang</h3>
                        <button type="button" onclick="clearAllCartItems()" class="text-xs font-bold text-red-500 hover:underline">Kosongkan Keranjang</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Informasi Pengiriman & Submit Pemesanan -->
        <div class="lg:col-span-1">
            <form id="form-checkout" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6 sticky top-24">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Konfirmasi Pengiriman</h3>
                
                <input type="hidden" name="cart_json" id="payload-checkout-json">

                <div class="space-y-1 text-xs font-semibold text-gray-600">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Penerima Pesanan</p>
                    <p class="text-gray-900 font-bold text-sm"><?= htmlspecialchars($user['nama_pelanggan']); ?></p>
                    <p>Telp: <?= htmlspecialchars($user['no_hp']); ?></p>
                    <p class="text-gray-400 font-medium mt-1">Alamat Tujuan: <span class="text-gray-600 font-semibold"><?= htmlspecialchars($user['alamat']); ?></span></p>
                    <a href="profil.php" class="text-brand font-bold hover:underline block pt-1 text-[11px]">&rarr; Ganti Alamat / No HP Baru</a>
                </div>

                <hr class="border-gray-100">

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm bg-white outline-none focus:ring-2 focus:ring-brand font-bold text-gray-800">
                        <option value="transfer">🏦 Transfer Rekening Bank Mandiri</option>
                        <option value="transfer">🏦 Transfer Rekening Bank BRI</option>
                    </select>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-200">
                    <span class="text-xs font-bold text-gray-500 uppercase">Total Bayar:</span>
                    <span class="text-lg font-black text-gray-900" id="text-grand-total">Rp 0</span>
                </div>

                <button type="submit" id="btn-order" class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition uppercase tracking-wider shadow-lg shadow-green-600/10">
                    Kirim Pesanan Online
                </button>
            </form>
        </div>
    </main>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart_user')) || [];

        document.addEventListener("DOMContentLoaded", () => {
            renderCartCheckout();
        });

        function renderCartCheckout() {
            const wrapper = document.getElementById('cart-list-wrapper');
            let grandTotal = 0;

            if (cart.length === 0) {
                wrapper.innerHTML = `<div class="text-center py-12 text-gray-400 font-medium">Keranjang belanjaanmu kosong. Silakan pilih buah segar di katalog terlebih dahulu!</div>`;
                document.getElementById('btn-order').disabled = true;
                return;
            }

            wrapper.innerHTML = "";
            cart.forEach((item, index) => {
                grandTotal += item.subtotal;
                const row = document.createElement('div');
                row.className = "py-4 flex items-center justify-between gap-4";
                row.innerHTML = `
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm">${item.nama_buah}</h4>
                        <p class="text-xs text-gray-400">Rp ${formatRupiah(item.harga)} /Kg &times; <span class="font-bold text-gray-700">${item.qty} Kg</span></p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-gray-900 text-sm">Rp ${formatRupiah(item.subtotal)}</span>
                        <button type="button" onclick="removeItemCheckout(${item.id_buah})" class="text-red-500 text-sm font-bold hover:text-red-700">&times;</button>
                    </div>
                `;
                wrapper.appendChild(row);
            });

            document.getElementById('text-grand-total').innerText = "Rp " + formatRupiah(grandTotal);
            document.getElementById('payload-checkout-json').value = JSON.stringify(cart);
        }

        function removeItemCheckout(id) {
            cart = cart.filter(item => item.id_buah !== id);
            localStorage.setItem('cart_user', JSON.stringify(cart));
            renderCartCheckout();
        }

        document.getElementById('form-checkout').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (cart.length === 0) return alert('Gagal! Keranjang belanja kosong.');

            const btn = document.getElementById('btn-order');
            btn.disabled = true;
            btn.innerText = 'Mengirim Pesanan...';

            try {
                const response = await fetch('api/pemesanan_handler.php?action=checkout', {
                    method: 'POST',
                    body: new FormData(e.target)
                });
                const data = await response.json();

                if (data.success) {
                    alert('Pesanan online Anda berhasil dikirim ke antrean toko!');
                    localStorage.removeItem('cart_user'); // Bersihkan isi keranjang lokal browser
                    window.location.href = 'detail_pesanan.php';
                } else {
                    alert('Gagal memproses pesanan: ' + data.message);
                    btn.disabled = false;
                    btn.innerText = 'Kirim Pesanan Online';
                }
            } catch (err) {
                alert('Gangguan koneksi internal server.');
                btn.disabled = false;
                btn.innerText = 'Kirim Pesanan Online';
            }
        });
function formatRupiah(angka) { return new Intl.NumberFormat('id-ID').format(angka); }
    </script>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </script>
</body>
</html>