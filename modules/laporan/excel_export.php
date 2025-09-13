<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/surat_PTUN/vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/constants.php';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_surat.xlsx"');

require_once '../vendor/autoload.php'; // PHPSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Surat Masuk');

// Header
$sheet->setCellValue('A1', 'No')
      ->setCellValue('B1', 'No Surat')
      ->setCellValue('C1', 'Tgl Terima')
      ->setCellValue('D1', 'Pengirim')
      ->setCellValue('E1', 'Perihal');

// Data
$res = $db->query("SELECT * FROM surat_masuk ORDER BY id DESC");
$row = 2;
while ($data = $res->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $row - 1)
          ->setCellValue('B' . $row, $data['no_surat'])
          ->setCellValue('C' . $row, $data['tgl_terima'])
          ->setCellValue('D' . $row, $data['pengirim'])
          ->setCellValue('E' . $row, $data['perihal']);
    $row++;
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>