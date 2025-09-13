<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/config/database.php';

use Dompdf\Dompdf;

// Ambil bulan & tahun dari GET
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Hitung ringkasan
$masuk_total   = $conn->query("SELECT COUNT(*) FROM surat_masuk WHERE DATE_FORMAT(tgl_terima,'%Y-%m')='$bulan'")->fetch_row()[0];
$keluar_total  = $conn->query("SELECT COUNT(*) FROM surat_keluar WHERE DATE_FORMAT(tgl_kirim,'%Y-%m')='$bulan'")->fetch_row()[0];
$masuk_belum   = $conn->query("SELECT COUNT(*) FROM surat_masuk WHERE DATE_FORMAT(tgl_terima,'%Y-%m')='$bulan' AND status='Belum Diproses'")->fetch_row()[0];
$keluar_tunggu = $conn->query("SELECT COUNT(*) FROM surat_keluar WHERE DATE_FORMAT(tgl_kirim,'%Y-%m')='$bulan' AND status='Menunggu'")->fetch_row()[0];

// Judul PDF
$judul = 'Ringkasan Bulan ' . strftime('%B %Y', strtotime($bulan));

// HTML PDF
$html = '
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Bulanan - PTUN Banjarmasin</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;color:#333}
        .kop{text-align:center;margin-bottom:20px}
        .kop img{width:70px}
        .kop h4,.kop h5{margin:2px 0}
        h5{margin-top:25px}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #000;padding:6px 8px;text-align:center}
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
        <h5>RINGKASAN BULANAN</h5>
        <p>'.$judul.'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Total Surat Masuk</td>
                <td>'.$masuk_total.'</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Total Surat Keluar</td>
                <td>'.$keluar_total.'</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Surat Masuk Belum Diproses</td>
                <td>'.$masuk_belum.'</td>
            </tr>
            <tr>
                <td>4</td>
                <td>Surat Keluar Menunggu Persetujuan</td>
                <td>'.$keluar_tunggu.'</td>
            </tr>
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
$dompdf->stream('ringkasan_'.$bulan.'.pdf', ['Attachment' => 1]);
?>