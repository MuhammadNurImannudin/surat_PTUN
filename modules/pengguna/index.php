<?php
// Data Pengguna - index.php
require_once '../../config/database.php';
require_once '../../auth.php';

$title = 'Data Pengguna – PTUN Banjarmasin';

// Ambil data pengguna
$sql = "SELECT * FROM pengguna ORDER BY id DESC";
$users = $db->query($sql);
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
    body{font-family:'Inter',sans-serif;background:var(--bg)}
    /* Sidebar */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg,var(--primary),var(--dark));
      color:#fff;
      position: fixed;
      top:0;left:0;bottom:0;
      padding: 1rem;
    }
    .sidebar img {
      width:100px;
      margin-bottom:10px;
    }
    .sidebar h6{font-weight:600;margin-bottom:1rem}
    .sidebar a {
      display:block;
      padding:.6rem 1rem;
      margin:.3rem 0;
      color:#fff;
      border-radius:10px;
      text-decoration:none;
      transition:.3s;
    }
    .sidebar a:hover {background:#ffffff25}
    .sidebar .active {background:#ffffff40;font-weight:600}
    /* Konten */
    .main {margin-left:250px;padding:32px}
    .table-wrapper{
      background:#fff;
      border-radius:12px;
      box-shadow:0 4px 20px rgba(0,0,0,.08);
      overflow:hidden
    }
  </style>
</head>
<body>
<aside class="sidebar text-center">
  <img src="/surat_PTUN/assets/img/logo.png" 
       onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='">
  <h6>PTUN Banjarmasin</h6>
  <nav class="text-start">
    <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
    <a href="../surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
    <a href="../arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
    <a href="#" class="active"><i class="bi bi-people me-2"></i>Pengguna</a>
    <a href="../laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    <a href="../pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    <a href="../../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </nav>
</aside>

<main class="main">
  <div class="d-flex justify-content-between mb-3">
    <h3>Data Pengguna</h3>
    <a href="tambah.php" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah
    </a>
  </div>

  <div class="table-wrapper">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>NO</th>
          <th>Username</th>
          <th>Nama</th>
          <th>Role</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($users->num_rows): ?>
        <?php $no=1; while($u=$users->fetch_assoc()): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['nama']) ?></td>
            <td><?= ucfirst($u['role']) ?></td>
            <td>
              <span class="badge bg-<?= $u['aktif']?'success':'secondary' ?>">
                <?= $u['aktif']?'Aktif':'Nonaktif' ?>
              </span>
            </td>
            <td>
              <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i>
              </a>
              <a href="hapus.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Hapus pengguna ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
