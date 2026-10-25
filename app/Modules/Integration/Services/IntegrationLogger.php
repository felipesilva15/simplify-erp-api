<?php

namespace App\Modules\Integration\Services;

use App\Modules\Integration\Enums\IntegrationDirection;
use App\Modules\Integration\Enums\IntegrationLogStatus;
use App\Modules\Integration\Models\IntegrationLog;
use App\Modules\Integration\Support\MaskingHelper;
use Illuminate\Support\Str;

class IntegrationLogger
{
    public function log(array $data): void
    {
        try {
            $sensitive = MaskingHelper::defaultSensitiveKeys();
            $requestMasked = isset($data['request']) && is_array($data['request']) 
                ? MaskingHelper::maskArray($data['request'], $sensitive) 
                : ($data['request'] ?? null);
            $responseMasked = isset($data['response']) && is_array($data['response'])
                ? MaskingHelper::maskArray($data['response'], $sensitive)
                : ($data['response'] ?? null);

            $requestTruncated = false;
            $responseTruncated = false;

            if (is_string($requestMasked)) {
                [$requestMasked, $requestTruncated] = MaskingHelper::truncate($requestMasked);
            } elseif (is_array($requestMasked)) {
                $json = json_encode($requestMasked);
                if ($json && strlen($json) > 10000) {
                    [$json, $requestTruncated] = MaskingHelper::truncate($json);
                    $requestMasked = json_decode($json, true) ?? ['truncated' => true];
                }
            }

            if (is_string($responseMasked)) {
                [$responseMasked, $responseTruncated] = MaskingHelper::truncate($responseMasked);
            } elseif (is_array($responseMasked)) {
                $json = json_encode($responseMasked);
                if ($json && strlen($json) > 10000) {
                    [$json, $responseTruncated] = MaskingHelper::truncate($json);
                    $responseMasked = json_decode($json, true) ?? ['truncated' => true];
                }
            }

            IntegrationLog::create([
                'provider' => $data['provider'] ?? null,
                'connection_id' => $data['connection_id'] ?? null,
                'operation' => $data['operation'] ?? null,
                'direction' => $data['direction'] ?? IntegrationDirection::OUTBOUND,
                'method' => $data['method'] ?? null,
                'url_sanitized' => isset($data['url']) ? MaskingHelper::sanitizeUrl($data['url']) : null,
                'status_code' => $data['status_code'] ?? null,
                'duration_ms' => $data['duration_ms'] ?? null,
                'request_masked' => is_array($requestMasked) ? json_encode($requestMasked) : $requestMasked,
                'request_truncated' => $requestTruncated,
                'response_masked' => is_array($responseMasked) ? json_encode($responseMasked) : $responseMasked,
                'response_truncated' => $responseTruncated,
                'status' => $data['status'] ?? IntegrationLogStatus::SUCCESS,
                'error_class' => $data['error_class'] ?? null,
                'correlation_id' => $data['correlation_id'] ?? (string) Str::uuid(),
            ]);
        } catch (\Throwable $e) {
            // Never throw from logger
        }
    }
}
