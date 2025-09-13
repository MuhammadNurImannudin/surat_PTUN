<?php
/**
 * Resize gambar dengan batas lebar/tinggi maksimum
 * Menyimpan ulang ke file asal (overwrite)
 * Hanya memproses JPG, PNG, GIF
 * Membutuhkan ekstensi GD
 *
 * @param string  $src       Path file gambar
 * @param int     $maxWidth  Lebar maksimum (px)
 * @param int     $maxHeight Tinggi maksimum (px)
 * @param int     $quality   Kualitas JPG (0-100)
 * @return bool   True jika sukses / tidak perlu resize
 */
function resizeImage($src, $maxWidth = 800, $maxHeight = 600, $quality = 85)
{
    // 1. Cek ekstensi GD
    if (!extension_loaded('gd')) {
        error_log('resizeImage: GD extension not loaded');
        return false;
    }

    // 2. Cek file ada & bisa dibaca
    if (!is_file($src) || !is_readable($src)) {
        error_log("resizeImage: file not found or not readable - $src");
        return false;
    }

    // 3. Ambil info gambar
    $info = @getimagesize($src);
    if (!$info) {
        error_log("resizeImage: getimagesize failed - $src");
        return false;
    }

    list($width, $height, $type) = $info;

    // 4. Hitung rasio & ukuran baru
    if ($width <= 0 || $height <= 0) {
        error_log("resizeImage: invalid dimensions - $width x $height");
        return false;
    }

    // Jika sudah lebih kecil, tidak perlu resize
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return true;
    }

    $ratio = $width / $height;

    if ($width / $height > $maxWidth / $maxHeight) {
        $newWidth  = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    } else {
        $newHeight = $maxHeight;
        $newWidth  = $maxHeight * $ratio;
    }

    $newWidth  = max(1, (int) round($newWidth));
    $newHeight = max(1, (int) round($newHeight));

    // 5. Load resource gambar
    switch ($type) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($src);
            break;
        case IMAGETYPE_GIF:
            $img = imagecreatefromgif($src);
            break;
        default:
            error_log("resizeImage: unsupported type - $type");
            return false;
    }

    if (!$img) {
        error_log("resizeImage: failed to create image resource");
        return false;
    }

    // 6. Buat canvas baru & resize
    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    // Transparansi PNG/GIF
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // 7. Simpan ulang ke file
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $src, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $src, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $src);
            break;
    }

    // 8. Bersihkan resource
    imagedestroy($thumb);
    imagedestroy($img);

    return true;
}
?>