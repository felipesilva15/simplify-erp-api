<?php

namespace App\Modules\Integration\Support;

class MaskingHelper
{
    public static function defaultSensitiveKeys(): array
    {
        return config('integrations.logging.sensitive_keys', [
            'password',
            'senha',
            'token',
            'access_token',
            'refresh_token',
            'authorization',
            'auth',
            'secret',
            'client_secret',
            'api_key',
            'apikey',
            'key',
            'private_key',
            'cert',
            'certificate',
            'credentials',
            'card_number',
            'cvv',
            'cvc',
            'pan',
        ]);
    }

    public static function maskArray(array $data, array $sensitiveKeys = null): array
    {
        $sensitiveKeys = $sensitiveKeys ?? self::defaultSensitiveKeys();
        $masked = [];

        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);
            $isSensitive = false;
            foreach ($sensitiveKeys as $sk) {
                if (str_contains($lowerKey, strtolower((string)$sk))) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $masked[$key] = '***MASKED***';
                continue;
            }

            if (is_array($value)) {
                $masked[$key] = self::maskArray($value, $sensitiveKeys);
                continue;
            }

            if (is_string($value) && strlen($value) > 2000) {
                $masked[$key] = '[TRUNCATED]';
                continue;
            }

            $masked[$key] = $value;
        }

        return $masked;
    }

    public static function sanitizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed) {
            return $url;
        }
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '';
        return $scheme.'://'.$host.$path;
    }

    public static function truncate(string $data, int $max = 10000): array
    {
        if (strlen($data) <= $max) {
            return [$data, false];
        }
        return [substr($data, 0, $max), true];
    }
}
