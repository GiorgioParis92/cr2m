<?php 

// app/Services/RemoteFileStore.php
namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class RemoteFileStore
{

    public function fetch(string $url, array $forwardHeaders = []): string
    {
        $body = Http::withHeaders($forwardHeaders)
            ->withoutVerifying()
            ->timeout(15)
            ->accept('*/*')
            ->get($url)
            ->throw()
            ->body();

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'bin';
        $path      = 'webhooks/' . Str::uuid() . '.' . $extension;

        Storage::put($path, $body);

        return $path;
    }
}
