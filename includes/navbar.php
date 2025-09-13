<?php
/* Pastikan session aktif */
if (session_status() === PHP_SESSION_NONE) session_start();

/* Base URL */
$base_url = '/surat_PTUN/'; // sesuaikan jika di sub-folder lain
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $base_url ?>dashboard.php">
      <img src="<?= $base_url ?>assets/img/logo-ptun.png" width="32" class="me-2" alt="PTUN">
      PTUN Banjarmasin
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active fw-bold' : '' ?>"
             href="<?= $base_url ?>dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'surat_masuk') ? 'active fw-bold' : '' ?>"
             href="<?= $base_url ?>modules/surat_masuk/">Surat Masuk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'surat_keluar') ? 'active fw-bold' : '' ?>"
             href="<?= $base_url ?>modules/surat_keluar/">Surat Keluar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], 'laporan') ? 'active fw-bold' : '' ?>"
             href="<?= $base_url ?>modules/laporan/">Laporan</a>
        </li>
      </ul>

      <!-- Safe nama user -->
      <span class="text-white me-3">
        Halo, <?= htmlspecialchars($_SESSION['user']['nama_user'] ?? $_SESSION['username'] ?? 'Admin') ?>
      </span>

      <a href="<?= $base_url ?>logout.php" class="btn btn-outline-light btn-sm"
         onclick="return confirm('Logout sekarang?')">Logout</a>
    </div>
  </div>
</nav>