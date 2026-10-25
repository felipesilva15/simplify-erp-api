<?php

namespace App\Modules\Integration\Registry;

use Illuminate\Support\Collection;

class ProviderRegistry
{
    protected array $providers = [];

    public function register(string $key, array $config): void
    {
        $this->providers[$key] = array_merge([
            'key' => $key,
            'capabilities' => [],
            'factory' => null,
        ], $config);
    }

    public function all(): Collection
    {
        return collect($this->providers)->values();
    }

    public function get(string $key): ?array
    {
        return $this->providers[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->providers[$key]);
    }
}
