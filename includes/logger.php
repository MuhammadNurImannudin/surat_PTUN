<?php
// includes/logger.php
function logActivity($message) {
    $file = __DIR__ . '/../logs/activity.log';
    $time = date('Y-m-d H:i:s');
    $line = "[{$time}] {$message}" . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}