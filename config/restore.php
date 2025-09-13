<?php
require_once 'constants.php';
require_once 'database.php';

if ($_FILES['restore_file']['error'] === 0) {
    $file = $_FILES['restore_file']['tmp_name'];
    $command = "mysql --user={$db->user} --password={$db->pass} {$db->db} < $file";
    exec($command, $output, $return);
    if ($return === 0) {
        echo json_encode(['success' => 'Restore berhasil']);
    } else {
        echo json_encode(['error' => 'Restore gagal']);
    }
}
?>