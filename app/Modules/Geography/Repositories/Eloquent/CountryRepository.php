<?php

namespace App\Modules\Geography\Repositories\Eloquent;

use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Repositories\Interfaces\CountryRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'iso_code',
            'name'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'iso_code',
            'name'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Country::class;
    }
}