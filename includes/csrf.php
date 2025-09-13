<?php
function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';
}
function csrf_check($token) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403); exit('CSRF tidak valid.');
    }
}