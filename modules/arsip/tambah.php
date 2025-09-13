<?php
require_once '../../config/database.php';
require_once '../../auth.php';

if ($_POST) {
    $uploadDir = '../../uploads/arsip/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['file']['name']);
    $target = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $stmt = $db->prepare("INSERT INTO arsip(no_surat,tgl_surat,perihal,pihak,jenis,file_path) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param(
            'ssssss',
            $_POST['no_surat'],
            $_POST['tgl_surat'],
            $_POST['perihal'],
            $_POST['pihak'],
            $_POST['jenis'],
            $fileName
        );
        $stmt->execute();
        echo "<script>alert('Arsip berhasil ditambahkan');location='index.php'</script>";
        exit;
    } else {
        echo "<script>alert('Gagal upload file');</script>";
    }
}

$title = 'Tambah Arsip';
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-archive me-2"></i> Tambah Arsip</h5>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No Surat</label>
                        <input type="text" class="form-control" name="no_surat" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Surat</label>
                        <input type="date" class="form-control" name="tgl_surat" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Perihal</label>
                    <textarea class="form-control" name="perihal" rows="2" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pihak / Instansi</label>
                    <input type="text" class="form-control" name="pihak" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis</label>
                    <select class="form-select" name="jenis" required>
                        <option value="">-- Pilih Jenis Surat --</option>
                        <option value="masuk">Surat Masuk</option>
                        <option value="keluar">Surat Keluar</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">File (PDF / JPG / PNG)</label>
                    <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="index.php" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left-circle"></i> Batal
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
