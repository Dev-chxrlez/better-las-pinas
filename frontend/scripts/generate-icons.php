<?php

declare(strict_types=1);

$sourcePath = __DIR__.'/../public/branding/las-pinas-seal.png';
$source = imagecreatefrompng($sourcePath);

if ($source === false) {
    throw new RuntimeException("Unable to read {$sourcePath}");
}

foreach ([1024, 192, 512] as $size) {
    $image = imagecreatetruecolor($size, $size);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $transparent);
    imagecopyresampled($image, $source, 0, 0, 0, 0, $size, $size, imagesx($source), imagesy($source));
    $destination = $size === 1024 ? $sourcePath : __DIR__."/../public/icons/icon-{$size}.png";
    imagepng($image, $destination, 9);
    imagedestroy($image);
}

imagedestroy($source);
