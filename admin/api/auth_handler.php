<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/credentials.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        sendJSONResponse(false, 'Username dan password wajib diisi.');
    }

    $account_found = false;
    foreach ($admin_credentials as $account) {
        if ($account['username'] === $username && $account['password'] === $password) {

            $_SESSION['admin_logged'] = true;
            $_SESSION['username']     = $account['username'];
            $_SESSION['level']        = $account['level'];
            
            $account_found = true;
            break;
        }
    }

    if ($account_found) {
        sendJSONResponse(true, 'Autentikasi berhasil, mengalihkan halaman...');
    } else {
        sendJSONResponse(false, 'Username atau password manajemen salah.');
    }

} else {
    sendJSONResponse(false, 'Metode akses tidak diizinkan.');
}