<?php

namespace App\Services;

use App\Models\BrandingProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores branding logos on the private disk. Logos are never publicly
 * reachable; they are streamed by BrandingLogoController after a policy check.
 */
class LogoStorage
{
    public const DISK = 'private';

    public const MAX_KILOBYTES = 2048;

    /** Raster formats only. SVG is refused outright (it can carry scripts). */
    public const MIMES = ['image/png', 'image/jpeg', 'image/webp'];

    /**
     * Validation rules for a logo upload.
     *
     * @return list<string>
     */
    public static function rules(): array
    {
        return [
            'image', // Laravel's image rule excludes SVG unless explicitly allowed.
            'mimes:png,jpg,jpeg,webp',
            'mimetypes:'.implode(',', self::MIMES),
            'max:'.self::MAX_KILOBYTES,
            'dimensions:min_width=64,min_height=64,max_width=6000,max_height=6000',
        ];
    }

    public function store(BrandingProfile $branding, UploadedFile $file): void
    {
        $mime = (string) $file->getMimeType();
        $extension = match ($mime) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            default => throw new \InvalidArgumentException("Unsupported logo type [{$mime}]."),
        };

        $path = $file->storeAs($this->directory($branding), Str::uuid().'.'.$extension, self::DISK);

        $this->replace($branding, (string) $path, $mime);
    }

    /** Used by seeders to install a bundled placeholder file. */
    public function storeFromPath(BrandingProfile $branding, string $sourcePath): void
    {
        $mime = (string) mime_content_type($sourcePath);
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $path = $this->directory($branding).'/'.Str::uuid().'.'.$extension;

        Storage::disk(self::DISK)->put($path, (string) file_get_contents($sourcePath));

        $this->replace($branding, $path, $mime);
    }

    public function delete(BrandingProfile $branding): void
    {
        if ($branding->logo_path !== null) {
            Storage::disk(self::DISK)->delete($branding->logo_path);
        }

        $branding->forceFill(['logo_path' => null, 'logo_mime' => null])->save();
    }

    private function replace(BrandingProfile $branding, string $path, string $mime): void
    {
        $previous = $branding->logo_path;

        $branding->forceFill(['logo_path' => $path, 'logo_mime' => $mime])->save();

        if ($previous !== null && $previous !== $path) {
            Storage::disk(self::DISK)->delete($previous);
        }
    }

    private function directory(BrandingProfile $branding): string
    {
        return 'branding/'.$branding->getKey();
    }
}
