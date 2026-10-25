<?php

namespace App\Modules\Integration\Models;

use App\Core\Models\BaseModel;
use App\Modules\Integration\Enums\WebhookEventStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationWebhookEvent extends BaseModel
{
    protected $table = 'integration_webhook_events';

    protected static ?string $morphAlias = 'integration-webhook-event';
    protected static ?string $activityLabel = 'Evento Webhook de Integração';

    protected $fillable = [
        'provider',
        'connection_id',
        'external_id',
        'headers',
        'payload',
        'status',
        'attempts',
        'processed_at',
        'error',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'status' => WebhookEventStatus::class,
        'attempts' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnection::class, 'connection_id');
    }
}
