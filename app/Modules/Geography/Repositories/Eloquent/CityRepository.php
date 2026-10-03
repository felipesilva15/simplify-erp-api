<?php

namespace App\Modules\Geography\Repositories\Eloquent;

use App\Modules\Geography\Models\City;
use App\Modules\Geography\Repositories\Interfaces\CityRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'name' => 'string',
            'ibge_code' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return City::class;
    }
}