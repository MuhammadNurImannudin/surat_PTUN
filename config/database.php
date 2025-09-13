<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbnm = 'db_surat_ptun';   // ← nama database yang benar

$db = new mysqli($host, $user, $pass, $dbnm);

if ($db->connect_error) {
    die('Koneksi database gagal: ' . $db->connect_error);
}
?>