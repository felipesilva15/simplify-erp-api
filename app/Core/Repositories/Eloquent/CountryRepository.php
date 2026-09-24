<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Country;
use App\Core\Repositories\Interfaces\CountryRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'iso_code' => 'string',
            'name' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Country::class;
    }
}