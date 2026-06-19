<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guard Keamanan
if (!isset($_SESSION['admin_logged']) || $_SESSION['level'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Akses Ditolak!']);
    exit;
}

$action = $_GET['action'] ?? '';

// TARGET FOLDER UPLOAD (Di luar folder admin agar bisa dibaca halaman user nanti)
$target_dir = __DIR__ . '/../../assets/uploads/buah/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// ============================================================
// METHOD POST: UNTUK CREATE & UPDATE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- ACTION: CREATE (Tambah Buah) ---
    if ($action === 'create') {
        $nama_buah = sanitizeInput($_POST['nama_buah']);
        $harga     = doubleval($_POST['harga']);
        $stok      = intval($_POST['stok'] ?? 0); 
        $deskripsi = sanitizeInput($_POST['deskripsi'] ?? '');

        $new_foto_name = 'default.jpg';

        if (isset($_FILES['foto_buah']) && $_FILES['foto_buah']['error'] === 0) {
            $file_ext = strtolower(pathinfo($_FILES['foto_buah']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext) && $_FILES['foto_buah']['size'] <= 2 * 1024 * 1024) {
                $new_foto_name = 'buah_' . uniqid() . '.' . $file_ext;
                move_uploaded_file($_FILES['foto_buah']['tmp_name'], $target_dir . $new_foto_name);
            }
        }

        $stmt = $conn->prepare("INSERT INTO buah (nama_buah, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiis", $nama_buah, $harga, $stok, $deskripsi, $new_foto_name);
        
        if ($stmt->execute()) {
            echo "<script>alert('Data buah berhasil ditambahkan!'); window.location.href = '../data_buah.php';</script>";
        } else {
            echo "Gagal: " . $conn->error;
        }
        $stmt->close();
    }

    // --- ACTION: UPDATE (Edit Data Buah) ---
    elseif ($action === 'update') {
        $id_buah       = intval($_POST['id_buah']);
        $nama_buah     = sanitizeInput($_POST['nama_buah']);
        $harga         = doubleval($_POST['harga']);
        $stok          = intval($_POST['stok']);
        $deskripsi     = sanitizeInput($_POST['deskripsi']);
        $status_tampil = sanitizeInput($_POST['status_tampil'] ?? 'tampilkan');

        $get_old = $conn->query("SELECT gambar FROM buah WHERE id_buah = $id_buah");
        $old_data = $get_old->fetch_assoc();
        $new_foto_name = $old_data['gambar'] ?? 'default.jpg';

        if (isset($_FILES['foto_buah']) && $_FILES['foto_buah']['error'] === 0) {
            $file_ext  = strtolower(pathinfo($_FILES['foto_buah']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_foto_name = 'buah_' . uniqid() . '.' . $file_ext;
                move_uploaded_file($_FILES['foto_buah']['tmp_name'], $target_dir . $new_foto_name);
            }
        }

        $stmt = $conn->prepare("UPDATE buah SET nama_buah = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ?, status_tampil = ? WHERE id_buah = ?");
        $stmt->bind_param("sdisssi", $nama_buah, $harga, $stok, $deskripsi, $new_foto_name, $status_tampil, $id_buah);

        if ($stmt->execute()) {
            echo "<script>alert('Data buah berhasil diperbarui!'); window.location.href = '../data_buah.php';</script>";
        } else {
            echo "Gagal: " . $conn->error;
        }
        $stmt->close();
    }
} 

// ============================================================
// METHOD GET: UNTUK DELETE & TRANSAKSI PENGURANGAN STOK VIA AJAX
// ============================================================
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');

    // --- ACTION: DELETE (Hapus Master Buah Permanen) ---
    if ($action === 'delete') {
        $id_buah = intval($_GET['id'] ?? 0);

        if ($id_buah > 0) {
            // Validasi: Cek apakah buah ini sudah pernah ada transaksi di detail_pemesanan
            $check = $conn->query("SELECT id_detail FROM detail_pemesanan WHERE id_buah = $id_buah LIMIT 1");
            if ($check && $check->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Buah tidak bisa dihapus karena sudah memiliki riwayat transaksi pelanggan!']);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM buah WHERE id_buah = ?");
            $stmt->bind_param("i", $id_buah);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Data komoditas buah berhasil dihapus permanen!']);
            } else {
                echo json_encode(['success' => false, 'message' => $conn->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'ID data tidak valid.']);
        }
        exit;
    }

    // --- ACTION: DELETE_STOK (Kurangi Jumlah Stok / Buang Buah Busuk) ---
    elseif ($action === 'delete_stok') {
        $id_buah      = intval($_GET['id'] ?? 0);
        $jumlah_buang = intval($_GET['qty'] ?? 0);

        if ($id_buah > 0 && $jumlah_buang > 0) {
            // Ambil info stok saat ini di database
            $get_stok = $conn->query("SELECT stok FROM buah WHERE id_buah = $id_buah");
            $data_stok = $get_stok->fetch_assoc();
            $stok_sekarang = $data_stok['stok'] ?? 0;

            if ($jumlah_buang > $stok_sekarang) {
                echo json_encode(['success' => false, 'message' => 'Gagal! Jumlah pengurangan melebihi sisa stok yang ada di gudang.']);
                exit;
            }

            // Eksekusi pemotongan stok secara matematis
            $stmt = $conn->prepare("UPDATE buah SET stok = stok - ? WHERE id_buah = ?");
            $stmt->bind_param("ii", $jumlah_buang, $id_buah);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Stok buah berhasil dipotong/dibuang dari gudang!']);
            } else {
                echo json_encode(['success' => false, 'message' => $conn->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Parameter data pemotongan stok tidak valid.']);
        }
        exit;
    }
} else {
    header('Location: ../data_buah.php');
    exit;
}