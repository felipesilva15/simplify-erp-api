<?php

namespace App\Modules\Geography\Repositories\Eloquent;

use App\Modules\Geography\Models\State;
use App\Modules\Geography\Repositories\Interfaces\StateRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'uf',
            'ibge_code'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'name',
            'uf',
            'ibge_code'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return State::class;
    }
}