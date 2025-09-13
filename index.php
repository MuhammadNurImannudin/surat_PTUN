<?php
require_once 'config/database.php';

$msg = '';
if ($_POST) {
    $username = trim($_POST['username']);
    $pass     = trim($_POST['password']);

    if ($username === '' || $pass === '') {
        $msg = 'Username & kata sandi wajib diisi.';
    } else {
        $stmt = $db->prepare("SELECT id, nama, password FROM pengguna WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nama, $hash);
            $stmt->fetch();
            // Catatan: jika password di DB masih plaintext, perbandingan ini benar.
            // Jika sudah memakai password_hash(), ganti ke password_verify($pass, $hash).
            if ($pass === $hash) {
                session_start();
                $_SESSION['user'] = [
                    'id'       => $id,
                    'username' => $nama,
                    'role'     => 'admin'
                ];
                header('Location: dashboard.php');
                exit;
            } else {
                $msg = 'Kata sandi salah.';
            }
        } else {
            $msg = 'Username tidak ditemukan.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Masuk – PTUN Banjarmasin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    html,body{height:100%}
    body{
      background:linear-gradient(135deg,#0056b3,#007bff,#00c6ff);
      display:flex;align-items:center;justify-content:center;
      font-family:'Inter',sans-serif;margin:0;
    }
    .login-card{
      width:100%;max-width:420px;padding:2.5rem;
      background:#fff;border-radius:16px;
      box-shadow:0 10px 40px rgba(0,0,0,.25);text-align:center;
    }
    .logo-ptun{width:120px;margin-bottom:1rem}
  </style>
</head>
<body>
<div class="login-card">
  <!-- Pakai path yang sama dengan halaman lain + fallback otomatis -->
  <img id="logo-ptun" src="/surat_PTUN/assets/img/logo.png" alt="Logo PTUN" class="logo-ptun">

  <h4 class="fw-bold mb-1">PTUN Banjarmasin</h4>
  <p class="text-muted mb-4">Portal Akses Sistem Persuratan</p>

  <?php if ($msg): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3 text-start">
      <label class="form-label"><i class="bi bi-person me-1"></i>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3 text-start">
      <label class="form-label"><i class="bi bi-lock me-1"></i>Kata Sandi</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 fw-semibold">
      <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
    </button>
  </form>

  <div class="mt-3">
    <small class="text-muted"></small>
  </div>
</div>

<script>
  // Fallback logo: coba beberapa lokasi jika path utama tidak ditemukan
  (function(){
    var img = document.getElementById('logo-ptun');
    var tries = ['assets/img/logo.png','public/assets/img/logo.png'];
    var i = 0;
    img.addEventListener('error', function onErr(){
      if (i < tries.length) {
        img.src = tries[i++];
      } else {
        img.removeEventListener('error', onErr);
        img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI2MCIgY3k9IjYwIiByPSI2MCIgZmlsbD0iIzAwNTZiMyIvPjwvc3ZnPg==';
      }
    });
  })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
