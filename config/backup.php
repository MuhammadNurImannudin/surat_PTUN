<?php
require_once 'constants.php';
require_once 'database.php';

$backupDir = __DIR__ . '/../backup/';
if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

$filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$command = "mysqldump --user={$db->user} --password={$db->pass} {$db->db} > $backupDir$filename";
exec($command, $output, $return);
if ($return === 0) {
    echo json_encode(['success' => 'Backup berhasil']);
} else {
    echo json_encode(['error' => 'Backup gagal']);
}
?>