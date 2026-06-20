<?php

function getImageRatio(string $sourcePath): float {
  [$width, $height] = getimagesize($sourcePath);
  return $height / $width;
}


function resizeImage(
  string  $sourcePath,
  int     $newWidth,
  int     $newHeight,
  string  $destination = null
): void {
  $ext    = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
  $isJpeg = in_array($ext, ['jpg', 'jpeg']);

  $source = $isJpeg
    ? imagecreatefromjpeg($sourcePath)
    : imagecreatefrompng($sourcePath);

  if (!$source) {
    throw new RuntimeException("Could not load image: $sourcePath");
  }

  [$srcWidth, $srcHeight] = getimagesize($sourcePath);
  $ratio = getImageRatio($sourcePath);

  if ($newHeight === 0) $newHeight = (int) round($newWidth * $ratio);
  if ($newWidth  === 0) $newWidth  = (int) round($newHeight / $ratio);

  $dest = imagecreatetruecolor($newWidth, $newHeight);

  // شفافیت PNG رو حفظ کن
  if (!$isJpeg) {
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
  }

  imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

  if ($destination === null) {
    // مستقیم به browser
    header($isJpeg ? 'Content-Type: image/jpeg' : 'Content-Type: image/png');
    $isJpeg ? imagejpeg($dest, null, 100) : imagepng($dest);
  } else {
    $isJpeg ? imagejpeg($dest, $destination, 100) : imagepng($dest, $destination);
  }

  imagedestroy($source);
  imagedestroy($dest);
}


/**
 * یه تصویر رو crop می‌کنه از مرکز
 */
function cropImageCenter(
  string $sourcePath,
  int    $newWidth,
  int    $newHeight,
  string $destination = null
): void {
  $ext    = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
  $isJpeg = in_array($ext, ['jpg', 'jpeg']);

  $source = $isJpeg
    ? imagecreatefromjpeg($sourcePath)
    : imagecreatefrompng($sourcePath);

  if (!$source) {
    throw new RuntimeException("Could not load image: $sourcePath");
  }

  [$srcWidth, $srcHeight] = getimagesize($sourcePath);

  $srcRatio  = $srcWidth / $srcHeight;
  $destRatio = $newWidth / $newHeight;

  if ($srcRatio > $destRatio) {
    $cropH = $srcHeight;
    $cropW = (int) round($srcHeight * $destRatio);
    $cropX = (int) round(($srcWidth - $cropW) / 2);
    $cropY = 0;
  } else {
    $cropW = $srcWidth;
    $cropH = (int) round($srcWidth / $destRatio);
    $cropX = 0;
    $cropY = (int) round(($srcHeight - $cropH) / 2);
  }

  $dest = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($dest, $source, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropW, $cropH);

  if ($destination === null) {
    header($isJpeg ? 'Content-Type: image/jpeg' : 'Content-Type: image/png');
    $isJpeg ? imagejpeg($dest, null, 100) : imagepng($dest);
  } else {
    $isJpeg ? imagejpeg($dest, $destination, 100) : imagepng($dest, $destination);
  }

  imagedestroy($source);
  imagedestroy($dest);
}
