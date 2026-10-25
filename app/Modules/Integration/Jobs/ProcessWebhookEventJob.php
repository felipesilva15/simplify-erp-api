<?php

namespace App\Modules\Integration\Jobs;

use App\Modules\Integration\Contracts\IWebhookHandler;
use App\Modules\Integration\Enums\WebhookEventStatus;
use App\Modules\Integration\Models\IntegrationWebhookEvent;
use App\Modules\Integration\Registry\ProviderRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int|string $webhookEventId)
    {
    }

    public function handle(ProviderRegistry $registry): void
    {
        $event = IntegrationWebhookEvent::find($this->webhookEventId);
        if (!$event) {
            return;
        }

        if ($event->status === WebhookEventStatus::PROCESSED) {
            return;
        }

        $event->status = WebhookEventStatus::PROCESSING;
        $event->attempts = ($event->attempts ?? 0) + 1;
        $event->save();

        $providerConfig = $registry->get($event->provider);
        $handlerClass = $providerConfig['webhook_handler'] ?? null;

        if (!$handlerClass || !class_exists($handlerClass)) {
            $event->status = WebhookEventStatus::FAILED;
            $event->error = 'Webhook handler não registrado para o provedor.';
            $event->save();
            return;
        }

        $handler = app($handlerClass);
        if (!$handler instanceof IWebhookHandler) {
            $event->status = WebhookEventStatus::FAILED;
            $event->error = 'Webhook handler inválido.';
            $event->save();
            return;
        }

        try {
            $handler->handle($event);
            $event->status = WebhookEventStatus::PROCESSED;
            $event->processed_at = now();
            $event->error = null;
            $event->save();
        } catch (\Throwable $e) {
            $event->status = WebhookEventStatus::FAILED;
            $event->error = $e->getMessage();
            $event->save();
            throw $e;
        }
    }
}
