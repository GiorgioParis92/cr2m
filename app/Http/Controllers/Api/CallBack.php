<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

 class CallBack extends Controller
{
    /**
     * Handle the incoming server callback.
     */
    public function __invoke(Request $request): Response
    {

        dd(Response::HTTP_OK);
        // 1️⃣  Headers (replacement for getallheaders())
        $headers = $this->collectHeaders($request);

        // 2️⃣  Raw JSON body (replacement for php://input + json_decode)
        $payload = $this->decodeJson($request->getContent());

        // 3️⃣  Persist in one shot
        $this->storeCallback($headers, $payload);

        // 4️⃣  Acknowledge
        return response()->noContent(Response::HTTP_OK);
    }

    // ────────────────────────────────────────────────────────────
    // Private helpers keep __invoke() focused and testable
    // ────────────────────────────────────────────────────────────

    private function collectHeaders(Request $request): Collection
    {
        return collect($request->headers->all())
            ->mapWithKeys(fn (array $values, string $name) => [$name => $values[0] ?? '']);
    }


    
    private function decodeJson(string $rawBody)
    {
        return json_decode($rawBody, true) ?? [];
    }

    private function storeCallback(Collection $headers, array $payload): void
    {
        DB::table('server_callbacks')->insert([
            'signature'  => $headers->get('x-custom-signature'),
            'headers'    => json_encode($headers->all(),  JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'payload'    => json_encode($payload,        JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
