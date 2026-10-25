<?php

namespace App\Modules\Integration\Services;

use App\Modules\Integration\Contracts\IConnectionResolver;
use App\Modules\Integration\Models\IntegrationConnection;

class SingleConnectionResolver implements IConnectionResolver
{
    public function resolve(string $provider): ?IntegrationConnection
    {
        return IntegrationConnection::query()
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    public function getCredentials(IntegrationConnection $connection): array
    {
        // credentials are encrypted:array cast; return as array
        return is_array($connection->credentials) ? $connection->credentials : [];
    }
}
