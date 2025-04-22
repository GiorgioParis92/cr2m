<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CallBack
{
    /**
     * Handle the webhook callback.
     */
    public function __invoke(Request $request): Response
    {
        // 1️⃣  Validate & normalise the payload --------------------------------
        $payload = $this->validated($request);

        // 2️⃣  Decide if a downloadable file exists for this event --------------
        $downloadUrl = $this->downloadUrlFor($payload);

        if ($downloadUrl === null) {
            return response()->json([
                'message' => 'No downloadable file for this event type.',
            ], Response::HTTP_OK);
        }

        // 3️⃣  Download + persist the file -------------------------------------
        try {
            $storedPath = $this->downloadAndStore($downloadUrl);
        } catch (RequestException $e) {
            // You might prefer to log the exact error instead.
            return response()->json([
                'error'   => 'Failed to fetch the file.',
                'details' => $e->getMessage(),
            ], Response::HTTP_BAD_GATEWAY);
        }

        // 4️⃣  Respond to the sender -------------------------------------------
        return response()->json([
            'stored_as' => $storedPath,
        ], Response::HTTP_CREATED);
    }

    /**
     * Only keep the fields we care about and ensure they’re present.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'event'                               => 'required|string',
            'processed_file_download_url'         => 'nullable|url',
            'edias_certificate_download_url'      => 'nullable|url',
            'blockchain_certificate_download_url' => 'nullable|url',
        ]);
    }

    /**
     * Pick the correct URL for the given event.
     */
    private function downloadUrlFor(array $payload): ?string
    {
        return match ($payload['event']) {
            'WatermarkedFileAvailable'  => $payload['processed_file_download_url']         ?? null,
            'EIDASCertificateAvailable' => $payload['edias_certificate_download_url']      ?? null,
            'BlockchainCertificateAvailable' => $payload['blockchain_certificate_download_url'] ?? null,
            default => null,
        };
    }

    /**
     * Fetch the file and save it to storage/app/webhooks (configurable).
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function downloadAndStore(string $url): string
    {
        $responseBody = Http::timeout(15)->get($url)->throw()->body();

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'bin';

        $filename = Str::uuid() . '.' . $extension;

        // Use a dedicated disk/folder so you can trim files later with a simple
        // Storage::disk('webhooks')->delete($oldPath);
        Storage::disk('local')->put("webhooks/{$filename}", $responseBody);

        return "webhooks/{$filename}";
    }
}
