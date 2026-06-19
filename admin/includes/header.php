<?php
/**
 * SIDEBAR & HEADER LAYOUT COMPONENT
 * Lokasi File: admin/includes/header.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Guard: Jika tidak ada session, tendang ke login
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

$current_user = $_SESSION['username'];
$user_level   = $_SESSION['level'];

// Mendapatkan nama file aktif untuk menu highlight
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#16a34a' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col fixed h-full z-10">
            <div class="p-5 border-b border-gray-800 bg-gray-950 flex items-center gap-3">
                <div class="p-2 bg-brand text-white rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-white text-sm leading-tight">Dilla Fruit's</h1>
                    <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Panel Toko</span>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'dashboard.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                    <span>Dashboard</span>
                </a>

                <?php if ($user_level === 'admin') : ?>
                    <a href="data_buah.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'data_buah.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                        <span>Data Buah</span>
                    </a>
                    <a href="stok_buah.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'stok_buah.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <span>Stok Buah</span>
                    </a>
                    <a href="pemesanan_online.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'pemesanan_online.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span>Pemesanan Online</span>
                    </a>
                    <a href="transaksi_penjualan.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'transaksi_penjualan.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        <span>Transaksi Kasir</span>
                    </a>
                <?php endif; ?>

                <a href="laporan.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= ($active_page == 'laporan.php') ? 'text-white bg-brand' : 'hover:bg-gray-800 hover:text-white' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Laporan</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-800 bg-gray-950">
                <div class="mb-3 px-2 truncate">
                    <p class="text-sm font-semibold text-white capitalize"><?= $current_user; ?></p>
                    <p class="text-xs text-gray-500 capitalize">Role: <?= $user_level; ?></p>
                </div>
                <a href="logout.php" class="flex items-center justify-center gap-2 w-full bg-red-600/10 hover:bg-red-600 hover:text-white text-red-500 font-medium py-2.5 px-4 rounded-xl transition duration-150 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Keluar Sistem</span>
                </a>
            </div>
        </aside>

        <div class="flex-1 pl-64">
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Panel Manajemen</h2>
                </div>
                <div class="text-sm text-gray-500 font-medium bg-gray-50 px-4 py-2 rounded-xl border border-gray-200">
                    Sistem Operasional Dilla Fruit's
                </div>
            </header>
            <main class="p-8">