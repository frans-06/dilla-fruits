<?php
/**
 * REVISED TRANSACTION KASIR - DILLA FRUIT'S
 * Lokasi File: admin/transaksi_penjualan.php
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

if ($user_level !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='dashboard.php';</script>";
    exit;
}

$query_buah = "SELECT id_buah, nama_buah, harga, stok FROM buah WHERE stok > 0 ORDER BY nama_buah ASC";
$result_buah = $conn->query($query_buah);

$nota_auto = 'TRX-' . date('YmdHis');
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Input Item Penjualan</h3>
            <p class="text-xs text-gray-400 mb-6">Pilih komoditas buah dan tentukan berat timbangan pelanggan</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Pilih Buah</label>
                    <select id="kasir-id-buah" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 outline-none text-sm bg-white focus:ring-2 focus:ring-brand">
                        <option value="">-- Pilih Produk --</option>
                        <?php while($row = $result_buah->fetch_assoc()): ?>
                            <option value="<?= $row['id_buah']; ?>" data-nama="<?= htmlspecialchars($row['nama_buah']); ?>" data-harga="<?= $row['harga']; ?>" data-stok="<?= $row['stok']; ?>">
                                <?= htmlspecialchars($row['nama_buah']); ?> (Sisa: <?= $row['stok']; ?> Kg)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Berat / Jumlah (Kg)</label>
                    <input type="number" id="kasir-qty" min="1" value="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 outline-none text-sm focus:ring-2 focus:ring-brand font-semibold text-gray-800">
                </div>
                <div>
                    <button type="button" onclick="addItemToCart()" class="w-full bg-brand hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        <span>Tambahkan</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse" id="table-cart">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-4">Nama Buah</th>
                        <th class="px-6 py-4">Harga /Kg</th>
                        <th class="px-6 py-4">Berat (Qty)</th>
                        <th class="px-6 py-4">Subtotal</th>
                        <th class="px-6 py-4 text-center">Batal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700" id="cart-items-wrapper">
                    <tr id="cart-empty-row">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">Keranjang kasir masih kosong.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-1">
        <form action="api/transaksi_handler.php?action=create" method="POST" onsubmit="return validateCheckout()" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-6 sticky top-24">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Ringkasan Pembayaran</h3>
            
            <input type="hidden" name="no_nota" value="<?= $nota_auto; ?>">
            <input type="hidden" name="cart_json" id="payload-cart-json">

            <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-400 font-medium">
                    <span>No. Nota:</span>
                    <span class="text-gray-700 font-bold"><?= $nota_auto; ?></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 font-medium pt-2">
                    <span>Grand Total:</span>
                    <span class="text-xl font-black text-gray-900" id="text-grand-total">Rp 0</span>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase text-brand">Uang Tunai Bayar (Rp)</label>
                <input type="number" name="uang_bayar" id="kasir-bayar" oninput="calculateChange()" required min="0" class="w-full px-4 py-3 bg-green-50/50 rounded-xl border border-green-200 outline-none text-lg font-black text-green-700 focus:ring-2 focus:ring-brand tracking-wide" placeholder="0">
            </div>

            <div class="space-y-1">
                <span class="text-xs font-medium text-gray-400">Uang Kembalian:</span>
                <div class="text-lg font-bold text-gray-800" id="text-kembalian">Rp 0</div>
            </div>

            <button type="submit" class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition shadow-xl shadow-green-600/20 tracking-wider uppercase">
                Simpan & Cetak Nota
            </button>
        </form>
    </div>
</div>

<script>
let cart = [];
let grandTotal = 0;

function addItemToCart() {
    const select = document.getElementById('kasir-id-buah');
    const qtyInput = document.getElementById('kasir-qty');
    
    if (!select.value) return alert('Silakan pilih buah terlebih dahulu!');
    
    const idBuah = parseInt(select.value);
    const selectedOption = select.options[select.selectedIndex];
    const namaBuah = selectedOption.getAttribute('data-nama');
    const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
    const maxStok = parseInt(selectedOption.getAttribute('data-stok'));
    const qty = parseInt(qtyInput.value);

    if (qty <= 0) return alert('Berat minimum adalah 1 Kg!');
    if (qty > maxStok) return alert('Stok tidak mencukupi! Sisa stok hanya ' + maxStok + ' Kg.');

    const existingItem = cart.find(item => item.id_buah === idBuah);
    if (existingItem) {
        if ((existingItem.qty + qty) > maxStok) {
            return alert('Total belanjaan melebihi batas stok gudang!');
        }
        existingItem.qty += qty;
        existingItem.subtotal = existingItem.qty * existingItem.harga;
    } else {
        cart.push({
            id_buah: idBuah,
            nama_buah: namaBuah,
            harga: harga,
            qty: qty,
            subtotal: qty * harga
        });
    }

    renderCart();
    qtyInput.value = 1;
    select.value = "";
}

function removeItemFromCart(idBuah) {
    cart = cart.filter(item => item.id_buah !== idBuah);
    renderCart();
}

function renderCart() {
    const wrapper = document.getElementById('cart-items-wrapper');
    const emptyRow = document.getElementById('cart-empty-row');
    
    wrapper.innerHTML = "";
    
    if (cart.length === 0) {
        wrapper.appendChild(emptyRow);
        grandTotal = 0;
        document.getElementById('text-grand-total').innerText = "Rp 0";
        calculateChange();
        return;
    }

    grandTotal = 0;
    cart.forEach(item => {
        grandTotal += item.subtotal;
        
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50/50 transition";
        tr.innerHTML = `
            <td class="px-6 py-4 font-semibold text-gray-800">${item.nama_buah}</td>
            <td class="px-6 py-4 text-gray-500">Rp ${formatRupiah(item.harga)}</td>
            <td class="px-6 py-4 font-bold text-gray-900">${item.qty} Kg</td>
            <td class="px-6 py-4 font-semibold text-gray-900">Rp ${formatRupiah(item.subtotal)}</td>
            <td class="px-6 py-4 text-center">
                <button type="button" onclick="removeItemFromCart(${item.id_buah})" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
            </td>
        `;
        wrapper.appendChild(tr);
    });

    document.getElementById('text-grand-total').innerText = "Rp " + formatRupiah(grandTotal);
    document.getElementById('payload-cart-json').value = JSON.stringify(cart);
    calculateChange();
}

function calculateChange() {
    const bayar = parseInt(document.getElementById('kasir-bayar').value) || 0;
    const kembalian = bayar - grandTotal;
    
    const kembalianText = document.getElementById('text-kembalian');
    if (kembalian < 0) {
        kembalianText.innerText = "Uang Kurang: Rp " + formatRupiah(Math.abs(kembalian));
        kembalianText.className = "text-lg font-bold text-red-500";
    } else {
        kembalianText.innerText = "Rp " + formatRupiah(kembalian);
        kembalianText.className = "text-lg font-bold text-gray-800";
    }
}

function validateCheckout() {
    if (cart.length === 0) {
        alert('Keranjang kasir kosong! Masukkan item belanjaan dahulu.');
        return false;
    }
    const bayar = parseInt(document.getElementById('kasir-bayar').value) || 0;
    if (bayar < grandTotal) {
        alert('Transaksi tidak dapat disimpan! Uang tunai bayar kurang.');
        return false;
    }
    return true;
}

function formatRupiah(angka) { return new Intl.NumberFormat('id-ID').format(angka); }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>