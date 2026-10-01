<?php

namespace App\Modules\Geography\Repositories\Eloquent;

use App\Modules\Geography\Models\State;
use App\Modules\Geography\Repositories\Interfaces\StateRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'name' => 'string',
            'uf' => 'string',
            'ibge_code' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return State::class;
    }
}