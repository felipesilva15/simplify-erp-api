<?php

namespace App\Modules\Integration\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Integration\Contracts\IWebhookHandler;
use App\Modules\Integration\Enums\WebhookEventStatus;
use App\Modules\Integration\Jobs\ProcessWebhookEventJob;
use App\Modules\Integration\Models\IntegrationConnection;
use App\Modules\Integration\Models\IntegrationWebhookEvent;
use App\Modules\Integration\Registry\ProviderRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use OpenApi\Annotations as OA;

class WebhookController extends Controller
{
    public function __construct(protected ProviderRegistry $registry)
    {
    }

    /**
     * @OA\Post(
     *     path="/webhooks/{provider}/{connection}",
     *     tags={"Integration"},
     *     summary="Webhook inbox",
     *     @OA\Response(response="202", description="Accepted")
     * )
     */
    public function handle(Request $request, string $provider, ?string $connection = null): JsonResponse
    {
        $key = 'webhook:'.$provider.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response()->json(['message' => 'Too Many Requests'], Response::HTTP_TOO_MANY_REQUESTS);
        }
        RateLimiter::hit($key, 60);

        $connectionModel = null;
        if ($connection) {
            $connectionModel = IntegrationConnection::query()->where('uuid', $connection)->first();
        }

        $providerConfig = $this->registry->get($provider);
        if (!$providerConfig) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }

        $handlerClass = $providerConfig['webhook_handler'] ?? null;
        if (!$handlerClass || !class_exists($handlerClass)) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }

        $handler = app($handlerClass);
        if (!$handler instanceof IWebhookHandler) {
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }

        $rawPayload = $request->getContent();
        $headers = $request->headers->all();

        if (!$handler->validate($headers, $rawPayload, $connectionModel)) {
            return response()->json(['message' => 'Invalid signature'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $request->all();
        $externalId = $payload['id'] ?? $payload['event_id'] ?? $payload['external_id'] ?? null;

        // Deduplicate by external_id per provider
        if ($externalId) {
            $existing = IntegrationWebhookEvent::query()
                ->where('provider', $provider)
                ->where('external_id', $externalId)
                ->first();
            if ($existing) {
                return response()->json(['message' => 'Accepted'], Response::HTTP_OK);
            }
        }

        $event = IntegrationWebhookEvent::create([
            'provider' => $provider,
            'connection_id' => $connectionModel?->id,
            'external_id' => $externalId,
            'headers' => $headers,
            'payload' => $payload,
            'status' => WebhookEventStatus::PENDING,
            'attempts' => 0,
        ]);

        ProcessWebhookEventJob::dispatch($event->id)->afterCommit();

        return response()->json(['message' => 'Accepted'], Response::HTTP_ACCEPTED);
    }
}
