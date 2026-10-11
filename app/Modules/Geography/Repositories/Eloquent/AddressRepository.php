<?php

namespace App\Modules\Geography\Repositories\Eloquent;

use App\Core\Repositories\Eloquent\BaseRepository;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Repositories\Interfaces\AddressRepositoryInterface;
use Override;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    #[Override]
    protected function withRelations(): array
    {
        return [
            'country',
            'state',
            'city',
        ];
    }

    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'addressable_type',
            'type',
            'district',
            'street',
            'city_name',
            'address_line_1',
            'postal_code',
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Address::class;
    }
}
