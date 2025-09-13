<?php
include '../../config/database.php';
include '../../includes/header.php';

// Ambil kode surat dari database untuk dropdown
$kodeResult = $db->query("SELECT kode, nama FROM kode_surat ORDER BY kode");

// Generate nomor otomatis
$auto = isset($_POST['auto_generate']) && $_POST['auto_generate'] === 'Ya';
$nomor_otomatis = '';
if ($auto) {
    $last = $db->query("SELECT MAX(no_urut) as last FROM surat_keluar")->fetch_assoc();
    $nomor_otomatis = str_pad(($last['last'] ?? 0) + 1, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_surat   = $_POST['tgl_surat'];
    $no_urut     = $_POST['no_urut'];
    $asal        = $_POST['asal_surat'];
    $tujuan      = $_POST['tujuan_surat'];
    $no_surat    = $_POST['no_surat'];
    $kode        = $_POST['kode_surat'];
    $keterangan  = $_POST['keterangan'];
    $perihal     = $_POST['perihal'];
    $kategori    = $_POST['kategori_surat'];

    // Upload file
    $file = null;
    if (!empty($_FILES['file_surat']['name'])) {
        $file = time() . '_' . basename($_FILES['file_surat']['name']);
        $targetPath = '../../assets/uploads/surat_keluar/' . $file;
        if (!move_uploaded_file($_FILES['file_surat']['tmp_name'], $targetPath)) {
            echo "<script>alert('Gagal upload file!');</script>";
            $file = null;
        }
    }

    $stmt = $db->prepare("INSERT INTO surat_keluar 
        (tgl_surat, no_urut, asal_surat, tujuan_surat, no_surat, kode_surat, keterangan, perihal, kategori_surat, file_surat) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssssss", 
        $tgl_surat, $no_urut, $asal, $tujuan, $no_surat, $kode, $keterangan, $perihal, $kategori, $file
    );

    if ($stmt->execute()) {
        echo "<script>alert('Surat keluar berhasil disimpan!');location='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<h5>Tambah Surat Keluar</h5>
<form method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12 mb-3">
            <label>Auto Generate Nomor Surat</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="auto_generate" value="Tidak" checked onchange="toggleAuto(this.value)">
                <label class="form-check-label">Tidak</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="auto_generate" value="Ya" onchange="toggleAuto(this.value)">
                <label class="form-check-label">Ya</label>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label>Tanggal Surat</label>
            <input type="date" name="tgl_surat" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Nomor Urut</label>
            <input type="number" name="no_urut" id="no_urut" class="form-control" value="<?= $nomor_otomatis ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Asal Surat</label>
            <input type="text" name="asal_surat" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tujuan Surat</label>
            <input type="text" name="tujuan_surat" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Nomor Surat</label>
            <input type="text" name="no_surat" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Kode Surat</label>
            <select name="kode_surat" class="form-select" required>
                <option value="">-- Pilih Kode --</option>
                <?php while ($row = $kodeResult->fetch_assoc()): ?>
                    <option value="<?= $row['kode'] ?>"><?= $row['kode'] ?> - <?= $row['nama'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label>Keterangan Surat</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label>Perihal Surat</label>
            <input type="text" name="perihal" class="form-control" required>
        </div>
        <div class="col-md-12 mb-3">
            <label>Kategori Surat</label>
            <select name="kategori_surat" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Hukum">Hukum</option>
                <option value="Perkara">Perkara</option>
                <option value="Kepegawaian">Kepegawaian, Organisasi dan Tata Laksana</option>
                <option value="Perencanaan">Perencanaan, TI dan Pelaporan</option>
                <option value="Umum">Umum dan Keuangan</option>
            </select>
        </div>
        <div class="col-md-12 mb-3">
            <label>Upload File Surat</label>
            <input type="file" name="file_surat" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
        </div>
    </div>
    <button class="btn btn-success">Tambahkan</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<script>
function toggleAuto(val) {
    const noUrut = document.getElementById('no_urut');
    if (val === 'Ya') {
        noUrut.value = '<?= $nomor_otomatis ?>';
        noUrut.readOnly = true;
    } else {
        noUrut.value = '';
        noUrut.readOnly = false;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>
