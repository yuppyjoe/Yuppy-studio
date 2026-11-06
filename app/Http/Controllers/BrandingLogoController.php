<?php

namespace App\Http\Controllers;

use App\Models\BrandingProfile;
use App\Services\LogoStorage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a branding logo from the private disk after a policy check.
 */
class BrandingLogoController extends Controller
{
    public function __invoke(BrandingProfile $brandingProfile): StreamedResponse
    {
        Gate::authorize('viewLogo', $brandingProfile);

        $disk = Storage::disk(LogoStorage::DISK);
        $path = $brandingProfile->logo_path;

        abort_if($path === null || ! $disk->exists($path), 404);

        return $disk->response($path, null, [
            'Content-Type' => $brandingProfile->logo_mime ?? 'application/octet-stream',
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'",
        ]);
    }
}
