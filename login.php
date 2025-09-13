<?php
// login.php
require_once 'config/database.php';

$msg = '';
if ($_POST) {
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    if ($email === '' || $pass === '') {
        $msg = 'Email & password wajib diisi.';
    } else {
        $stmt = $db->prepare("SELECT id, nama, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nama, $hash);
            $stmt->fetch();

            // Untuk demo, password plain-text dulu. Nanti bisa pakai password_verify
            if ($pass === $hash) {
                session_start();
                $_SESSION['user_id']   = $id;
                $_SESSION['nama_user'] = $nama;
                header('Location: dashboard.php');
                exit;
            } else {
                $msg = 'Password salah.';
            }
        } else {
            $msg = 'Email tidak ditemukan.';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login – PTUN Banjarmasin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    html,body{height:100%}
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0056b3,#002147)}
    .login-wrapper{min-height:100%;display:flex;align-items:center;justify-content:center;padding:40px 15px}
    .login-card{width:100%;max-width:420px;background:#fff;border-radius:18px;box-shadow:0 8px 30px rgba(0,0,0,.2);overflow:hidden}
    .login-header{background:linear-gradient(135deg,#007bff,#0056b3);padding:2rem 1.5rem;text-align:center;color:#fff}
    .login-header img{width:90px;margin-bottom:.75rem}
    .login-header h5{font-weight:600;margin:0}
  </style>
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-header">
      <img src="assets/img/logo.png"
           onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTAiIGhlaWdodD0iOTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNDUiIGN5PSI0NSIgcj0iNDUiIGZpbGw9IiNmZmYiLz48L3N2Zz4='">
      <h5>PTUN Banjarmasin</h5>
      <small>Sistem Informasi Surat</small>
    </div>
    <div class="p-4">
      <?php if ($msg): ?>
        <script>
          Swal.fire({icon:'error',title:'Oops...',text:'<?= $msg ?>'});
        </script>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-envelope me-1"></i>Email</label>
          <input type="email" name="email" class="form-control" placeholder="admin@ptun-bjm.go.id" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="bi bi-lock me-1"></i>Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
          </button>
        </div>
        <div class="text-center mt-3">
          <a href="#" class="link-primary small">Lupa password?</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>