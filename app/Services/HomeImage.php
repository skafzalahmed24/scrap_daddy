<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/** Saves / removes the images uploaded from the admin panel for the Home sections. */
class HomeImage
{
    public const DIR = 'uploads/home';
    public const RULE = 'file|mimes:jpeg,png,jpg,gif,svg|max:2048'; // JPG, PNG, SVG, GIF - 2 MB

    /** @return string path relative to /public, e.g. uploads/home/1700000000_abc.jpg */
    public static function store(UploadedFile $file): string
    {
        $dir = public_path(self::DIR);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = time() . '_' . Str::random(10) . '.' . ($file->extension() ?: 'jpg');
        file_put_contents($dir . '/' . $name, file_get_contents($file->getRealPath()));
        @chmod($dir . '/' . $name, 0644);

        return self::DIR . '/' . $name;
    }

    public static function delete(?string $path): void
    {
        // only files this feature created
        if ($path && str_starts_with($path, self::DIR . '/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
