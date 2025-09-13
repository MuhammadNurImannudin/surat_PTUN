<?php
require_once '../../config/database.php';
require_once '../../auth.php';

/* ambil data lama */
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM arsip WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$arsip = $stmt->get_result()->fetch_assoc();
if (!$arsip) {
    header('Location: index.php');
    exit;
}

/* proses update */
if ($_POST) {
    $no_surat   = $_POST['no_surat'];
    $tgl_surat  = $_POST['tgl_surat'];
    $perihal    = $_POST['perihal'];
    $pihak      = $_POST['pihak'];
    $jenis      = $_POST['jenis'];

    /* jika user upload file baru */
    $fileName = $arsip['file_path']; // default tetap file lama
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = '../../uploads/arsip/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        /* hapus file lama */
        @unlink($uploadDir . $arsip['file_path']);

        /* simpan file baru */
        $fileName = basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
    }

    $stmt = $db->prepare("UPDATE arsip 
                          SET no_surat=?, tgl_surat=?, perihal=?, pihak=?, jenis=?, file_path=? 
                          WHERE id=?");
    $stmt->bind_param('ssssssi', $no_surat, $tgl_surat, $perihal, $pihak, $jenis, $fileName, $id);
    $stmt->execute();

    header('Location: index.php?sukses=1');
    exit;
}

$title = 'Edit Arsip';
include '../../includes/header.php';
?>

<h4>Edit Arsip</h4>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>No Surat</label>
        <input type="text" class="form-control" name="no_surat" value="<?= htmlspecialchars($arsip['no_surat']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Tanggal Surat</label>
        <input type="date" class="form-control" name="tgl_surat" value="<?= $arsip['tgl_surat'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Perihal</label>
        <textarea class="form-control" name="perihal" rows="3" required><?= htmlspecialchars($arsip['perihal']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Pihak / Instansi</label>
        <input type="text" class="form-control" name="pihak" value="<?= htmlspecialchars($arsip['pihak']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Jenis</label>
        <select class="form-select" name="jenis">
            <option value="masuk"  <?= $arsip['jenis']=='masuk' ?'selected':'' ?>>Surat Masuk</option>
            <option value="keluar" <?= $arsip['jenis']=='keluar'?'selected':'' ?>>Surat Keluar</option>
        </select>
    </div>
    <div class="mb-3">
        <label>File Dokumen (kosongkan jika tidak diganti)</label>
        <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">File saat ini: <?= htmlspecialchars($arsip['file_path']) ?></small>
    </div>
    <button class="btn btn-primary">Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../../includes/footer.php'; ?>