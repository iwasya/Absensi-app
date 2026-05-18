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

        imagedestroy($source);
        imagedestroy($target);

        if (! $encoded || ! is_string($optimized)) {
            return null;
        }

        Storage::disk('public')->put($path, $optimized);

        return $path;
    }
}
