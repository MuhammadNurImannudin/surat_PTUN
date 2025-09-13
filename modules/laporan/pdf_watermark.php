<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // DomPDF / TCPDF

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

$html = '<h2>Laporan Surat Masuk</h2><table border="1"><tr><th>No</th><th>No Surat</th><th>Perihal</th></tr>';
$res = $db->query("SELECT * FROM surat_masuk ORDER BY id DESC");
$i = 1;
while ($row = $res->fetch_assoc()) {
    $html .= "<tr><td>{$i}</td><td>{$row['no_surat']}</td><td>{$row['perihal']}</td></tr>";
    $i++;
}
$html .= '</table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$canvas = $dompdf->getCanvas();
$canvas->page_text(500, 800, "PTUN Banjarmasin", null, 8, [0, 0, 0, 0.1]);

$dompdf->stream('laporan.pdf', ['Attachment' => false]);
exit;
?>