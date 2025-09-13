<?php
function uploadFile($file, $dir = '../../uploads/', $allowed = ['pdf','jpg','jpeg','png'])
{
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return ['error' => 'Ekstensi tidak diizinkan'];
    if ($file['size'] > 5*1024*1024) return ['error' => 'Maks 5 MB'];
    $name = time() . '_' . preg_replace('/[^a-z0-9.]/i', '', $file['name']);
    if (move_uploaded_file($file['tmp_name'], $dir . $name)) {
        return ['success' => $name];
    }
    return ['error' => 'Upload gagal'];
}
?>