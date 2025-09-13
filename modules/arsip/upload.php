<?php
require_once '../../config/database.php';
require_once '../../auth.php';
require_once '../../includes/session_timeout.php';
require_once '../../includes/role_check.php';
require_once '../../includes/file_upload.php';
require_once '../../includes/resize_image.php';
require_once '../../includes/logger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user login
if (!isset($_SESSION['user'])) {
    logActivity('Akses upload arsip tanpa login');
    die(json_encode(['error' => 'Unauthorized']));
}

// Validasi hak akses
if (!hasRole(['admin', 'panitera'])) {
    logActivity('Akses upload arsip ditolak role', $_SESSION['user']['id']);
    denyAccess('Anda tidak punya akses upload.');
}

// Konfigurasi
$uploadDir = '../../uploads/arsip/';
$allowed   = ['pdf', 'jpg', 'jpeg', 'png'];
$maxSize   = 5 * 1024 * 1024; // 5 MB

// Cek POST & file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    // Upload
    $upload = uploadFile($_FILES['file'], $uploadDir, $allowed);
    if (isset($upload['error'])) {
        logActivity('Upload arsip gagal: ' . $upload['error'], $_SESSION['user']['id']);
        die(json_encode(['error' => $upload['error']]));
    }

    $fileName = $upload['success'];

    // Resize jika gambar
    $fullPath = $uploadDir . $fileName;
    if (in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
        resizeImage($fullPath);
    }

    // Simpan DB
    $stmt = $db->prepare(
        "INSERT INTO arsip(no_surat, tgl_surat, perihal, pihak, jenis, file_path, uploaded_by)
         VALUES(?,?,?,?,?,?,?)"
    );
    $stmt->bind_param(
        'ssssssi',
        $_POST['no_surat'],
        $_POST['tgl_surat'],
        $_POST['perihal'],
        $_POST['pihak'],
        $_POST['jenis'],
        $fileName,
        $_SESSION['user']['id']
    );
    $stmt->execute();

    logActivity("Upload arsip berhasil: {$fileName}", $_SESSION['user']['id']);
    echo json_encode(['success' => 'File berhasil di-upload']);
    exit;
}

// Tampilan Form
$title = 'Upload Arsip';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h4>Upload File Arsip</h4>
  <form action="" method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label>No Surat</label>
      <input type="text" name="no_surat" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Tgl Surat</label>
      <input type="date" name="tgl_surat" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Perihal</label>
      <input type="text" name="perihal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Pihak</label>
      <input type="text" name="pihak" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Jenis</label>
      <select class="form-select" name="jenis" required>
        <option value="masuk">Masuk</option>
        <option value="keluar">Keluar</option>
      </select>
    </div>
    <div class="mb-3">
      <label>File (PDF/JPG/PNG ≤ 5 MB)</label>
      <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
    </div>
    <button class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
  </form>
</div>
</body>
</html>