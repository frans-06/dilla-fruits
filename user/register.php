<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col justify-center p-4">

    <div class="bg-white w-full max-w-md mx-auto rounded-2xl shadow-xl border border-gray-200 overflow-hidden p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black text-gray-900">Daftar Akun Baru</h2>
            <p class="text-xs text-gray-400 mt-1">Bergabung untuk mulai memesan buah segar pilihan secara online</p>
        </div>

        <div id="alert-box" class="hidden mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold"></div>

        <form id="form-register" onsubmit="handleRegister(e => e.preventDefault())" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_pelanggan" required autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Email</label>
                <input type="email" name="email" required autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" required autocomplete="off" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Pengiriman</label>
                <textarea name="alamat" required rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Kata Sandi (Password)</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand">
            </div>

            <button type="submit" id="btn-submit" class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition shadow-lg shadow-green-600/10">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">Sudah punya akun? <a href="login.php" class="text-brand font-bold hover:underline">Masuk di sini</a></p>
    </div>

    <script>
        document.getElementById('form-register').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btn-submit');
            const alertBox = document.getElementById('alert-box');

            btn.disabled = true;
            btn.innerText = 'Memproses Pendaftaran...';
            alertBox.classList.add('hidden');

            try {
                const res = await fetch('api/auth_handler.php?action=register', {
                    method: 'POST',
                    body: new FormData(form)
                });
                const data = await res.json();

                if (data.success) {
                    alert('Registrasi Berhasil! Silakan login.');
                    window.location.href = 'login.php';
                } else {
                    alertBox.innerText = data.message;
                    alertBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerText = 'Daftar Sekarang';
                }
            } catch (err) {
                alertBox.innerText = 'Gagal terhubung ke server registrasi.';
                alertBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Daftar Sekarang';
            }
        });
    </script>
</body>
</html>