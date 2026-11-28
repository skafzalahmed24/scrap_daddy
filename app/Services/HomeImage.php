<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/** Saves / removes the images uploaded from the admin panel for the Home sections. */
class HomeImage
{
    public const DIR = 'uploads/home';
    public const RULE = 'file|mimes:jpeg,png,jpg,gif,svg|max:2048'; // JPG, PNG, SVG, GIF - 2 MB
    public const SIDE = 800;                                         // every JPG / PNG is saved as a square box this size

    /** @return string path relative to /public, e.g. uploads/home/1700000000_abc.jpg */
    public static function store(UploadedFile $file): string
    {
        $dir = public_path(self::DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = strtolower($file->extension() ?: 'jpg');
        $name = time() . '_' . Str::random(10) . '.' . $ext;
        $target = $dir . '/' . $name;

        if (!self::saveSquare($file->getRealPath(), $ext, $target)) {
            file_put_contents($target, file_get_contents($file->getRealPath()));   // GIF / SVG (or no GD): kept as uploaded
        }
        @chmod($target, 0644);

        return self::DIR . '/' . $name;
    }

    /** Centre-crops a JPG / PNG to a square box so every card has the same shape. */
    private static function saveSquare(string $src, string $ext, string $target): bool
    {
        if (!function_exists('imagecreatetruecolor') || !in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return false;
        }
        $img = @($ext === 'png' ? imagecreatefrompng($src) : imagecreatefromjpeg($src));
        if (!$img) {
            return false;
        }
        $w = imagesx($img);
        $h = imagesy($img);
        $side = min($w, $h);
        $out = imagecreatetruecolor(self::SIDE, self::SIDE);
        if ($ext === 'png') {
            imagealphablending($out, false);
            imagesavealpha($out, true);
            imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        }
        imagecopyresampled($out, $img, 0, 0, (int) (($w - $side) / 2), (int) (($h - $side) / 2), self::SIDE, self::SIDE, $side, $side);
        $ok = $ext === 'png' ? imagepng($out, $target, 6) : imagejpeg($out, $target, 85);
        imagedestroy($img);
        imagedestroy($out);

        return (bool) $ok;
    }

    public static function delete(?string $path): void
    {
        // only files this feature created
        if ($path && str_starts_with($path, self::DIR . '/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
