<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Every uploaded image is decoded and re-encoded as WebP with GD. That strips metadata and any
 * payload hidden in the file, and keeps pages light. Each upload gets a large copy and a small
 * copy (`-sm.webp`) for phones. Works on shared hosting: GD only, no queue.
 */
final class ImageStore
{
    public const LARGE = 1600;

    public const SMALL = 640;

    public const MIMES = 'mimes:jpg,jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|max:5120';

    public static function store(UploadedFile $file, string $dir): string
    {
        return self::storeFromPath($file->getRealPath(), $dir);
    }

    public static function storeFromPath(string $source, string $dir): string
    {
        $data = @file_get_contents($source);
        $image = $data === false ? false : @imagecreatefromstring($data);
        if ($image === false) {
            throw new RuntimeException('Unreadable image.');
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $path = trim($dir, '/').'/'.Str::lower((string) Str::ulid()).'.webp';
        Storage::disk('public')->put($path, self::encode($image, self::LARGE));
        Storage::disk('public')->put(self::smallPath($path), self::encode($image, self::SMALL));
        imagedestroy($image);

        return $path;
    }

    public static function url(?string $path, bool $small = false): ?string
    {
        if (! $path) {
            return null;
        }
        if ($small && str_ends_with($path, '.webp')) {
            $path = self::smallPath($path);
        }

        return Storage::disk('public')->url($path);
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete([$path, self::smallPath($path)]);
        }
    }

    private static function smallPath(string $path): string
    {
        return preg_replace('/\.webp$/', '-sm.webp', $path) ?? $path;
    }

    private static function encode(\GdImage $image, int $maxWidth): string
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $target = $image;

        if ($w > $maxWidth) {
            $nh = (int) round($h * $maxWidth / $w);
            $target = imagecreatetruecolor($maxWidth, $nh);
            imagealphablending($target, false);
            imagesavealpha($target, true);
            imagecopyresampled($target, $image, 0, 0, 0, 0, $maxWidth, $nh, $w, $h);
        }

        ob_start();
        imagewebp($target, null, 80);
        $bytes = (string) ob_get_clean();

        if ($target !== $image) {
            imagedestroy($target);
        }

        return $bytes;
    }
}
