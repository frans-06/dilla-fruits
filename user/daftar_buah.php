<?php
/**
 * KATALOG LENGKAP & ADVANCED SEARCH PELANGGAN
 * Lokasi File: user/daftar_buah.php
 */
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Lengkap Buah Segar - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#16a34a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6 sticky top-24">
                <div>
                    <h3 class="font-black text-gray-900 text-base">Filter Produk</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Saring komoditas buah sesuai kebutuhan Anda</p>
                </div>

                <hr class="border-gray-100">

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase text-gray-400">Cari Nama Buah</label>
                    <input type="text" id="search-input" oninput="fetchKatalogAdvanced()" placeholder="Ketik nama buah..." 
                        class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand transition font-medium text-gray-700">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase text-gray-400">Harga Maksimal (/Kg)</label>
                    <input type="range" id="price-range" min="5000" max="50000" step="500" value="50000" oninput="updatePriceLabel(this.value)"
                        class="w-full accent-brand cursor-pointer">
                    <div class="flex justify-between items-center text-xs font-bold text-gray-500">
                        <span>Rp 5.000</span>
                        <span id="price-label" class="text-brand bg-green-50 px-2 py-0.5 rounded-md border border-green-200">Rp 50.000</span>
                    </div>
                </div>

                <button type="button" onclick="resetFilter()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 rounded-xl text-xs transition">
                    Reset Filter Pencarian
                </button>
            </div>
        </div>

        <div class="md:col-span-3 space-y-6">
            <div>
                <h2 class="text-xl font-black text-gray-900">Semua Pilihan Buah Segar</h2>
                <p class="text-xs text-gray-400 mt-0.5">Total produk disuplai langsung dari perkebunan terbaik lokal Padang</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6" id="advanced-katalog-wrapper">
                <div class="col-span-full text-center text-gray-400 py-12 font-medium">Memuat daftar buah lengkap...</div>
            </div>
        </div>
    </main>

    <div id="toast-notif" class="fixed bottom-6 right-6 transform translate-y-20 opacity-0 transition-all duration-300 ease-out z-50 pointer-events-none">
        <div class="bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span id="toast-msg" class="text-sm font-semibold tracking-wide">Pesan Toast Di Sini</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            fetchKatalogAdvanced();
        });

        function updatePriceLabel(value) {
            document.getElementById('price-label').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(value);
            fetchKatalogAdvanced(); 
        }

        function resetFilter() {
            document.getElementById('search-input').value = "";
            document.getElementById('price-range').value = 50000;
            document.getElementById('price-label').innerText = "Rp 50.000";
            fetchKatalogAdvanced();
        }

        async function fetchKatalogAdvanced() {
            const wrapper = document.getElementById('advanced-katalog-wrapper');
            const keyword = document.getElementById('search-input').value;
            const maxPrice = document.getElementById('price-range').value;

            try {
                const response = await fetch(`api_katalog.php?search=${encodeURIComponent(keyword)}`);
                const result = await response.json();

                wrapper.innerHTML = "";

                if (result.success && result.data.length > 0) {
                    const filteredData = result.data.filter(buah => parseFloat(buah.harga) <= parseFloat(maxPrice));

                    if (filteredData.length > 0) {
                        filteredData.forEach(buah => {
                            const card = document.createElement('div');
                            card.className = "bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col group hover:border-brand transition duration-200";
                            
                            let badgeStok = `<span class="absolute top-2 right-2 px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-md">Stok: ${buah.stok} Kg</span>`;
                            if (parseInt(buah.stok) <= 5) {
                                badgeStok = `<span class="absolute top-2 right-2 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-md">Stok Kritis</span>`;
                            }

                            card.innerHTML = `
                                <div class="relative aspect-square overflow-hidden bg-gray-50">
                                    <img src="../assets/uploads/images/${buah.gambar}" alt="${buah.nama_buah}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    ${badgeStok}
                                </div>
                                <div class="p-4 flex flex-col flex-1 space-y-1">
                                    <h4 class="font-bold text-gray-800 text-sm line-clamp-1">${buah.nama_buah}</h4>
                                    <p class="text-xs text-gray-400 line-clamp-2 flex-1">${buah.deskripsi || 'Tidak ada keterangan deskripsi.'}</p>
                                    <div class="pt-3 flex items-center justify-between">
                                        <span class="text-brand font-black text-sm">Rp ${formatRupiah(buah.harga)}<span class="text-[10px] font-medium text-gray-400">/Kg</span></span>
                                        
                                        <button type="button" onclick="beliLangsungPencegat(${buah.id_buah})" class="p-1.5 bg-gray-900 hover:bg-brand text-white rounded-lg transition" title="Beli Buah">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                        </button>
                                    </div>
                                </div>
                            `;
                            wrapper.appendChild(card);
                        });
                    } else {
                        wrapper.innerHTML = `<div class="col-span-full text-center text-gray-400 py-12">Tidak ada produk buah dengan harga di bawah batas filter Anda.</div>`;
                    }
                } else {
                    wrapper.innerHTML = `<div class="col-span-full text-center text-gray-400 py-12">Produk buah tidak ditemukan.</div>`;
                }
            } catch (error) {
                wrapper.innerHTML = `<div class="col-span-full text-center text-red-500 py-12">Gagal memuat katalog komoditas dari server.</div>`;
            }
        }

        function formatRupiah(angka) { return new Intl.NumberFormat('id-ID').format(angka); }

        // --- FUNGSI TOAST NOTIFICATION ---
        function showToast(pesan) {
            const toast = document.getElementById('toast-notif');
            document.getElementById('toast-msg').innerText = pesan;
            
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // --- FUNGSI CEGATAN LOGIN ---
        function beliLangsungPencegat(idBuah) {
            const isLoggedIn = <?= isset($_SESSION['pelanggan_logged']) ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                showToast('Akses Terbatas! Silakan login untuk mulai memesan.');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                return;
            }
            window.location.href = `detail_buah.php?id=${idBuah}`;
        }

        // --- FUNGSI UPDATE BADGE NAVBAR ---
        function updateNavbarBadge() {
            const cart = JSON.parse(localStorage.getItem('cart_user')) || [];
            const badge = document.getElementById('badge-cart');
            if (badge) {
                if (cart.length > 0) {
                    badge.innerText = cart.length;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }
        updateNavbarBadge();
    </script>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>