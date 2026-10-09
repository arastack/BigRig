<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Serves uploaded images at a sensible size as WebP. Resized copies are written
 * once to public/img-cache and reused; on any failure the original is served.
 */
class Image
{
    public static function url(?string $path, int $width = 1200, int $quality = 78, bool $trim = false): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }

        $source = self::locate($path);
        if (! $source) {
            return Storage::url($path);
        }

        $relative = 'img-cache/' . $width . '/' . md5($path . '|' . filemtime($source) . ($trim ? '|trim' : '')) . '.webp';
        $target = public_path($relative);

        if (is_file($target)) {
            return asset($relative);
        }

        return self::resize($source, $target, $width, $quality, $trim) ? asset($relative) : self::originalUrl($path);
    }

    private static function locate(string $path): ?string
    {
        $path = ltrim($path, '/');

        foreach ([public_path('storage/' . $path), Storage::disk('public')->path($path), public_path($path)] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function originalUrl(string $path): string
    {
        return is_file(public_path(ltrim($path, '/'))) ? asset(ltrim($path, '/')) : Storage::url($path);
    }

    private static function resize(string $source, string $target, int $width, int $quality, bool $trim = false): bool
    {
        if (! function_exists('imagewebp')) {
            return false;
        }

        try {
            $info = @getimagesize($source);
            $image = match ($info[2] ?? null) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
                IMAGETYPE_PNG => @imagecreatefrompng($source),
                IMAGETYPE_WEBP => @imagecreatefromwebp($source),
                IMAGETYPE_GIF => @imagecreatefromgif($source),
                default => false,
            };

            if (! $image) {
                return false;
            }

            // WebP encoding needs a true-colour image (palette PNGs produce corrupt output)
            if (! imageistruecolor($image)) {
                imagepalettetotruecolor($image);
            }

            imagealphablending($image, false);
            imagesavealpha($image, true);

            // Remove transparent padding (used for logos)
            if ($trim && ($cropped = imagecropauto($image, IMG_CROP_TRANSPARENT))) {
                imagedestroy($image);
                $image = $cropped;
            }

            if (imagesx($image) > $width) {
                $image = imagescale($image, $width);
            }

            imagealphablending($image, false);
            imagesavealpha($image, true);

            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0755, true);
            }

            // Write to a temporary file first so a concurrent request never serves a half-written image
            $temp = $target . '.' . uniqid('', true) . '.tmp';
            $ok = imagewebp($image, $temp, $quality) && filesize($temp) > 0 && rename($temp, $target);
            imagedestroy($image);
            @unlink($temp);

            return $ok;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
