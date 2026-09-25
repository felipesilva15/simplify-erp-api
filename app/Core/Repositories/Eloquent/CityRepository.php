<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\City;
use App\Core\Repositories\Interfaces\CityRepositoryInterface;
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