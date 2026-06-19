<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col justify-center p-4">

    <div class="bg-white w-full max-w-md mx-auto rounded-2xl shadow-xl border border-gray-200 overflow-hidden p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-gray-900">Selamat Datang Kembali</h2>
            <p class="text-xs text-gray-400 mt-1">Masuk untuk melanjutkan pesanan buah segarmu</p>
        </div>

        <div id="alert-box" class="hidden mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold"></div>

        <form id="form-login" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Email</label>
                <input type="email" name="email" required autocomplete="off" placeholder="contoh@email.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>

            <button type="submit" id="btn-submit" class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition shadow-lg shadow-green-600/10">
                Masuk Sistem
            </button>
        </form>

        <div class="flex flex-col gap-2 text-center mt-6">
            <p class="text-xs text-gray-400">Belum punya akun? <a href="register.php" class="text-brand font-bold hover:underline">Daftar di sini</a></p>
            <a href="index.php" class="text-xs text-gray-400 hover:text-gray-600 mt-2">&larr; Kembali Lihat Katalog</a>
        </div>
    </div>

    <script>
        document.getElementById('form-login').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btn-submit');
            const alertBox = document.getElementById('alert-box');

            btn.disabled = true;
            btn.innerText = 'Memverifikasi...';
            alertBox.classList.add('hidden');

            try {
                const res = await fetch('api/auth_handler.php?action=login', {
                    method: 'POST',
                    body: new FormData(form)
                });
                const data = await res.json();

                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    alertBox.innerText = data.message;
                    alertBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerText = 'Masuk Sistem';
                }
            } catch (err) {
                alertBox.innerText = 'Gagal terhubung ke server autentikasi.';
                alertBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Masuk Sistem';
            }
        });
    </script>
</body>
</html>