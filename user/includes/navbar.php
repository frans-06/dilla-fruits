<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-brand text-white rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="font-black text-xl text-gray-900 tracking-wide">Dilla Fruit's</span>
            </div>

            <div class="flex items-center gap-4 text-sm font-semibold text-gray-600">
                <a href="index.php" class="text-brand hover:text-green-700 transition">Katalog Buah</a>
                
                <?php if (isset($_SESSION['pelanggan_logged'])) : ?>
                    <a href="pemesanan.php" class="hover:text-brand transition flex items-center gap-1">
                        <span>Keranjang</span>
                        <span id="badge-cart" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                    </a>
                    <a href="detail_pesanan.php" class="hover:text-brand transition">Pesanan Saya</a>
                    <div class="h-4 w-px bg-gray-200"></div>
                    <span class="text-gray-800 font-bold">Halo, <?= htmlspecialchars($_SESSION['nama_pelanggan']); ?></span>
                    <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl transition text-xs font-bold">Keluar</a>
                <?php else : ?>
                    <div class="h-4 w-px bg-gray-200"></div>
                    <a href="login.php" class="hover:text-brand transition">Masuk</a>
                    <a href="register.php" class="bg-brand hover:bg-green-700 text-white px-4 py-2 rounded-xl transition shadow-lg shadow-green-600/15">Daftar Akun</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>