<?php
/**
 * FOOTER PARTIAL LAYOUT SISI USER
 * Lokasi File: user/includes/footer.php
 */
?>
    <footer class="bg-white border-t border-gray-200 mt-auto py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-400 font-medium">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-700 text-sm tracking-wide">Dilla Fruit's Padang</span>
                <span>&copy; <?= date('Y'); ?> All Rights Reserved.</span>
            </div>
            <div class="flex gap-6">
                <a href="index.php" class="hover:text-brand transition">Katalog Utama</a>
                <a href="daftar_buah.php" class="hover:text-brand transition">Pencarian Buah</a>
                <a href="../admin/login.php" class="hover:text-gray-600 transition underline">Sistem Manajemen (Admin)</a>
            </div>
            <div>
                <p>Format Pembukuan & Transaksi Instan Real-Time (Skala Tugas Akhir)</p>
            </div>
        </div>
    </footer>
</body>
</html>