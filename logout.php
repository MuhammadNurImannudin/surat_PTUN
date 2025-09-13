<?php
session_start(); 

// Hapus semua data session
session_unset();
session_destroy();

// (Opsional) Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect dengan alert
echo "<script>
    alert('Anda telah logout');
    window.location.href = 'index.php';
</script>";
exit;
