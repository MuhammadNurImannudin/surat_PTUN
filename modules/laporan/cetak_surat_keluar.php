<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/config/database.php';

use Dompdf\Dompdf;

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$data  = $conn->query("SELECT * FROM surat_keluar WHERE DATE_FORMAT(tgl_kirim,'%Y-%m')='$bulan' ORDER BY tgl_kirim");

$html = '
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Surat Keluar - PTUN Banjarmasin</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;color:#333}
        .kop{text-align:center;margin-bottom:20px}
        .kop img{width:70px}
        .kop h4,.kop h5{margin:2px 0}
        table{width:100%;border-collapse:collapse;margin-top:10px;font-size:12px}
        th,td{border:1px solid #000;padding:6px 4px}
        th{background:#e9ecef;font-weight:bold}
        .footer{margin-top:40px;text-align:right;font-size:12px}
    </style>
</head>
<body onload="window.print()">
<div class="container mt-4">
    <div class="kop">
        <img src="../../assets/img/logo-ptun.png" alt="Logo PTUN">
        <h4>PENGADILAN TATA USAHA NEGARA BANJARMASIN</h4>
        <h5>Jl. Pramuka No. 4, Banjarmasin – Kalimantan Selatan 70123</h5>
        <hr>
        <h5>LAPORAN SURAT KELUAR</h5>
        <p>Bulan: '.strftime('%B %Y', strtotime($bulan.'-01')).'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Agenda</th>
                <th>No Surat</th>
                <th>Tgl Surat</th>
                <th>Tgl Kirim</th>
                <th>Tujuan</th>
                <th>Perihal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
$no = 1;
while ($r = $data->fetch_assoc()) {
    $html .= '
            <tr>
                <td>'.$no++.'</td>
                <td>'.$r['no_agenda'].'</td>
                <td>'.$r['no_surat'].'</td>
                <td>'.date('d/m/Y', strtotime($r['tgl_surat'])).'</td>
                <td>'.date('d/m/Y', strtotime($r['tgl_kirim'])).'</td>
                <td>'.$r['tujuan'].'</td>
                <td>'.$r['perihal'].'</td>
                <td>'.($r['status'] ?? 'Menunggu').'</td>
            </tr>';
}
$html .= '
        </tbody>
    </table>

    <div class="footer">
        <p>Banjarmasin, '.strftime('%d %B %Y').'</p>
        <p>Admin PTUN Banjarmasin</p>
    </div>
</div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('surat_keluar_'.$bulan.'.pdf', ['Attachment' => 1]);
?>