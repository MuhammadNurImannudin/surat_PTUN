<?php
/**
 * Middleware login otomatis (hanya sekali)
 * Letakkan di root project: /surat_PTUN/auth.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Jika sudah login, langsung lewat */
if (!empty($_SESSION['user_id'])) {
    return;
}

/* Load koneksi dengan path relatif yang aman */
$databasePath = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
if (!file_exists($databasePath)) {
    die('File database.php tidak ditemukan di: ' . $databasePath);
}
require_once $databasePath;

/* Ambil user pertama sebagai default */
try {
    $stmt = $db->query("SELECT id, nama FROM users LIMIT 1");
    $user = $stmt ? $stmt->fetch_assoc() : null;

    if ($user) {
        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['nama_user'] = htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8');
    } else {
        // Fallback jika tabel kosong
        $_SESSION['user_id']   = 1;
        $_SESSION['nama_user'] = 'User Default';
    }
} catch (Exception $e) {
    die('Terjadi kesalahan saat mengambil data user: ' . $e->getMessage());
}