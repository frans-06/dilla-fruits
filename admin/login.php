<?php
session_start();
// Jika admin/owner sudah terlanjur login, langsung lempar ke dashboard
if (isset($_SESSION['admin_logged'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Manajemen - Dilla Fruit's Padang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#16a34a', // Warna hijau utama buah segar
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden p-8 border border-gray-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex p-3 bg-green-50 rounded-full text-brand mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Dilla Fruit's Padang</h2>
            <p class="text-sm text-gray-500 mt-1">Sistem Informasi Penjualan Buah Berbasis Web</p>
        </div>

        <div id="alert-box" class="hidden mb-5 p-4 rounded-xl border bg-red-50 border-red-200 text-red-700 text-sm">
            <span id="alert-message"></span>
        </div>

        <form id="form-login" onsubmit="handleLogin(event)" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required autocomplete="off"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand transition outline-none text-gray-800" 
                    placeholder="Masukkan username manajemen...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand focus:border-brand transition outline-none text-gray-800" 
                    placeholder="••••••••">
            </div>

            <button type="submit" id="btn-submit"
                class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition duration-150 shadow-lg shadow-green-600/20 flex justify-center items-center gap-2">
                <span>Masuk ke Dashboard</span>
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="../user/index.php" class="text-sm text-gray-400 hover:text-brand transition">&larr; Kembali ke Katalog Pelanggan</a>
        </div>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            
            const form = document.getElementById('form-login');
            const btnSubmit = document.getElementById('btn-submit');
            const alertBox = document.getElementById('alert-box');
            const alertMessage = document.getElementById('alert-message');

            // Kunci tombol agar tidak terjadi spam klik saat proses validasi
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Memverifikasi...';
            alertBox.classList.add('hidden');

            try {
                // Tembak data form langsung ke file handler via Fetch API
                const response = await fetch('api/auth_handler.php', {
                    method: 'POST',
                    body: new FormData(form)
                });

                const result = await response.json();

                if (result.success) {
                    btnSubmit.innerHTML = 'Login Sukses! Mengalihkan...';
                    btnSubmit.classList.replace('bg-brand', 'bg-blue-600');
                    window.location.href = 'dashboard.php';
                } else {
                    alertMessage.innerText = result.message;
                    alertBox.classList.remove('hidden');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Masuk ke Dashboard';
                }
            } catch (error) {
                alertMessage.innerText = 'Gagal terhubung ke server autentikasi.';
                alertBox.classList.remove('hidden');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Masuk ke Dashboard';
            }
        }
    </script>
</body>
</html>