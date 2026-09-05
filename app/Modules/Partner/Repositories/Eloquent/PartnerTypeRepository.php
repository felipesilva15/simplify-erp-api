<?php

namespace App\Modules\Partner\Repositories\Eloquent;

use App\Modules\Partner\Models\PartnerType;
use App\Modules\Partner\Repositories\Interfaces\PartnerTypeRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PartnerTypeRepository extends BaseRepository implements PartnerTypeRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return PartnerType::class;
    }
}