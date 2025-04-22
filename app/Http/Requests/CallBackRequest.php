<?php
//  app/Http/Requests/CallbackRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CallbackRequest extends FormRequest   // ← class name **must** match file name
{
    public function authorize()
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'event'   => ['required', 'string'],
            'data'    => ['required', 'array'],
            'data.processed_file_download_url'         => ['nullable', 'url'],
            'data.eidas_certificate_download_url'      => ['nullable', 'url'],
            'data.blockchain_certificate_download_url' => ['nullable', 'url'],
        ];
    }
}
