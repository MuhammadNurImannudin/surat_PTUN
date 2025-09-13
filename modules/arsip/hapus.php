<?php
require_once '../../config/database.php';
require_once '../../auth.php';

$id = (int)$_GET['id'];
/* ambil nama file */
$file = $db->query("SELECT file_path FROM arsip WHERE id=$id")->fetch_assoc()['file_path'];
@unlink("../../uploads/arsip/$file");

$db->query("DELETE FROM arsip WHERE id=$id");
header('Location: index.php');