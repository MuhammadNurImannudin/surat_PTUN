<?php
$title = 'Laporan – PTUN Banjarmasin';
require_once '../../config/database.php';

/* Filter periode */
$awal = $_GET['awal'] ?? date('Y-m-01');
$akhir = $_GET['akhir'] ?? date('Y-m-t');

/* Ringkasan jumlah */
$masuk_total = $keluar_total = $arsip_total = 0;

$sql = "SELECT COUNT(*) FROM surat_masuk WHERE tgl_terima BETWEEN ? AND ?";
$stmt = $db->prepare($sql); $stmt->bind_param('ss', $awal, $akhir);
$stmt->execute(); $stmt->bind_result($masuk_total); $stmt->fetch(); $stmt->close();

$sql = "SELECT COUNT(*) FROM surat_keluar WHERE tgl_kirim BETWEEN ? AND ?";
$stmt = $db->prepare($sql); $stmt->bind_param('ss', $awal, $akhir);
$stmt->execute(); $stmt->bind_result($keluar_total); $stmt->fetch(); $stmt->close();

$sql = "SELECT COUNT(*) FROM arsip WHERE tgl_arsip BETWEEN ? AND ?";
$stmt = $db->prepare($sql); $stmt->bind_param('ss', $awal, $akhir);
$stmt->execute(); $stmt->bind_result($arsip_total); $stmt->fetch(); $stmt->close();

/* Status surat (masuk & keluar) */
$status_masuk = $db->query("SELECT status, COUNT(*) AS jml 
                            FROM surat_masuk 
                            WHERE tgl_terima BETWEEN '$awal' AND '$akhir' 
                            GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$status_keluar = $db->query("SELECT status, COUNT(*) AS jml 
                             FROM surat_keluar 
                             WHERE tgl_kirim BETWEEN '$awal' AND '$akhir' 
                             GROUP BY status")->fetch_all(MYSQLI_ASSOC);

/* Export URL */
$pdf_url = "cetak_laporan.php?awal=$awal&akhir=$akhir&format=pdf";
$excel_url = "cetak_laporan.php?awal=$awal&akhir=$akhir&format=excel";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" rel="stylesheet">
  <style>
    :root{--primary:#0056b3;--dark:#002147;--bg:#f8f9fa;}
    body{font-family:'Inter',sans-serif;background:var(--bg);margin:0;height:100vh;overflow-x:hidden}
    .sidebar{width:250px;background:linear-gradient(180deg,var(--primary),var(--dark));color:#fff;position:fixed;top:0;left:0;bottom:0;display:flex;flex-direction:column;padding:1rem}
    .sidebar img{width:100px;margin:0 auto 1rem}
    .sidebar h6{font-weight:600;margin-bottom:1rem;text-align:center}
    .sidebar nav{flex:1 1 auto;margin-bottom:1rem}
    .sidebar a{display:block;padding:.6rem 1rem;margin:.3rem 0;color:#fff;border-radius:10px;text-decoration:none;transition:.3s}
    .sidebar a:hover{background:#ffffff25}
    .sidebar .active{background:#ffffff40;font-weight:600}
    .sidebar .logout{margin-top:auto;text-align:center}
    .main{margin-left:250px;padding:32px}
    .card-laporan{background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden}
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <!-- Logo & Title -->
  <div class="text-center mb-3">
    <img src="/surat_PTUN/assets/img/logo.png"
         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI1MCIgZmlsbD0iIzAwN2JmZiIvPjwvc3ZnPg=='"
         alt="Logo PTUN" width="100">
    <h6 class="mt-2 mb-0 fw-bold">PTUN Banjarmasin</h6>
  </div>

  <!-- Navigation -->
  <nav class="flex-grow-1">
    <a href="../../dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="../surat_masuk/"><i class="bi bi-inbox me-2"></i>Surat Masuk</a>
    <a href="../surat_keluar/"><i class="bi bi-send me-2"></i>Surat Keluar</a>
    <a href="../arsip/"><i class="bi bi-archive me-2"></i>Arsip</a>
    <a href="../pengguna/"><i class="bi bi-people me-2"></i>Pengguna</a>
    <a href="#" class="active"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    <a href="../pengaturan/"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    <a href="../../logout.php" onclick="return confirm('Logout sekarang?')">
      <i class="bi bi-box-arrow-right me-2"></i>Logout
    </a>
  </nav>
</aside>


<main class="main">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Laporan Surat PTUN Banjarmasin</h3>
    <span class="badge bg-secondary"><i class="bi bi-calendar"></i> <?= strftime('%B %Y', strtotime($awal)) ?> – <?= strftime('%B %Y', strtotime($akhir)) ?></span>
  </div>

  <!-- Filter periode -->
  <form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
      <label class="form-label">Tanggal Awal</label>
      <input type="date" name="awal" class="form-control" value="<?= htmlspecialchars($awal) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Tanggal Akhir</label>
      <input type="date" name="akhir" class="form-control" value="<?= htmlspecialchars($akhir) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label">&nbsp;</label>
      <button class="btn btn-primary w-100"><i class="bi bi-filter"></i> Tampilkan</button>
    </div>
  </form>

  <!-- Ringkasan -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-inbox icon fs-2 text-primary"></i>
          <h4 class="mb-1"><?= $masuk_total ?></h4>
          <div class="small">Surat Masuk</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-send icon fs-2 text-primary"></i>
          <h4 class="mb-1"><?= $keluar_total ?></h4>
          <div class="small">Surat Keluar</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-archive icon fs-2 text-primary"></i>
          <h4 class="mb-1"><?= $arsip_total ?></h4>
          <div class="small">Arsip Surat</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-printer icon fs-2 text-primary"></i>
          <a href="<?= $pdf_url ?>" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer"></i> PDF Ringkasan
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">Grafik Surat</div>
        <div class="card-body">
          <canvas id="chartSurat" height="120"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Surat -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header">Status Surat Masuk</div>
        <div class="card-body">
          <?php if ($status_masuk): ?>
            <table class="table table-sm mb-0">
              <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
              <tbody>
                <?php foreach ($status_masuk as $st): ?>
                  <tr><td><?= htmlspecialchars($st['status']) ?></td><td><?= $st['jml'] ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-muted mb-0">Tidak ada data</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header">Status Surat Keluar</div>
        <div class="card-body">
          <?php if ($status_keluar): ?>
            <table class="table table-sm mb-0">
              <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
              <tbody>
                <?php foreach ($status_keluar as $st): ?>
                  <tr><td><?= htmlspecialchars($st['status']) ?></td><td><?= $st['jml'] ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-muted mb-0">Tidak ada data</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Export -->
  <div class="row g-3">
    <div class="col-md-6">
      <a href="<?= $pdf_url ?>" target="_blank" class="btn btn-primary w-100">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
      </a>
    </div>
    <div class="col-md-6">
      <a href="<?= $excel_url ?>" target="_blank" class="btn btn-success w-100">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
      </a>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const ctx = document.getElementById('chartSurat');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Surat Masuk', 'Surat Keluar', 'Arsip'],
      datasets: [{
        label: 'Jumlah',
        data: [<?= $masuk_total ?>, <?= $keluar_total ?>, <?= $arsip_total ?>],
        backgroundColor: ['#007bff', '#28a745', '#ffc107']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } }
    }
  });
</script>
</body>
</html>