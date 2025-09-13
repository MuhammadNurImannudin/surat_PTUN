<?php
// surat_masuk/tambah.php
require_once '../../config/database.php';
require_once '../../auth.php';
require_once '../../includes/session_timeout.php';
require_once '../../includes/role_check.php';
require_once '../../includes/file_upload.php';
require_once '../../includes/resize_image.php';
require_once '../../includes/logger.php';
include '../../includes/header.php';

if (session_status() === PHP_SESSION_NONE) session_start();
logActivity('Akses halaman Tambah Surat Masuk');

// Daftar kategori lengkap (kategori + perkara digabung)
$kategoriList = [
    'Kepegawaian, Organisasi dan Tata Laksana - Pengangkatan CPNS',
    'Kepegawaian, Organisasi dan Tata Laksana - Pengangkatan PNS',
    'Kepegawaian, Organisasi dan Tata Laksana - Kenaikan Pangkat',
    'Kepegawaian, Organisasi dan Tata Laksana - Mutasi',
    'Kepegawaian, Organisasi dan Tata Laksana - Pensiun',
    'Kepegawaian, Organisasi dan Tata Laksana - Disiplin Pegawai',

    'Perencanaan, TI dan Pelaporan - Laporan Keuangan',
    'Perencanaan, TI dan Pelaporan - Laporan Kinerja',
    'Perencanaan, TI dan Pelaporan - Pengadaan TI',
    'Perencanaan, TI dan Pelaporan - Pemeliharaan TI',

    'Umum dan Keuangan - Perjalanan Dinas',
    'Umum dan Keuangan - Pertanggungjawaban Keuangan',
    'Umum dan Keuangan - Pengadaan Barang/Jasa',
    'Umum dan Keuangan - Peminjaman Kendaraan'
];

// Auto-generate nomor agenda (contoh: 2025/08/285)
$bulan = date('m');
$tahun = date('Y');
$stmt  = $db->query("SELECT MAX(no_agenda) as max FROM surat_masuk WHERE YEAR(tgl_terima) = $tahun");
$last  = (int) $stmt->fetch_assoc()['max'];
$next  = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
$autoAgenda = "$tahun/$bulan/$next";

$errors = [];
if ($_POST) {
    $no_agenda   = trim($_POST['no_agenda'] ?: $autoAgenda);
    $no_surat    = trim($_POST['no_surat']);
    $tgl_surat   = $_POST['tgl_surat'];
    $tgl_terima  = $_POST['tgl_terima'];
    $pengirim    = trim($_POST['pengirim']);
    $perihal     = trim($_POST['perihal']);
    $kategori    = $_POST['kategori'];
    $keterangan  = trim($_POST['keterangan']);
    $file        = null;

    // Validasi
    if (!$no_surat)   $errors[] = "Nomor surat wajib diisi.";
    if (!$tgl_surat)  $errors[] = "Tanggal surat wajib diisi.";
    if (!$tgl_terima) $errors[] = "Tanggal terima wajib diisi.";
    if (!$pengirim)   $errors[] = "Pengirim wajib diisi.";
    if (!$perihal)    $errors[] = "Perihal wajib diisi.";
    if (!$kategori)   $errors[] = "Kategori surat wajib dipilih.";

    // Upload file
    if ($_FILES['file_surat']['name'] && empty($errors)) {
        $uploadDir  = '../../assets/uploads/surat_masuk/';
        $fileName   = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $_FILES['file_surat']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (uploadFile($_FILES['file_surat'], $uploadPath)) {
            resizeImage($uploadPath, 1200, 800);
            $file = $fileName;
        } else {
            $errors[] = "Upload file gagal.";
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO surat_masuk 
            (no_agenda, no_surat, tgl_surat, tgl_terima, pengirim, perihal, kategori, file_surat, keterangan) 
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssss", $no_agenda, $no_surat, $tgl_surat, $tgl_terima, $pengirim, $perihal, $kategori, $file, $keterangan);
        $stmt->execute();
        logActivity("Tambah Surat Masuk: No Surat $no_surat dari $pengirim");
        echo "<script>alert('Data berhasil disimpan');location='index.php'</script>";
        exit;
    }
}
?>

<div class="d-flex justify-content-between mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="../../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Surat Masuk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
        </ol>
    </nav>
    <a href="index.php" class="btn btn-secondary btn-sm">Kembali</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Form Tambah Surat Masuk</h5>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Agenda</label>
                    <input type="text" name="no_agenda" class="form-control" value="<?= $autoAgenda ?>" placeholder="Bisa diganti manual">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                    <input type="text" name="no_surat" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_surat" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_terima" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Pengirim <span class="text-danger">*</span></label>
                    <input type="text" name="pengirim" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <input type="text" name="perihal" class="form-control" required>
                </div>

                <!-- Kategori Surat -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Kategori Surat <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= $kat ?>"><?= $kat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">File Surat (PDF/JPG/PNG, max 2 MB)</label>
                    <input type="file" name="file_surat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <div id="preview" class="mt-2"></div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
        </form>
    </div>
</div>

<script>
// Preview file
document.querySelector('input[name="file_surat"]').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    if (file) {
        const valid = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!valid.includes(file.type)) {
            alert('Format file tidak didukung.');
            e.target.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2 MB.');
            e.target.value = '';
            return;
        }
        if (file.type.startsWith('image')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'img-fluid rounded';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        } else {
            preview.innerHTML = `<span class="badge bg-info">✓ ${file.name}</span>`;
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?>