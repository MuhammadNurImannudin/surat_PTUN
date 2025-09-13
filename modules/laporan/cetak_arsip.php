<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require '../../config/database.php';
require '../../auth.php';

$arsip = $db->query("
    SELECT id, no_surat, tgl_surat, perihal, pihak, jenis
    FROM arsip
    ORDER BY id DESC
");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Arsip Surat</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;font-size:13px}
    table{width:100%;border-collapse:collapse;margin-top:20px}
    th,td{border:1px solid #000;padding:6px;text-align:left}
    th{background:#eee}
    h2{text-align:center;margin-top:20px}
  </style>
</head>
<body>
  <h2>Laporan Arsip Surat</h2>
  <table>
    <thead>
      <tr>
        <th>No</th><th>No Surat</th><th>Tgl Surat</th><th>Perihal</th><th>Pihak</th><th>Jenis</th>
      </tr>
    </thead>
    <tbody>
    <?php $no=1; while($r=$arsip->fetch_assoc()): ?>
      <tr>
        <td><?= $no++ ?></td><td><?= $r['no_surat'] ?></td><td><?= $r['tgl_surat'] ?></td>
        <td><?= $r['perihal'] ?></td><td><?= $r['pihak'] ?></td><td><?= $r['jenis'] ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>