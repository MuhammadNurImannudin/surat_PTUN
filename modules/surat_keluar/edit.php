<?php
require_once '../../config/database.php';
require_once '../../auth.php';
require_once '../../includes/session_timeout.php';
require_once '../../includes/role_check.php';
require_once '../../includes/file_upload.php';
require_once '../../includes/resize_image.php';
require_once '../../includes/logger.php';
include '../../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logging aktivitas
logActivity('Edit surat keluar', $_SESSION['user']['id']);

// Ambil data berdasarkan ID
$id = intval($_GET['id']);
$stmt = $koneksi->prepare("SELECT * FROM surat_keluar WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_agenda   = $_POST['no_agenda'];
    $no_surat    = $_POST['no_surat'];
    $tgl_surat   = $_POST['tgl_surat'];
    $tgl_kirim   = $_POST['tgl_kirim'];
    $tujuan      = $_POST['tujuan'];
    $perihal     = $_POST['perihal'];
    $keterangan  = $_POST['keterangan'];

    $update = $koneksi->prepare("UPDATE surat_keluar 
                SET no_agenda=?, no_surat=?, tgl_surat=?, tgl_kirim=?, tujuan=?, perihal=?, keterangan=? 
                WHERE id=?");
    $update->bind_param("sssssssi", $no_agenda, $no_surat, $tgl_surat, $tgl_kirim, $tujuan, $perihal, $keterangan, $id);

    if ($update->execute()) {
        logActivity('Berhasil update surat keluar ID ' . $id, $_SESSION['user']['id']);
        echo "<script>alert('Data berhasil diperbarui');location='index.php'</script>";
    } else {
        logActivity('Gagal update surat keluar ID ' . $id, $_SESSION['user']['id']);
        echo "<script>alert('Terjadi kesalahan saat memperbarui data');</script>";
    }
}
?>

<h5 class="mb-3">Edit Surat Keluar</h5>
<form method="post">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>No Agenda</label>
            <input type="text" name="no_agenda" class="form-control" value="<?=htmlspecialchars($row['no_agenda'])?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>No Surat</label>
            <input type="text" name="no_surat" class="form-control" value="<?=htmlspecialchars($row['no_surat'])?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tanggal Surat</label>
            <input type="date" name="tgl_surat" class="form-control" value="<?=$row['tgl_surat']?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tanggal Kirim</label>
            <input type="date" name="tgl_kirim" class="form-control" value="<?=$row['tgl_kirim']?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Tujuan</label>
            <input type="text" name="tujuan" class="form-control" value="<?=htmlspecialchars($row['tujuan'])?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Perihal</label>
            <input type="text" name="perihal" class="form-control" value="<?=htmlspecialchars($row['perihal'])?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"><?=htmlspecialchars($row['keterangan'])?></textarea>
        </div>
    </div>
    <button class="btn btn-warning">Update</button>
    <a href="index.php" class="btn btn
