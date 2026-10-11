<?php

namespace App\Modules\ThirdParty\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Services\Children\ChildRelation;
use App\Modules\Geography\Models\Address;
use App\Modules\ThirdParty\Enums\PersonTypeEnum;
use App\Modules\ThirdParty\Enums\TaxpayerTypeEnum;
use App\Modules\ThirdParty\Models\Contact;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PartnerService extends BaseCrudService
{
    public function __construct(PartnerRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function childRelations(): array
    {
        return [
            'contacts' => new ChildRelation(
                relation: 'contacts',
                model: Contact::class,
                sync: PartnerContactSync::class
            ),
            'addresses' => new ChildRelation(
                relation: 'addresses',
                model: Address::class,
                sync: PartnerAddressSync::class
            ),
        ];
    }

    protected function loadChildren(?Model $entity, ?array $relations = null): ?Model
    {
        $relations ??= [
            'contacts',
            'addresses.country',
            'addresses.state',
            'addresses.city',
        ];

        return parent::loadChildren($entity, $relations);
    }

    protected function prepareData(mixed $data): mixed
    {
        if ($data->person_type == PersonTypeEnum::Person->value) {
            $data->taxpayer_type = TaxpayerTypeEnum::Exempt->value;
        }

        return $data;
    }
}
