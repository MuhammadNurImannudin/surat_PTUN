<?php
// Base konfigurasi aplikasi PTUN
define('BASE_URL', 'http://localhost/surat_PTUN/');
define('APP_NAME', 'Sistem Persuratan PTUN Banjarmasin');
define('TIMEZONE', 'Asia/Jakarta');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXT', ['pdf', 'jpg', 'jpeg', 'png']);
define('ROLE_ADMIN', 'admin');
define('ROLE_PANITERA', 'panitera');
define('ROLE_KETUA', 'ketua');
date_default_timezone_set(TIMEZONE);
?>