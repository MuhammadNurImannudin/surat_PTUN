<?php
$title = 'Edit Pengguna – PTUN Banjarmasin';
require '../../config/database.php';
require '../../auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM pengguna WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    header('Location: index.php');
    exit;
}

if ($_POST) {
    $stmt = $db->prepare(
        "UPDATE pengguna SET username=?, nama_user=?, role=?, status=? WHERE id=?"
    );
    $stmt->bind_param(
        'ssssi',
        $_POST['username'],
        $_POST['nama_user'],
        $_POST['role'],
        $_POST['status'],
        $id
    );
    $stmt->execute();
    echo "<script>alert('Data berhasil diperbarui');location='index.php'</script>";
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root{--primary:#0056b3;--dark:#002147;--bg:#f6f9fc;}
    body{font-family:'Inter',sans-serif;background:var(--bg);margin:0}
    .wrapper{display:flex}
    .sidebar{
      width:250px;background:linear-gradient(180deg,var(--primary),var(--dark));
      color:#fff;position:fixed;top:0;left:0;bottom:0;padding:1rem;
      display:flex;flex-direction:column;justify-content:space-between
    }
    .sidebar img{width:100px;border-radius:50%;margin-bottom:.5rem}
    .sidebar h6{font-weight:600;margin-bottom:1rem}
    .sidebar a{
      display:block;padding:.6rem 1rem;margin:.3rem 0;color:#fff;
      border-radius:10px;text-decoration:none;transition:.3s
    }
    .sidebar a:hover{background:#ffffff25}
    .sidebar .active{background:#ffffff40;font-weight:600}
    .main{margin-left:250px;padding:32px}
    .card-header{background:var(--primary);color:#fff}
  </style>
</head>
<body>
<div class="wrapper">
  <aside class="sidebar text-center">
    <div>
      <img src="/surat_PTUN/assets/img/logo.png"
           onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='"
           alt="Logo PTUN">
      <h6>PTUN Banjarmasin</h6>
      <nav class="text-start">
        <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
        <a href="../surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
        <a href="../arsip/" class="active"><i class="bi bi-archive me-2"></i>Arsip</a>
        <a href="index.php" class="active"><i class="bi bi-people me-2"></i>Pengguna</a>
        <a href="../laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
        <a href="../pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
      </nav>
    </div>
    <a href="../../logout.php" onclick="return confirm('Logout sekarang?')"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </aside>

  <main class="main">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Pengguna</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Edit Pengguna</h3>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-check me-2"></i> Form Edit Pengguna</h5>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="id" value="<?= $user['id'] ?>">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="nama_user" value="<?= htmlspecialchars($user['nama_user']) ?>" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Role</label>
              <select class="form-select" name="role" required>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <option value="Aktif" <?= $user['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Nonaktif" <?= $user['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <a href="index.php" class="btn btn-secondary me-2"><i class="bi bi-arrow-left-circle"></i> Batal</a>
            <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>