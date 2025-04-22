<?php
// app/Http/Controllers/Api/ServerCallbackController.php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ServerCallbackController
{
    /** CORS headers that accept any origin, method, and header. */
    private const CORS = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
       'Access-Control-Allow-Headers'     => '*',
      'Access-Control-Allow-Headers'     => 'Content-Type, X-Requested-With, X-CEERTIF-SECRET, X-Signature',
        'Access-Control-Allow-Credentials' => 'true',
      'Content-Type'                     => 'application/json; charset=UTF-8',
    ];

    public function __invoke(Request $request): JsonResponse
    {
        // $payload = $this->payloadFromRequest($request);
        // $payload = $this->payloadFromRequest($request);
        $payload = json_decode(
            $request->getContent(),      // raw body
            true,                        // ← associative array, not objects
            512,                         // recursion depth
            JSON_THROW_ON_ERROR          // fail fast on bad JSON
        );      
   
        // 2. Persist raw call for audit / debug --------------------------------
        $this->persistRaw($request, $payload);
    
        // 3. Work out whether we need to download a file -----------------------
        $downloadUrl = $this->downloadUrlFor($payload);
    
        if ($downloadUrl === null) {
            return $this->ok('No downloadable file for this event type.');
        }
    
        // 4. Download & store --------------------------------------------------
        try {
            $storedPath = $this->downloadAndStore(
                $downloadUrl,
                $request->header('X-CEERTIF-SECRET')
            );
        } catch (\Throwable $e) {
            return $this->error('Failed to fetch the file.', JsonResponse::HTTP_BAD_GATEWAY);
        }
    
        // 5. Reply to Ceertif ---------------------------------------------------
        return $this->created(['stored_as' => $storedPath]);
    }

    // ---------------------------------------------------------------- helpers
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

        // Guard against malformed payloads in the simplest possible way
        if (!isset($payload['event'], $payload['data']) || !is_array($payload['data'])) {
            return null;
        }

        return match ($payload['event']) {
            'WatermarkedFileAvailable'       => $payload['data']['processed_file_download_url']         ?? null,
            'EIDASCertificateAvailable'      => $payload['data']['edias_certificate_download_url']      ?? null,
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
        return $this->json($body, JsonResponse::HTTP_OK);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }

    private function json(array|string $body, int $status): JsonResponse
    {
        return response()->json($body, $status)->withHeaders(self::CORS);
    }

        private function cleanJsonBody(string $raw): string
    {
        // replace "\tfoo" with "foo", but keep "\t" that are inside strings
        return preg_replace('/^(\\\\[tnr])+|\\\\[tnr]+$/m', '', $raw);
    }


    private function payloadFromRequest(Request $request): array
{
    $raw = $request->getContent();

    // 1 Keep only the part between the first { and the matching last }
    $start = strpos($raw, '{');
    $end   = strrpos($raw, '}');

    if ($start === false || $end === false || $end <= $start) {
        throw new \RuntimeException('No JSON object found in request body.');
    }
 
    $json = substr($raw, $start, $end - $start + 1);
 
    // 2 Remove "\t", "\n" and "\r" that appear OUTSIDE strings
    //    (preg_replace treats \\ as a single literal back‑slash)
    $json = str_replace(['\\t', '\\n', '\\r'], '', $json);
 
    // 3 Decode (throws JsonException on failure – Laravel will return 500)
    return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
}
}
