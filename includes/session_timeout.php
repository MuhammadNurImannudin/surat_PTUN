<?php
// Auto-logout setelah idle 15 menit
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 900; // 15 menit
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
?>