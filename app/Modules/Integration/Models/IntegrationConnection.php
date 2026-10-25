<?php

namespace App\Modules\Integration\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class IntegrationConnection extends BaseModel
{
    protected $table = 'integration_connections';

    protected static ?string $morphAlias = 'integration-connection';
    protected static ?string $activityLabel = 'Conexão de Integração';

    protected $fillable = [
        'uuid',
        'provider',
        'name',
        'credentials',
        'settings',
        'is_active',
        'last_used_at',
    ];

    protected $hidden = [
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (IntegrationConnection $connection) {
            if (empty($connection->uuid)) {
                $connection->uuid = (string) Str::uuid();
            }
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class, 'connection_id');
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(IntegrationWebhookEvent::class, 'connection_id');
    }
}
