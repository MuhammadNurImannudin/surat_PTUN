<?php
require '../../config/database.php';
require '../../auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("DELETE FROM pengguna WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();

echo "<script>alert('Pengguna berhasil dihapus');location='index.php'</script>";
exit;
?>