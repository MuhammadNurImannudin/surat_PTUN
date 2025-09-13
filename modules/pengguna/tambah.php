<?php
require_once '../../config/database.php';
require_once '../../auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ---------- ACTION : SIMPAN ---------- */
if ($_POST) {
    $stmt = $db->prepare("
        INSERT INTO pengguna(username, nama_user, password, role, status)
        VALUES(?,?,?,?,?)
    ");
    $stmt->bind_param(
        'sssss',
        $_POST['username'],
        $_POST['nama_user'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['role'],
        $_POST['status']
    );
    $stmt->execute();
    echo "<script>
            alert('Pengguna berhasil ditambahkan');
            location='index.php';
          </script>";
    exit;
}

$title = 'Tambah Pengguna';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#0056b3;
      --dark:#002147;
      --bg:#f6f9fc;
    }
    body{font-family:'Inter',sans-serif;background:var(--bg);}
    .wrapper{display:flex;min-height:100vh;}
    /* ---------- SIDEBAR ---------- */
    .sidebar{
      width:250px;
      background:linear-gradient(180deg,var(--primary),var(--dark));
      color:#fff;
      position:fixed;top:0;left:0;bottom:0;
      padding:1.5rem 1rem;
      display:flex;flex-direction:column;justify-content:space-between;
      box-shadow:2px 0 10px rgba(0,0,0,.15);
    }
    .sidebar img{
      width:90px;height:90px;border-radius:50%;object-fit:cover;
      background:#fff;padding:4px;margin-bottom:.75rem;
    }
    .sidebar h6{font-weight:600;margin-bottom:1.5rem}
    .sidebar nav a{
      display:flex;align-items:center;padding:.6rem 1rem;margin:.35rem 0;
      color:#fff;border-radius:8px;text-decoration:none;transition:.3s;
      font-size:15px;
    }
    .sidebar nav a:hover{background:#ffffff25}
    .sidebar nav .active{background:#ffffff40;font-weight:600}
    /* ---------- MAIN ---------- */
    .main{margin-left:250px;padding:2rem;width:100%;}
    .card{border:none;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,.08);}
    .card-header{background:var(--primary);color:#fff;border-radius:15px 15px 0 0;padding:1rem 1.25rem;}
    @media(max-width:768px){
      .sidebar{left:-250px;transition:.3s;z-index:1050}
      .sidebar.show{left:0}
      .main{margin-left:0}
    }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- ---------- SIDEBAR ---------- -->
  <aside class="sidebar text-center">
    <div>
      <img src="/surat_PTUN/assets/img/logo.png"
           alt="Logo PTUN"
           onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iOTAiIGhlaWdodD0iOTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNDUiIGN5PSI0NSIgcj0iNDUiIGZpbGw9IiMwMDdiZmYiLz48L3N2Zz4='">
      <h6>PTUN Banjarmasin</h6>
      <nav class="text-start">
        <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
        <a href="../surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
        <a href="../arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
        <a href="#" class="active"><i class="bi bi-people me-2"></i>Pengguna</a>
        <a href="../laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
        <a href="../pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
      </nav>
    </div>
    <a href="../../logout.php" class="mt-3 btn btn-outline-light w-100"
       onclick="return confirm('Logout sekarang?')">
      <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
  </aside>

  <!-- ---------- MAIN ---------- -->
  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-semibold"><i class="bi bi-person-plus me-2"></i> Tambah Pengguna</h3>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i> Form Input</h5>
      </div>
      <div class="card-body p-4">
        <form method="post">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="nama_user" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Role</label>
              <select class="form-select" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="super">Super</option>
                <option value="admin">Admin</option>
                <option value="umum">Umum</option>
                <option value="kepegawaian">Kepegawaian</option>
                <option value="hukum">Hukum</option>
                <option value="perkara">Perkara</option>
                <option value="panitera">Panitera</option>
                <option value="sekretaris">Sekretaris</option>
                <option value="keuangan">Keuangan</option>
                <option value="bendahara">Bendahara</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="Aktif">Aktif</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <a href="index.php" class="btn btn-secondary me-2">
              <i class="bi bi-arrow-left-circle"></i> Batal
            </a>
            <button class="btn btn-primary">
              <i class="bi bi-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>