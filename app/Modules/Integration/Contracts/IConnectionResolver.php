<?php

namespace App\Modules\Integration\Contracts;

use App\Modules\Integration\Models\IntegrationConnection;

interface IConnectionResolver
{
    /**
     * Resolve active connection for provider. 
     * TODO: access to credentials must go through this interface (tenant-aware later).
     */
    public function resolve(string $provider): ?IntegrationConnection;

    /**
     * Get decrypted credentials array for connection.
     */
    public function getCredentials(IntegrationConnection $connection): array;
}
