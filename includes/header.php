<?php
if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/path.php';
}

// === includes/header.php ===
// Pastikan tidak ada spasi/BOM sebelum tag PHP pembuka

$base_url = '/surat_ptun/'; // konsisten huruf kecil

/* (1) Aktifkan session hanya jika belum aktif */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* (2) Koneksi database */
require_once __DIR__ . '/../config/database.php';

/* (3) Cek login */
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . 'login.php');
    exit;
}

/* Helper: tandai menu aktif dengan aman */
$current = $_SERVER['REQUEST_URI'] ?? '';
$active = fn($needle) => (strpos($current, $needle) !== false) ? 'active' : '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'PTUN Banjarmasin', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= $base_url ?>assets/img/favicon.ico">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/custom.css">
    <script src="<?= $base_url ?>assets/js/app.js"></script>
    <style>
        body{background:#f6f9fc;font-family:'Inter',sans-serif}
        .navbar-brand img{width:32px;height:auto}
    </style>
</head>
<body>

<!-- 🔝 NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <!-- Logo + Nama -->
        <a class="navbar-brand d-flex align-items-center" href="<?= $base_url ?>dashboard.php">
            <img src="<?= $base_url ?>assets/img/logo.png" alt="Logo PTUN">
            <span class="ms-2 fw-semibold">PTUN Banjarmasin</span>
        </a>

        <!-- Toggler Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Navigasi -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $active('dashboard') ?>" href="<?= $base_url ?>dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('surat_masuk') ?>" href="<?= $base_url ?>modules/surat_masuk/">
                        <i class="bi bi-inbox me-1"></i> Surat Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('surat_keluar') ?>" href="<?= $base_url ?>modules/surat_keluar/">
                        <i class="bi bi-send me-1"></i> Surat Keluar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('arsip') ?>" href="<?= $base_url ?>modules/arsip/">
                        <i class="bi bi-archive me-1"></i> Arsip
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('pengguna') ?>" href="<?= $base_url ?>modules/pengguna/">
                        <i class="bi bi-people me-1"></i> Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('laporan') ?>" href="<?= $base_url ?>modules/laporan/">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active('pengaturan') ?>" href="<?= $base_url ?>modules/pengaturan/">
                        <i class="bi bi-gear me-1"></i> Pengaturan
                    </a>
                </li>
            </ul>

            <!-- User info + logout -->
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small">Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
                <a href="<?= $base_url ?>logout.php" class="btn btn-sm btn-outline-light"
                   onclick="return confirm('Yakin ingin keluar?')">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Container utama -->
<div class="container-fluid mt-4">
