<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/config/database.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil semua pengguna
$users = $conn->query("SELECT * FROM users ORDER BY id");

// Pilih mode cetak
$mode = $_GET['mode'] ?? 'pdf';

if ($mode === 'excel') {
    // ── EXCEL ──
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Pengguna PTUN');

    // Header
    $headers = ['No', 'Username', 'Nama Lengkap', 'Role'];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col.'1', $h);
        $col++;
    }

    // Data
    $row = 2;
    $no = 1;
    while ($u = $users->fetch_assoc()) {
        $sheet->setCellValue('A'.$row, $no++);
        $sheet->setCellValue('B'.$row, $u['username']);
        $sheet->setCellValue('C'.$row, $u['nama']);
        $sheet->setCellValue('D'.$row, ucfirst($u['role']));
        $row++;
    }

    // Auto-size kolom
    foreach (range('A','D') as $c) $sheet->getColumnDimension($c)->setAutoSize(true);

    // Download Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="pengguna_ptun_bjm.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// ── PDF ──
$html = '
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pengguna PTUN Banjarmasin</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;color:#333}
        .kop{text-align:center;margin-bottom:20px}
        .kop img{width:70px}
        .kop h4,.kop h5{margin:2px 0}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #000;padding:6px 8px;text-align:left}
        th{background:#e9ecef;font-weight:bold}
        .footer{margin-top:40px;text-align:right;font-size:12px}
    </style>
</head>
<body>
    <div class="kop">
        <img src="../../assets/img/logo-ptun.png" alt="Logo PTUN">
        <h4>PENGADILAN TATA USAHA NEGARA BANJARMASIN</h4>
        <h5>Jl. Pramuka No. 4, Banjarmasin – Kalimantan Selatan 70123</h5>
        <hr>
        <h5>DAFTAR PENGGUNA</h5>
        <p>Bulan: '.strftime('%B %Y').'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
while ($u = $users->fetch_assoc()) {
    $html .= '<tr>
                <td>'.$no++.'</td>
                <td>'.$u['username'].'</td>
                <td>'.$u['nama'].'</td>
                <td>'.ucfirst($u['role']).'</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        <p>Banjarmasin, '.strftime('%d %B %Y').'</p>
        <p>Admin PTUN Banjarmasin</p>
    </div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('pengguna_ptun_bjm.pdf', ['Attachment' => 1]);
?>