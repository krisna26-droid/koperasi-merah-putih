<?php

namespace App\Http\Controllers;

use App\Services\CooperativeIdentity;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CooperativeLogoController extends Controller
{
    public function __invoke(CooperativeIdentity $identity): BinaryFileResponse
    {
        $path = $identity->current()->logo_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
