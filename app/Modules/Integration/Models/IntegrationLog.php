<?php

namespace App\Modules\Integration\Models;

use App\Core\Models\BaseModel;
use App\Modules\Integration\Enums\IntegrationDirection;
use App\Modules\Integration\Enums\IntegrationLogStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends BaseModel
{
    protected $table = 'integration_logs';

    protected static ?string $morphAlias = 'integration-log';
    protected static ?string $activityLabel = 'Log de Integração';

    protected $fillable = [
        'provider',
        'connection_id',
        'operation',
        'direction',
        'method',
        'url_sanitized',
        'status_code',
        'duration_ms',
        'request_masked',
        'request_truncated',
        'response_masked',
        'response_truncated',
        'status',
        'error_class',
        'correlation_id',
    ];

    protected $casts = [
        'direction' => IntegrationDirection::class,
        'status' => IntegrationLogStatus::class,
        'duration_ms' => 'integer',
        'status_code' => 'integer',
        'request_truncated' => 'boolean',
        'response_truncated' => 'boolean',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnection::class, 'connection_id');
    }
}
