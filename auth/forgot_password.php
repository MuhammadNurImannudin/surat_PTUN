<?php
require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$title = 'Lupa Password';
?>
<!doctype html>
<html lang="id">
<head><meta charset="UTF-8"><title><?= $title ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f6f9fc;font-family:'Inter',sans-serif}</style></head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
  <div class="card shadow-lg" style="width:400px">
    <div class="card-body">
      <h5 class="card-title text-center mb-3">Lupa Password</h5>
      <form method="post">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email terdaftar" required>
        <button class="btn btn-primary w-100">Kirim Link Reset</button>
      </form>
    </div>
  </div>
</body>
</html>