<?php
// surat_masuk/edit.php
require_once '../../config/database.php';
require_once '../../auth.php';
require_once '../../includes/session_timeout.php';
require_once '../../includes/role_check.php';
require_once '../../includes/logger.php';
include '../../includes/header.php';

if (session_status() === PHP_SESSION_NONE) session_start();
logActivity('Akses halaman Edit Surat Masuk');

$id = (int) $_GET['id'];

// Ambil data lama
$row = $db->query("SELECT * FROM surat_masuk WHERE id=$id")->fetch_assoc();

if ($_POST) {
    $no_agenda  = $_POST['no_agenda'];
    $no_surat   = $_POST['no_surat'];
    $tgl_surat  = $_POST['tgl_surat'];
    $tgl_terima = $_POST['tgl_terima'];
    $pengirim   = $_POST['pengirim'];
    $perihal    = $_POST['perihal'];
    $keterangan = $_POST['keterangan'];

    $stmt = $db->prepare("UPDATE surat_masuk 
        SET no_agenda=?, no_surat=?, tgl_surat=?, tgl_terima=?, pengirim=?, perihal=?, keterangan=? 
        WHERE id=?");
    $stmt->bind_param("sssssssi", $no_agenda, $no_surat, $tgl_surat, $tgl_terima, $pengirim, $perihal, $keterangan, $id);

    if ($stmt->execute()) {
        logActivity("Update Surat Masuk ID $id - No Surat $no_surat");
        echo "<script>alert('Data berhasil diperbarui');location='index.php'</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>

<h5>Edit Surat Masuk</h5>
<form method="post">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>No Agenda</label>
            <input type="text" name="no_agenda" class="form-control" value="<?= htmlspecialchars($row['no_agenda']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>No Surat</label>
            <input type="text" name="no_surat" class="form-control" value="<?= htmlspecialchars($row['no_surat']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tanggal Surat</label>
            <input type="date" name="tgl_surat" class="form-control" value="<?= $row['tgl_surat'] ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tanggal Terima</label>
            <input type="date" name="tgl_terima" class="form-control" value="<?= $row['tgl_terima'] ?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Pengirim</label>
            <input type="text" name="pengirim" class="form-control" value="<?= htmlspecialchars($row['pengirim']) ?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Perihal</label>
            <input type="text" name="perihal" class="form-control" value="<?= htmlspecialchars($row['perihal']) ?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"><?= htmlspecialchars($row['keterangan']) ?></textarea>
        </div>
    </div>
    <button class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include '../../includes/footer.php'; ?>
