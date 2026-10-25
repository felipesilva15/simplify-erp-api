<?php

namespace App\Modules\Integration\Contracts;

use App\Modules\Integration\Models\IntegrationWebhookEvent;

interface IWebhookHandler
{
    /**
     * Validate webhook signature.
     */
    public function validate(array $headers, string $rawPayload, ?IntegrationConnection $connection): bool;

    /**
     * Handle webhook event.
     */
    public function handle(IntegrationWebhookEvent $event): void;
}
