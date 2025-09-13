<?php
/*
 * Halaman Pengaturan – PTUN Banjarmasin
 * Fitur: general settings, upload logo, backup/restore DB
 */

$title = 'Pengaturan – PTUN Banjarmasin';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';   // token CSRF helper
session_start();

/* ---------- Fungsi Helper ---------- */
function msg($t, $c = 'success') { echo "<div class='alert alert-$c alert-dismissible fade show'>$t<button class='btn-close' data-bs-dismiss='alert'></button></div>"; }

/* ---------- Proses POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? '');

    /* 1. Simpan general settings */
    if (isset($_POST['save_general'])) {
        $stmt = $db->prepare("REPLACE INTO settings(`key`, val) VALUES
                             ('nama_instansi',?), ('footer_text',?), ('rows_per_page',?)");
        $stmt->bind_param('ssi',
            $_POST['nama_instansi'],
            $_POST['footer_text'],
            $_POST['rows_per_page']
        );
        $stmt->execute();
        $stmt->close();
        msg('Pengaturan umum disimpan.');
    }

    /* 2. Upload logo baru */
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['logo'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['png','jpg','jpeg','gif'];
        if (in_array($ext, $allowed) && $file['size'] < 2*1024*1024) {
            $newName = 'logo_' . time() . '.' . $ext;
            $dest    = __DIR__ . '/../../assets/img/' . $newName;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $db->query("REPLACE INTO settings(`key`, val) VALUES ('logo','$newName')");
                msg('Logo berhasil diperbarui.');
            } else {
                msg('Gagal memindahkan logo. Pastikan folder writable.', 'danger');
            }
        } else {
            msg('Logo harus PNG/JPG/JPEG/GIF & max 2 MB.', 'warning');
        }
    }

    /* 3. Backup DB */
    if (isset($_POST['backup'])) {
        $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        exec("mysqldump -u{$db->real_escape_string(DB_USER)} -p{$db->real_escape_string(DB_PASS)} {$db->real_escape_string(DB_NAME)} > " . escapeshellarg(__DIR__ . "/../../backup/$backupFile"));
        msg("Backup berhasil: $backupFile");
    }

    /* 4. Restore DB (sederhana) */
    if (isset($_FILES['restore']) && $_FILES['restore']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['restore']['tmp_name'];
        exec("mysql -u{$db->real_escape_string(DB_USER)} -p{$db->real_escape_string(DB_PASS)} {$db->real_escape_string(DB_NAME)} < " . escapeshellarg($tmp));
        msg('Database berhasil direstore.', 'warning');
    }
}

/* ---------- Ambil nilai saat ini ---------- */
$settings = [];
$q = $db->query("SELECT `key`, val FROM settings");
if ($q) {
    while ($r = $q->fetch_assoc()) $settings[$r['key']] = $r['val'];
}
$logo         = $settings['logo'] ?? 'logo.png';
$nama_instansi= $settings['nama_instansi'] ?? 'PTUN Banjarmasin';
$footer_text  = $settings['footer_text'] ?? '&copy; 2025 PTUN Banjarmasin';
$rows_per_page= (int)($settings['rows_per_page'] ?? 10);
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
    .logo-preview{max-height:120px;border:1px solid #ddd;border-radius:8px;object-fit:contain}
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar text-center">
  <img src="/surat_PTUN/assets/img/<?= htmlspecialchars($logo) ?>"
       onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='">
  <h6><?= htmlspecialchars($nama_instansi) ?></h6>

  <nav class="text-start">
    <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
    <a href="../surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
    <a href="../arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
    <a href="../pengguna/"><i class="bi bi-people me-2"></i>Pengguna</a>
    <a href="../laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    <a href="#" class="active"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    <a href="../../logout.php" onclick="return confirm('Logout sekarang?')">
      <i class="bi bi-box-arrow-right me-2"></i>Logout
    </a>
  </nav>
</aside>


<main class="main">
  <h3>Pengaturan Aplikasi</h3>

  <!-- General Settings -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">Umum</div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Nama Instansi</label>
          <input type="text" name="nama_instansi" class="form-control" value="<?= htmlspecialchars($nama_instansi) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Footer / Copyright</label>
          <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($footer_text) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Data per Halaman Default</label>
          <select name="rows_per_page" class="form-select">
            <?php foreach ([5,10,25,50] as $v): ?>
              <option value="<?= $v ?>" <?= $v==$rows_per_page?'selected':'' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Logo Baru (PNG/JPG, max 2 MB)</label>
          <input type="file" name="logo" class="form-control" accept="image/*">
          <?php if ($logo): ?>
            <img src="/surat_PTUN/assets/img/<?= htmlspecialchars($logo) ?>" class="logo-preview mt-2">
          <?php endif; ?>
        </div>
        <button type="submit" name="save_general" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
      </form>
    </div>
  </div>

  <!-- Backup / Restore -->
  <div class="card shadow-sm">
    <div class="card-header">Backup & Restore Database</div>
    <div class="card-body">
      <form method="post" class="mb-3">
        <?= csrf_field() ?>
        <button type="submit" name="backup" class="btn btn-success"><i class="bi bi-download me-1"></i>Backup Sekarang</button>
      </form>

      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label class="form-label">Restore SQL</label>
        <input type="file" name="restore" class="form-control mb-2" accept=".sql">
        <button type="submit" class="btn btn-warning"><i class="bi bi-upload me-1"></i>Restore</button>
      </form>
    </div>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>