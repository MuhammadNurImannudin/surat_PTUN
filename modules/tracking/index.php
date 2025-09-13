<?php
$title = 'Tracking Surat – PTUN Banjarmasin';
require '../../config/database.php';
require '../../auth.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT no_surat, status, created_at FROM surat_keluar WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$surat = $stmt->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="id">
<head><meta charset="UTF-8"><title><?= $title ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>.timeline{position:relative;margin:20px 0}.timeline::before{content:'';position:absolute;left:20px;top:0;bottom:0;width:2px;background:#ddd}
.timeline-item{position:relative;margin:20px 0;padding-left:50px}.timeline-item::before{content:'';position:absolute;left:15px;top:5px;width:10px;height:10px;border-radius:50%;background:var(--primary)}
</style></head>
<body>
<main class="container mt-4">
  <h4>Tracking Surat <?= htmlspecialchars($surat['no_surat']) ?></h4>
  <div class="timeline">
    <div class="timeline-item">
      <strong>Surat Dibuat</strong><br><?= $surat['created_at'] ?>
    </div>
    <div class="timeline-item">
      <strong>Status Saat Ini</strong><br><?= $surat['status'] ?>
    </div>
  </div>
</main>
</body>
</html>