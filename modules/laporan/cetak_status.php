<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/config/database.php';

use Dompdf\Dompdf;

// Ambil filter tanggal (opsional)
$awal  = isset($_GET['awal'])  ? $_GET['awal']  : date('Y-m-01');
$akhir = isset($_GET['akhir']) ? $_GET['akhir'] : date('Y-m-t');

// Hitung rekap
$rekap = [
    'masuk_belum'   => $conn->query("SELECT COUNT(*) FROM surat_masuk WHERE status='Belum Diproses' AND tgl_terima BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],
    'masuk_proses'  => $conn->query("SELECT COUNT(*) FROM surat_masuk WHERE status='Diproses'   AND tgl_terima BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],
    'masuk_selesai' => $conn->query("SELECT COUNT(*) FROM surat_masuk WHERE status='Selesai'    AND tgl_terima BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],

    'keluar_tunggu' => $conn->query("SELECT COUNT(*) FROM surat_keluar WHERE status='Menunggu' AND tgl_kirim BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],
    'keluar_kirim'  => $conn->query("SELECT COUNT(*) FROM surat_keluar WHERE status='Terkirim' AND tgl_kirim BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],
    'keluar_selesai'=> $conn->query("SELECT COUNT(*) FROM surat_keluar WHERE status='Selesai'  AND tgl_kirim BETWEEN '$awal' AND '$akhir'")->fetch_row()[0],
];

$judul = 'Rekap Status Surat Periode '.strftime('%d %B %Y', strtotime($awal)).' s.d. '.strftime('%d %B %Y', strtotime($akhir));

// HTML PDF
$html = '
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Status Surat – PTUN Banjarmasin</title>
    <style>
        body{font-family:Arial;margin:0;color:#333}
        .kop{text-align:center;margin-bottom:20px}
        .kop img{width:70px}
        .kop h4,.kop h5{margin:2px 0}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #000;padding:6px;text-align:center}
        th{background:#e9ecef}
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
        <h5>REKAP STATUS SURAT</h5>
        <p>'.$judul.'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>1</td><td>Surat Masuk</td><td>Belum Diproses</td><td>'.$rekap['masuk_belum'].'</td></tr>
            <tr><td>2</td><td>Surat Masuk</td><td>Diproses</td><td>'.$rekap['masuk_proses'].'</td></tr>
            <tr><td>3</td><td>Surat Masuk</td><td>Selesai</td><td>'.$rekap['masuk_selesai'].'</td></tr>
            <tr><td>4</td><td>Surat Keluar</td><td>Menunggu</td><td>'.$rekap['keluar_tunggu'].'</td></tr>
            <tr><td>5</td><td>Surat Keluar</td><td>Terkirim</td><td>'.$rekap['keluar_kirim'].'</td></tr>
            <tr><td>6</td><td>Surat Keluar</td><td>Selesai</td><td>'.$rekap['keluar_selesai'].'</td></tr>
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
$dompdf->stream('rekap_status_'.$awal.'_'.$akhir.'.pdf', ['Attachment' => 1]);
?>