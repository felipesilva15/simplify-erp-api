<?php

namespace App\Modules\Integration\Support;

class IntegrationCacheKey
{
    public static function make(string $provider, string $key): string
    {
        // Today only prefixes provider; tomorrow may include tenant
        $prefix = config('integrations.cache.prefix', 'integrations');
        return "{$prefix}:{$provider}:{$key}";
    }
}
