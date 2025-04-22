<?php 

// app/Enums/WebhookEvent.php
namespace App\Enums;

enum WebhookEvent: string
{
    case WATERMARKED_FILE       = 'WatermarkedFileAvailable';
    case EIDAS_CERTIFICATE      = 'EIDASCertificateAvailable';
    case BLOCKCHAIN_CERTIFICATE = 'BlockchainCertificateAvailable';

    public function downloadUrl(array $data): ?string
    {
        return match ($this) {
            self::WATERMARKED_FILE       => $data['processed_file_download_url']         ?? null,
            self::EIDAS_CERTIFICATE      => $data['eidas_certificate_download_url']      ?? null,
            self::BLOCKCHAIN_CERTIFICATE => $data['blockchain_certificate_download_url'] ?? null,
        };
    }
}
