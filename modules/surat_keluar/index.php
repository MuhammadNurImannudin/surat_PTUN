<?php
$title = 'Surat Keluar – PTUN Banjarmasin';

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth.php';

/* ---------- Filter & Pagination ---------- */
$keyword = trim($_GET['search'] ?? '');
$show    = max(1, (int)($_GET['show'] ?? 10));
$page    = max(1, (int)($_GET['page']  ?? 1));
$offset  = ($page - 1) * $show;
$like    = "%$keyword%";

/* ---------- Hitung total ---------- */
$totalSql = "SELECT COUNT(*) 
             FROM surat_keluar
             WHERE no_agenda LIKE ? OR no_surat LIKE ? OR perihal LIKE ? OR tujuan LIKE ?";
$stmt = $db->prepare($totalSql);
$stmt->bind_param('ssss', $like, $like, $like, $like);
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();
$totalPages = max(1, ceil($totalRows / $show));

/* ---------- Ambil data ---------- */
$dataSql = "SELECT id, no_agenda, no_surat, tgl_kirim, tujuan, perihal, status
            FROM surat_keluar
            WHERE no_agenda LIKE ? OR no_surat LIKE ? OR perihal LIKE ? OR tujuan LIKE ?
            ORDER BY id DESC
            LIMIT ? OFFSET ?";
$stmt = $db->prepare($dataSql);
$stmt->bind_param('ssssii', $like, $like, $like, $like, $show, $offset);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root{--primary:#0056b3;--dark:#002147;--bg:#f6f9fc;}
    body{font-family:'Inter',sans-serif;background:var(--bg)}
    .sidebar{width:250px;background:linear-gradient(180deg,var(--primary),var(--dark));color:#fff;position:fixed;top:0;left:0;bottom:0;padding:1rem}
    .sidebar img{width:100px;margin-bottom:10px}
    .sidebar h6{font-weight:600;margin-bottom:1rem}
    .sidebar a{display:block;padding:.6rem 1rem;margin:.3rem 0;color:#fff;border-radius:10px;text-decoration:none;transition:.3s}
    .sidebar a:hover{background:#ffffff25}
    .sidebar .active{background:#ffffff40;font-weight:600}
    .main{margin-left:250px;padding:32px}
    .table-wrapper{background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden}
  </style>
</head>
<body>
<!-- SIDEBAR -->
<aside class="sidebar text-center">
  <img src="/surat_PTUN/assets/img/logo.png"
       onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='">
  <h6>PTUN Banjarmasin</h6>
  <nav class="text-start">
    <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
    <a href="index.php" class="active"><i class="bi bi-send me-2"></i>Surat Keluar</a>
    <a href="../arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
    <a href="../pengguna/"><i class="bi bi-people me-2"></i>Pengguna</a>
    <a href="../laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    <a href="../pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    <a href="../../logout.php" onclick="return confirm('Logout sekarang?')"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </nav>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="d-flex justify-content-between mb-3">
    <h3>Surat Keluar</h3>
    <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Tambah</a>
  </div>

  <!-- Filter & Search -->
  <form method="get" class="row g-2 mb-3">
    <div class="col-auto">
      <select name="show" class="form-select" onchange="this.form.submit()">
        <?php foreach ([10,25,50] as $s): ?>
          <option value="<?= $s ?>" <?= $show==$s?'selected':'' ?>><?= $s ?> entries</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <input type="text" name="search" class="form-control" placeholder="Cari surat..." value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <!-- Tabel -->
  <div class="table-wrapper">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>No</th><th>No Agenda</th><th>No Surat</th><th>Tgl Kirim</th>
          <th>Tujuan</th><th>Perihal</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows): ?>
        <?php $no = $offset + 1; while($r = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['no_agenda']) ?></td>
          <td><?= htmlspecialchars($r['no_surat']) ?></td>
          <td><?= htmlspecialchars($r['tgl_kirim']) ?></td>
          <td><?= htmlspecialchars($r['tujuan']) ?></td>
          <td><?= htmlspecialchars($r['perihal']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($r['status']) ?></span></td>
          <td>
            <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="hapus.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus data ini?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination justify-content-center">
      <?php for($p = 1; $p <= $totalPages; $p++): ?>
        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $p ?>&show=<?= $show ?>&search=<?= urlencode($keyword) ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
