<?php
$title = 'Notifikasi – PTUN Banjarmasin';
require '../../config/database.php';
require '../../auth.php';
$notif = $db->query("SELECT * FROM notifications WHERE user_id = " . ($_SESSION['user']['id'] ?? 0) . " ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="id">
<head><meta charset="UTF-8"><title><?= $title ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="container mt-4">
  <h4>Notifikasi</h4>
  <?php while($n=$notif->fetch_assoc()): ?>
    <div class="alert alert-info">
      <?= htmlspecialchars($n['message']) ?> <small class="float-end"><?= $n['created_at'] ?></small>
    </div>
  <?php endwhile; ?>
</main>
</body>
</html>