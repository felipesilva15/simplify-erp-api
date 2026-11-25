<?php

namespace App\Modules\Finance\Repositories\Eloquent;

use App\Modules\Finance\Models\PaymentMethod;
use App\Modules\Finance\Repositories\Interfaces\PaymentMethodRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'condition',
            'applicable_type',
            'tax_type'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'condition',
            'applicable_type',
            'tax_type'
        ];
    }

    #[Override]
    protected function getLookupFilterColumns(): array
    {
        return [
            'name',
            'condition',
            'applicable_type',
            'tax_type',
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return PaymentMethod::class;
    }
}