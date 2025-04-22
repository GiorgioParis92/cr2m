<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CallBack
{
    /** CORS headers that allow every origin, header and method. */
    private const CORS_HEADERS = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => '*',
        'Access-Control-Allow-Credentials' => 'true',
    ];

    public function __invoke(Request $request): JsonResponse
    {
        // 1) Validate and extract just what we need ---------------------------
        $payload = $this->validated($request);            // array with event + data[]
        $headers = $request->headers->all();              // <-- *all* request headers

        // 2) Work out whether a downloadable file exists ---------------------
        $downloadUrl = $this->downloadUrlFor($payload);   // returns ?string

        // 3) Persist callback meta‑data --------------------------------------
        DB::table('server_callbacks')->insert([
            'signature'  => (string) $request->header('x-signature'),
            'headers'    => json_encode($headers,  JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'payload'    => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4) Nothing to download? All done. ----------------------------------
        if ($downloadUrl === null) {
            return $this->json(['message' => 'No downloadable file for this event type.'], JsonResponse::HTTP_OK);
        }

        // 5) Download & persist the file -------------------------------------
        try {
            $storedPath = $this->downloadAndStore($downloadUrl);
        } catch (RequestException $e) {
            return $this->json(
                ['error' => 'Failed to fetch the file.', 'details' => $e->getMessage()],
                JsonResponse::HTTP_BAD_GATEWAY
            );
        }

        // 6) Tell the sender where we stored it ------------------------------
        return $this->json(['stored_as' => $storedPath], JsonResponse::HTTP_CREATED);
    }

    // ---------------------------------------------------------------- helpers
    private function validated(Request $request): array
    {
        return $request->validate([
            'event'   => ['required', 'string'],
            'data'    => ['required', 'array'],

            // nested keys (Laravel dot‑notation)
            'data.processed_file_download_url'         => ['nullable', 'url'],
            'data.eidas_certificate_download_url'      => ['nullable', 'url'],
            'data.blockchain_certificate_download_url' => ['nullable', 'url'],
        ]);
    }

    /** Resolve the correct URL for this event type. */
    private function downloadUrlFor(array $payload): ?string
    {
        return match ($payload['event']) {
            'WatermarkedFileAvailable'       => $payload['data']['processed_file_download_url']         ?? null,
            'EIDASCertificateAvailable'      => $payload['data']['edias_certificate_download_url']      ?? null,
            'BlockchainCertificateAvailable' => $payload['data']['blockchain_certificate_download_url'] ?? null,
            default                          => null,
        };
    }

    /** @throws RequestException */
    private function downloadAndStore(string $url): string
    {
        $responseBody = Http::withoutVerifying()   // accept any TLS certificate
            ->timeout(15)
            ->accept('*/*')                        // accept any MIME type
            ->get($url)
            ->throw()
            ->body();

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'bin';
        $filename  = Str::uuid() . '.' . $extension;

        Storage::disk('local')->put("webhooks/{$filename}", $responseBody);

        return "webhooks/{$filename}";
    }

    // ---------------------------------------------------------------- commons
    private function json(array $body, int $status): JsonResponse
    {
        return response()
            ->json($body, $status)
            ->withHeaders(self::CORS_HEADERS);     // accept *all* connections
    }
}
