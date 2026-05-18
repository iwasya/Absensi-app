<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    public static function storeUploaded(UploadedFile $file, string $directory, int $maxWidth = 900, int $maxHeight = 900, int $quality = 78): ?string
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            return null;
        }

        return self::storeBinary($contents, $directory, $maxWidth, $maxHeight, $quality);
    }

    public static function storeBinary(string $contents, string $directory, int $maxWidth = 900, int $maxHeight = 900, int $quality = 78): ?string
    {
        $imageInfo = @getimagesizefromstring($contents);
        if ($imageInfo === false) {
            return null;
        }

        $source = @imagecreatefromstring($contents);
        if (! $source) {
            return null;
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $source = self::trimTransparentPadding($source);

        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight, 1);
        $targetWidth = max(1, (int) floor($originalWidth * $scale));
        $targetHeight = max(1, (int) floor($originalHeight * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
        $path = trim($directory, '/') . '/' . Str::random(24) . '.' . $extension;

        ob_start();
        $encoded = $extension === 'webp'
            ? imagewebp($target, null, $quality)
            : imagejpeg($target, null, $quality);
        $optimized = ob_get_clean();

        self::destroyImage($source);
        self::destroyImage($target);

        if (! $encoded || ! is_string($optimized)) {
            return null;
        }

        Storage::disk('public')->put($path, $optimized);

        return $path;
    }

    private static function trimTransparentPadding(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $alpha = (imagecolorat($source, $x, $y) & 0x7F000000) >> 24;

                if ($alpha < 120) {
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if ($maxX < 0) {
            return $source;
        }

        $trimmedWidth = $maxX - $minX + 1;
        $trimmedHeight = $maxY - $minY + 1;

        if ($trimmedWidth === $width && $trimmedHeight === $height) {
            return $source;
        }

        $target = imagecreatetruecolor($trimmedWidth, $trimmedHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopy($target, $source, 0, 0, $minX, $minY, $trimmedWidth, $trimmedHeight);
        self::destroyImage($source);

        return $target;
    }

    private static function destroyImage(\GdImage $image): void
    {
        if (PHP_VERSION_ID < 80000) {
            imagedestroy($image);
        }
    }
}
