<?php
/**
 * PELANGGAN AUTH HANDLER (API)
 * Lokasi File: user/api/auth_handler.php
 */
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CASE 1: REGISTRASI PELANGGAN ---
    if ($action === 'register') {
        $nama    = sanitizeInput($_POST['nama_pelanggan'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $no_hp   = sanitizeInput($_POST['no_hp'] ?? '');
        $alamat  = sanitizeInput($_POST['alamat'] ?? '');
        $pass    = $_POST['password'] ?? '';

        if (empty($nama) || empty($email) || empty($no_hp) || empty($alamat) || empty($pass)) {
            echo json_encode(['success' => false, 'message' => 'Semua kolom isian wajib diisi lengkap!']);
            exit;
        }

        // Cek apakah email sudah terdaftar di database
        $check_email = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE email = ? LIMIT 1");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar! Gunakan email lain.']);
            $check_email->close();
            exit;
        }
        $check_email->close();

        // Hash password menggunakan MD5 agar klop dengan data seed tokomu
        $password_hashed = md5($pass);

        $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, email, password, alamat, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $password_hashed, $alamat, $no_hp);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Pendaftaran akun sukses!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database: ' . $conn->error]);
        }
        $stmt->close();
        exit;
    }

    // --- CASE 2: LOGIN PELANGGAN ---
    elseif ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (empty($email) || empty($pass)) {
            echo json_encode(['success' => false, 'message' => 'Email dan password tidak boleh kosong!']);
            exit;
        }

        $password_hashed = md5($pass);

        $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ? AND password = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $password_hashed);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $pelanggan = $result->fetch_assoc();

            // Set up Session Pelanggan secara mandiri
            session_regenerate_id(true); 
            $_SESSION['pelanggan_logged'] = true;
            $_SESSION['id_pelanggan']     = $pelanggan['id_pelanggan'];
            $_SESSION['nama_pelanggan']   = $pelanggan['nama_pelanggan'];
            $_SESSION['email_pelanggan']  = $pelanggan['email'];

            echo json_encode(['success' => true, 'message' => 'Login sukses!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kombinasi email atau password salah!']);
        }
        $stmt->close();
        exit;
    }

    // Masukkan potongan ini di dalam if ($_SERVER['REQUEST_METHOD'] === 'POST') pada file user/api/auth_handler.php kamu:
    elseif ($action === 'update_profile') {
        $id_pelanggan = $_SESSION['id_pelanggan'] ?? 0;
        $nama         = sanitizeInput($_POST['nama_pelanggan'] ?? '');
        $no_hp        = sanitizeInput($_POST['no_hp'] ?? '');
        $alamat       = sanitizeInput($_POST['alamat'] ?? '');

        if ($id_pelanggan === 0) {
            echo json_encode(['success' => false, 'message' => 'Sesi login kedaluwarsa!']);
            exit;
        }

        if (empty($nama) || empty($no_hp) || empty($alamat)) {
            echo json_encode(['success' => false, 'message' => 'Semua kolom isian wajib diisi lengkap!']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE pelanggan SET nama_pelanggan = ?, no_hp = ?, alamat = ? WHERE id_pelanggan = ?");
        $stmt->bind_param("sssi", $nama, $no_hp, $alamat, $id_pelanggan);

        if ($stmt->execute()) {
            // Perbarui data nama di session global agar navbar ikut berubah instan
            $_SESSION['nama_pelanggan'] = $nama;
            echo json_encode(['success' => true, 'message' => 'Data profil utama berhasil diperbarui!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengubah data: ' . $conn->error]);
        }
        $stmt->close();
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak didukung.']);
    exit;
}