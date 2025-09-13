<?php
include '../../config/database.php';
$id = $_GET['id'];
$koneksi->query("DELETE FROM surat_keluar WHERE id=$id");
header('Location: index.php');