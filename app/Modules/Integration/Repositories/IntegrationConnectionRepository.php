<?php

namespace App\Modules\Integration\Repositories;

use App\Core\Repositories\Eloquent\BaseRepository;
use App\Modules\Integration\Models\IntegrationConnection;

class IntegrationConnectionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new IntegrationConnection());
    }

    public function getModelClass(): string
    {
        return IntegrationConnection::class;
    }

    public function getListColumnsToFilter(): array
    {
        return ['id', 'uuid', 'provider', 'name', 'is_active'];
    }

    public function getSearchableColumns(): array
    {
        return ['uuid', 'provider', 'name'];
    }
}
