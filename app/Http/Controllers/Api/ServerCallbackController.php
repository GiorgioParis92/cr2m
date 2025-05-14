<?php
// app/Http/Controllers/Api/ServerCallbackController.php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Dossier;

use Illuminate\Http\Client\RequestException;

final class ServerCallbackController
{
    /** CORS headers that accept any origin, method, and header. */
    private const CORS = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Content-Type, X-Requested-With, X-CEERTIF-SECRET, X-Signature',
        'Access-Control-Allow-Credentials' => 'true',
        'Content-Type'                     => 'application/json; charset=UTF-8',
    ];

    public function __invoke(Request $request): JsonResponse
    {
  
        $payload = $request->getContent();
   
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (is_string($decoded)) {
           
            $payload=json_decode($decoded,true);
       
            }
            
        }

        // Persist raw call for audit/debug
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
                $request->header('X-CEERTIF-SECRET'),
                $payload['data']['file_display_name'] ?? null,
                $payload['data']['intervention_id'] ?? null
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

    private function downloadAndStore(string $url, ?string $secret, ?string $fileDisplayName = null, ?string $dossier_id = null): string
    {
        try {
            $dossier = Dossier::where('id',$dossier_id)->first();

            $response = Http::withHeaders([
                    'X-CEERTIF-SECRET' => $secret
                ])
                ->withoutVerifying()
                ->timeout(15)
                ->accept('*/*')
                ->get($url);
    
            $response->throw();
    
            $fileContents = $response->body();
            $extension    = $this->getFileExtensionFromUrl($url);
            $baseName     = $this->sanitizeFileName($fileDisplayName ?? Str::uuid());
            $fileName     = "{$baseName}.{$extension}";
            $filePath     = "public/dossiers/{$dossier->folder}/{$fileName}";
    
            Storage::put($filePath, $fileContents);
    
            return $filePath;
        } catch (RequestException $e) {
            throw new \RuntimeException("Failed to download file from URL: {$url}", 0, $e);
        }
    }
    
    
    private function getFileExtensionFromUrl(string $url): string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryParams);
    
        if (isset($queryParams['path'])) {
            $filePath = urldecode($queryParams['path']);
            return pathinfo($filePath, PATHINFO_EXTENSION) ?: 'bin';
        }
    
        // Fallback if 'path' param doesn't exist
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        return pathinfo($path, PATHINFO_EXTENSION) ?: 'bin';
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


    private function sanitizeFileName(string $name): string
    {
        // Remove accents, convert to ASCII, replace non-alphanumeric characters with underscores
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        $name = preg_replace('/[^A-Za-z0-9_-]/', '_', $name);
        $name = trim($name, '_');
    
        return $name ?: 'file';
    }
    
}
