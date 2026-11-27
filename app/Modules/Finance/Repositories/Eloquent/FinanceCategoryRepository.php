<?php

namespace App\Modules\Finance\Repositories\Eloquent;

use App\Modules\Finance\Models\FinanceCategory;
use App\Modules\Finance\Repositories\Interfaces\FinanceCategoryRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class FinanceCategoryRepository extends BaseRepository implements FinanceCategoryRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'code',
            'name'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'code',
            'name'
        ];
    }

    #[Override]
    protected function getLookupFilterColumns(): array
    {
        return [
            'parent_id',
            'code',
            'name'
        ];
    }

    #[Override]
    protected function getLookupKeyColumn(): string
    {
        return 'id';
    }

    #[Override]
    protected function getModelClass(): string
    {
        return FinanceCategory::class;
    }
}