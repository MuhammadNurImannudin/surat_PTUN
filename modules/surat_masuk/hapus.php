<?php
include '../../config/database.php';
$id = $_GET['id'];
$koneksi->query("DELETE FROM surat_masuk WHERE id=$id");
header('Location: index.php');