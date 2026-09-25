<?php

namespace App\Modules\Partner\Repositories\Eloquent;

use App\Modules\Partner\Models\Contact;
use App\Modules\Partner\Repositories\Interfaces\ContactRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Contact::class;
    }
}