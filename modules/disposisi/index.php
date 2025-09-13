<?php
$title = 'Disposisi – PTUN Banjarmasin';
require '../../config/database.php';
require '../../auth.php';
require '../../includes/session_timeout.php';
require '../../includes/role_check.php';

if (!hasRole(['admin', 'ketua'])) denyAccess();

$sql = "SELECT d.id, d.keterangan, d.tanggal, u.nama_user, s.no_surat
        FROM disposisi d
        JOIN pengguna u ON d.user_id = u.id
        JOIN surat_keluar s ON d.surat_id = s.id
        ORDER BY d.tanggal DESC";
$data = $db->query($sql);
?>
<!doctype html>
<html lang="id">
<head><meta charset="UTF-8"><title><?= $title ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>:root{--primary:#0056b3;--dark:#002147;--bg:#f6f9fc}
body{font-family:'Inter',sans-serif;background:var(--bg)}
.sidebar{width:250px;background:linear-gradient(180deg,var(--primary),var(--dark));color:#fff;position:fixed;top:0;left:0;bottom:0;padding:1rem}
.sidebar img{width:100px;border-radius:50%}
.sidebar a{display:block;padding:.6rem 1rem;margin:.3rem 0;color:#fff;border-radius:10px;text-decoration:none;transition:.3s}
.sidebar a:hover{background:#ffffff25}.sidebar .active{background:#ffffff40;font-weight:600}
.main{margin-left:250px;padding:32px}
.card-header{background:var(--primary);color:#fff}</style></head>
<body>
<aside class="sidebar text-center">
  <img src="/surat_PTUN/assets/img/logo.png" alt="Logo"><h6>PTUN Banjarmasin</h6>
  <nav class="text-start">
    <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="#" class="active"><i class="bi bi-arrow-repeat me-2"></i>Disposisi</a>
  </nav>
</aside>
<main class="main">
  <h3>Daftar Disposisi</h3>
  <table class="table table-hover">
    <thead><tr><th>#</th><th>No Surat</th><th>Keterangan</th><th>Tanggal</th><th>Disposisi Oleh</th></tr></thead>
    <tbody><?php $no=1; while($r=$data->fetch_assoc()): ?>
      <tr><td><?=$no++?></td><td><?=$r['no_surat']?></td><td><?=$r['keterangan']?></td><td><?=$r['tanggal']?></td><td><?=$r['nama_user']?></td></tr>
    <?php endwhile; ?></tbody>
  </table>
</main>
</body>
</html>