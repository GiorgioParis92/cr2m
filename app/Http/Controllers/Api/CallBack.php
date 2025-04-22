<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CallBack
{
    public function __invoke(Request $request): JsonResponse
    {
        // 1) Validate & extract only what we need ------------------------------
        $payload = $this->validated($request);          // <-- array with event + data[]

        // 2) Work out whether a downloadable file exists ----------------------
        $downloadUrl = $this->downloadUrlFor($payload); // <-- safe, returns ?string

        if ($downloadUrl === null) {
            return $this->ok('No downloadable file for this event type.');
        }

        // 3) Download & persist the file --------------------------------------
        try {
            $storedPath = $this->downloadAndStore($downloadUrl);
        } catch (RequestException $e) {
            // Consider logging $e here for traceability
            return $this->badGateway('Failed to fetch the file.', $e->getMessage());
        }

        // 4) Tell the sender where we stored it -------------------------------
        return $this->created(['stored_as' => $storedPath]);
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
        $data = $payload['data'];         // validated() guarantees this is an array

        return match ($payload['event']) {
            'WatermarkedFileAvailable'       => $data['processed_file_download_url']         ?? null,
            'EIDASCertificateAvailable'      => $data['eidas_certificate_download_url']      ?? null,
            'BlockchainCertificateAvailable' => $data['blockchain_certificate_download_url'] ?? null,
            default                          => null,
        };
    }

    /** @throws RequestException */
    private function downloadAndStore(string $url): string
    {
        $responseBody = Http::timeout(15)->get($url)->throw()->body();

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'bin';
        $filename  = Str::uuid() . '.' . $extension;

        Storage::disk('local')->put("webhooks/{$filename}", $responseBody);

        return "webhooks/{$filename}";
    }

    // ------------------------------------------------------------ response shortcuts
    private function ok(string $message): JsonResponse
    {
        return response()->json(['message' => $message], JsonResponse::HTTP_OK);
    }

    private function created(array $data): JsonResponse
    {
        return response()->json($data, JsonResponse::HTTP_CREATED);
    }

    private function badGateway(string $error, string $details): JsonResponse
    {
        return response()->json(
            ['error' => $error, 'details' => $details],
            JsonResponse::HTTP_BAD_GATEWAY
        );
    }
}
