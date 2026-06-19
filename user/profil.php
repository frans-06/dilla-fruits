<?php
/**
 * USER PROFILE MANAGEMENT
 * Lokasi File: user/profil.php
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guard: Jika belum login, tendang ke halaman login
if (!isset($_SESSION['pelanggan_logged'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pelanggan terbaru murni dari database berdasarkan ID Session
$id_pelanggan = $_SESSION['id_pelanggan'];
$query = $conn->prepare("SELECT nama_pelanggan, email, no_hp, alamat FROM pelanggan WHERE id_pelanggan = ? LIMIT 1");
$query->bind_param("i", $id_pelanggan);
$query->execute();
$user = $query->get_result()->fetch_assoc();
$query->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Dilla Fruit's</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: '#16a34a' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-xl w-full mx-auto px-4 py-12 flex-1">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden p-8">
            <div class="mb-6">
                <h2 class="text-xl font-black text-gray-900">Pengaturan Profil</h2>
                <p class="text-xs text-gray-400 mt-0.5">Perbarui data diri dan alamat utama tujuan pengiriman buah segar</p>
            </div>

            <div id="msg-box" class="hidden mb-4 p-3 rounded-xl text-xs font-semibold border"></div>

            <form id="form-profil" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Email (Akun)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']); ?>" disabled 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 text-sm cursor-not-allowed outline-none">
                    <p class="text-[10px] text-gray-400 mt-1">* Email digunakan sebagai ID masuk utama dan tidak dapat diganti.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($user['nama_pelanggan']); ?>" required 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand text-gray-800 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp']); ?>" required 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand text-gray-800 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Alamat Lengkap Pengiriman</label>
                    <textarea name="alamat" required rows="3" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:ring-2 focus:ring-brand text-gray-800 font-medium resize-none"><?= htmlspecialchars($user['alamat']); ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" id="btn-save" 
                        class="w-full bg-brand hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition shadow-lg shadow-green-600/10">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 text-center text-[11px] text-gray-400">
        <p>&copy; <?= date('Y'); ?> Dilla Fruit's Padang.</p>
    </footer>

    <script>
        document.getElementById('form-profil').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const btn = document.getElementById('btn-save');
            const msgBox = document.getElementById('msg-box');

            btn.disabled = true;
            btn.innerText = 'Menyimpan...';
            msgBox.className = "hidden";

            try {
                // Tembak data ke auth_handler dengan action baru (update_profile)
                const res = await fetch('api/auth_handler.php?action=update_profile', {
                    method: 'POST',
                    body: new FormData(form)
                });
                const data = await res.json();

                btn.disabled = false;
                btn.innerText = 'Simpan Perubahan';
                
                if (data.success) {
                    msgBox.className = "mb-4 p-3 rounded-xl text-xs font-semibold border bg-green-50 border-green-200 text-green-700";
                    msgBox.innerText = data.message;
                    msgBox.classList.remove('hidden');
                } else {
                    msgBox.className = "mb-4 p-3 rounded-xl text-xs font-semibold border bg-red-50 border-red-200 text-red-700";
                    msgBox.innerText = data.message;
                    msgBox.classList.remove('hidden');
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerText = 'Simpan Perubahan';
                msgBox.className = "mb-4 p-3 rounded-xl text-xs font-semibold border bg-red-50 border-red-200 text-red-700";
                msgBox.innerText = 'Terjadi gangguan sistem koneksi.';
                msgBox.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>