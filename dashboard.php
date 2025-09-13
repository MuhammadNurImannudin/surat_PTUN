<?php
require_once 'config/database.php';

/* ---------- Helper: safe count dengan prepared statement ---------- */
function safeCount($db, $sql, $types = '', $params = [])
{
    $stmt = $db->prepare($sql);
    if (!$stmt) return 0;
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int) $count;
}

/* ---------- Ambil data statistik ---------- */
$total_masuk   = safeCount($db, "SELECT COUNT(*) FROM surat_masuk");
$total_keluar  = safeCount($db, "SELECT COUNT(*) FROM surat_keluar");
$belum_proses  = safeCount($db, "SELECT COUNT(*) FROM surat_masuk WHERE status = ?", 's', ['Belum Diproses']);
$tunggu_setuju = safeCount($db, "SELECT COUNT(*) FROM surat_keluar WHERE status = ?", 's', ['Menunggu Persetujuan']);

/* ---------- Ambil 5 data terbaru ---------- */
function getLatest($db, $sql, $types = '', $params = [])
{
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

$masukRows = getLatest($db, "
    SELECT perihal, pengirim, status, tgl_terima 
    FROM surat_masuk 
    ORDER BY id DESC 
    LIMIT 5
");

$keluarRows = getLatest($db, "
    SELECT perihal, kepada, status, tgl_kirim 
    FROM surat_keluar 
    ORDER BY id DESC 
    LIMIT 5
");

$title = 'Dashboard – PTUN Banjarmasin';

/* ---------- Data bulan untuk looping ---------- */
$bulan = [
  'Januari','Februari','Maret','April','Mei','Juni',
  'Juli','Agustus','September','Oktober','November','Desember'
];
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
    :root{
      --primary:#0056b3;--dark:#002147;
      --bg:#f6f9fc;--shadow:0 4px 20px rgba(0,0,0,.08);
      --shadow-hover:0 8px 30px rgba(0,0,0,.12);--radius:12px;
    }
    html,body{height:100%;font-family:'Inter',sans-serif;background:var(--bg)}
    .wrapper{display:flex}
    .sidebar{
      width:250px;background:linear-gradient(180deg,var(--primary),var(--dark));
      color:#fff;padding:1.5rem 1rem;position:fixed;top:0;bottom:0;left:0;
    }
    .sidebar img{width:100px;margin-bottom:10px}
    .sidebar h6{font-weight:600;margin-bottom:1rem}
    .sidebar a{
      display:block;padding:.6rem 1rem;margin:.3rem 0;
      color:#fff;border-radius:10px;text-decoration:none;transition:.3s;
    }
    .sidebar a:hover{background:#ffffff25}
    .sidebar .active{background:#ffffff40;font-weight:600}
    .main{margin-left:250px;padding:32px;flex:1}
    .stat-card{
      background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);
      padding:1.5rem;text-align:center;transition:.3s
    }
    .stat-card:hover{box-shadow:var(--shadow-hover)}
    .stat-card .icon{font-size:2.5rem;margin-bottom:.5rem}
    .stat-card .count{font-size:2rem;font-weight:700;color:var(--primary)}
    .stat-card .label{font-size:.9rem;color:#6c757d}
    .table-wrapper{background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
    .badge{padding:.4rem .8rem;border-radius:var(--radius);font-size:.75rem}
    .bulan-box{flex:1;min-width:120px}
  </style>
</head>
<body>
<div class="wrapper">
  <aside class="sidebar text-center">
    <img src="/surat_PTUN/assets/img/logo.png"
         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='"
         alt="Logo PTUN">
    <h6>PTUN Banjarmasin</h6>
    <nav class="text-start mt-3">
      <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="modules/surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
      <a href="modules/surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
      <a href="modules/arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
      <a href="modules/pengguna/"><i class="bi bi-people me-2"></i>Pengguna</a>
      <a href="modules/laporan/"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
      <a href="modules/pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
      <a href="logout.php" onclick="return confirm('Logout sekarang?')"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Dashboard Sistem Persuratan</h3>
      <?php
  $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  echo $hari[date('w')] . ', ' . date('d') . ' ' . $bulan[date('n')-1] . ' ' . date('Y');
?>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <i class="bi bi-inbox icon text-primary"></i>
          <div class="count"><?= $total_masuk ?: 0 ?></div>
          <div class="label">Total Surat Masuk</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="bi bi-send icon text-primary"></i>
          <div class="count"><?= $total_keluar ?: 0 ?></div>
          <div class="label">Total Surat Keluar</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="bi bi-hourglass-split icon text-warning"></i>
          <div class="count"><?= $belum_proses ?: 0 ?></div>
          <div class="label">Belum Diproses</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="bi bi-clock icon text-info"></i>
          <div class="count"><?= $tunggu_setuju ?: 0 ?></div>
          <div class="label">Menunggu Persetujuan</div>
        </div>
      </div>
    </div>

    <!-- Latest Tables -->
    <div class="row g-4 mb-5">
      <div class="col-md-6">
        <h5>Surat Masuk Terbaru</h5>
        <div class="table-wrapper">
          <table class="table mb-0">
            <thead class="table-light">
              <tr><th>Perihal</th><th>Dari</th><th>Status</th><th>Tgl Terima</th></tr>
            </thead>
            <tbody>
            <?php if ($masukRows): ?>
              <?php foreach ($masukRows as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['perihal'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($r['pengirim'] ?? '-') ?></td>
                  <td><span class="badge bg-secondary"><?= htmlspecialchars($r['status'] ?? '-') ?></span></td>
                  <td><?= htmlspecialchars($r['tgl_terima'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <h5>Surat Keluar Terbaru</h5>
        <div class="table-wrapper">
          <table class="table mb-0">
            <thead class="table-light">
              <tr><th>Perihal</th><th>Kepada</th><th>Status</th><th>Tgl Kirim</th></tr>
            </thead>
            <tbody>
            <?php if ($keluarRows): ?>
              <?php foreach ($keluarRows as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['perihal'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($r['kepada'] ?? '-') ?></td>
                  <td><span class="badge bg-secondary"><?= htmlspecialchars($r['status'] ?? '-') ?></span></td>
                  <td><?= htmlspecialchars($r['tgl_kirim'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Statistik Bulanan -->
    <h5>Statistik Surat Tahun <?= date('Y') ?></h5>
    <ul class="nav nav-tabs mb-3" id="statTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk" type="button" role="tab">Surat Masuk</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar" type="button" role="tab">Surat Keluar</button>
      </li>
    </ul>

    <div class="tab-content">
      <!-- Surat Masuk -->
      <div class="tab-pane fade show active" id="masuk" role="tabpanel">
        <div class="d-flex flex-wrap gap-2">
          <?php
          $totalBulan = 0;
          foreach ($bulan as $i => $nama) {
            $count = safeCount($db,
              "SELECT COUNT(*) FROM surat_masuk WHERE MONTH(tgl_terima)=? AND YEAR(tgl_terima)=?",
              'ii', [$i+1, date('Y')]
            );
            $totalBulan += $count;
            echo "<div class='stat-card bulan-box'>
                    <div class='count'>$count</div>
                    <div class='label'>$nama</div>
                  </div>";
          }
          echo "<div class='stat-card bulan-box bg-light'>
                  <div class='count'>$totalBulan</div>
                  <div class='label'>Total</div>
                </div>";
          ?>
        </div>
      </div>

      <!-- Surat Keluar -->
      <div class="tab-pane fade" id="keluar" role="tabpanel">
        <div class="d-flex flex-wrap gap-2">
          <?php
          $totalBulan = 0;
          foreach ($bulan as $i => $nama) {
            $count = safeCount($db,
              "SELECT COUNT(*) FROM surat_keluar WHERE MONTH(tgl_kirim)=? AND YEAR(tgl_kirim)=?",
              'ii', [$i+1, date('Y')]
            );
            $totalBulan += $count;
            echo "<div class='stat-card bulan-box'>
                    <div class='count'>$count</div>
                    <div class='label'>$nama</div>
                  </div>";
          }
          echo "<div class='stat-card bulan-box bg-light'>
                  <div class='count'>$totalBulan</div>
                  <div class='label'>Total</div>
                </div>";
          ?>
        </div>
      </div>
    </div>

    <!-- Kategori -->
    <h5 class="mt-5">Kategori Surat</h5>
    <div class="d-flex flex-wrap gap-2">
      <a href="modules/statistik/detail.php?kategori=Hukum" class="btn btn-outline-primary">Hukum</a>
      <a href="modules/statistik/detail.php?kategori=Perkara" class="btn btn-outline-primary">Perkara</a>
      <a href="modules/statistik/detail.php?kategori=Kepegawaian" class="btn btn-outline-primary">Kepegawaian, Organisasi dan Tata Laksana</a>
      <a href="modules/statistik/detail.php?kategori=Perencanaan" class="btn btn-outline-primary">Perencanaan, TI dan Pelaporan</a>
      <a href="modules/statistik/detail.php?kategori=Umum" class="btn btn-outline-primary">Umum dan Keuangan</a>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
