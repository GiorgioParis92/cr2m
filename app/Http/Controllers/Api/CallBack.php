<?php
// app/Http/Controllers/Api/ServerCallbackController.php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CallBack
{
    /** CORS headers that accept any origin, method, and header. */
    private const CORS = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => '*',
        'Access-Control-Allow-Credentials' => 'true',
    ];

    public function __invoke(Request $request): JsonResponse
    {
        // 1. Validate --------------------------------------------------------
        $payload = $this->validateIncoming($request);

        // 2. Persist raw call for audit/debug --------------------------------
        $this->persistRaw($request, $payload);

        // 3. Work out whether a file must be downloaded ----------------------
        $downloadUrl = $this->downloadUrlFor($payload);

        if ($downloadUrl === null) {
            return $this->ok('No downloadable file for this event type.');
        }

        // 4. Download and store ----------------------------------------------
        try {
            $storedPath = $this->downloadAndStore(
                $downloadUrl,
                $request->header('X-CEERTIF-SECRET')
            );
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch the file.', JsonResponse::HTTP_BAD_GATEWAY);
        }

        // 5. Tell the sender where we stored it ------------------------------
        return $this->created(['stored_as' => $storedPath]);
    }

    // ---------------------------------------------------------------- helpers
    private function validateIncoming(Request $request): array
    {
        // If you need auth, check the header here before validating.
        return $request->validate([
            'event'   => ['required', 'string'],
            'data'    => ['required', 'array'],
            'data.processed_file_download_url'         => ['nullable', 'url'],
            'data.eidas_certificate_download_url'      => ['nullable', 'url'],
            'data.blockchain_certificate_download_url' => ['nullable', 'url'],
        ]);
    }

    private function persistRaw(Request $request, array $payload): void
    {
        DB::table('server_callbacks')->insert([
            'signature'  => $request->header('x-signature'),
            'headers'    => json_encode($request->headers->all(), JSON_THROW_ON_ERROR),
            'payload'    => json_encode($payload,               JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function downloadUrlFor(array $payload): ?string
    {
        return match ($payload['event']) {
            'WatermarkedFileAvailable'       => $payload['data']['processed_file_download_url']         ?? null,
            'EIDASCertificateAvailable'      => $payload['data']['eidas_certificate_download_url']      ?? null,
            'BlockchainCertificateAvailable' => $payload['data']['blockchain_certificate_download_url'] ?? null,
            default                          => null,
        };
    }

    private function downloadAndStore(string $url, ?string $secret): string
    {
        $body = Http::withHeaders(['X-CEERTIF-SECRET' => $secret])
            ->withoutVerifying()   // accept any TLS certificate
            ->timeout(15)
            ->accept('*/*')        // accept any MIME type
            ->get($url)
            ->throw()
            ->body();

        $ext   = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'bin';
        $name  = Str::uuid() . '.' . $ext;
        $path  = "webhooks/{$name}";

        Storage::put($path, $body);

        return $path;
    }

    // ---------------------------------------------------------------- traits
    private function ok(string|array $body): JsonResponse
    {
        return $this->json($body, JsonResponse::HTTP_OK);
    }

    private function created(array $body): JsonResponse
    {
        return $this->json($body, JsonResponse::HTTP_CREATED);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }

    private function json(array|string $body, int $status): JsonResponse
    {
        return response()->json($body, $status)->withHeaders(self::CORS);
    }
}
