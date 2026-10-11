<?php

namespace App\Modules\Geography\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Repositories\Interfaces\AddressRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AddressService extends BaseCrudService
{
    public const BRAZIL_COUNTRY_ISO = 'BR';

    /** @var list<string> */
    private const NATIONAL_ADDRESS_FIELDS = ['state_id', 'city_id', 'district', 'street', 'number'];

    /** @var list<string> */
    private const FOREIGN_ADDRESS_FIELDS = ['state_name', 'city_name', 'address_line_1', 'address_line_2'];

    public function __construct(AddressRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    /**
     * Valida os campos nacional/estrangeiro conforme o país do endereço. Endereços
     * nacionais (Brasil) exigem os campos nacionais e não aceitam os estrangeiros;
     * endereços de outros países seguem a regra inversa. O prefixo permite reusar a
     * regra em payloads aninhados (ex.: addresses.* de um parceiro).
     */
    public static function assertValidAddressData(array $payload, string $prefix = ''): void
    {
        $countryId = $payload['country_id'] ?? null;
        $isBrazil = $countryId !== null && Country::whereKey($countryId)
            ->where('iso_code', self::BRAZIL_COUNTRY_ISO)
            ->exists();

        $requiredFields = $isBrazil ? self::NATIONAL_ADDRESS_FIELDS : self::FOREIGN_ADDRESS_FIELDS;
        $prohibitedFields = $isBrazil ? self::FOREIGN_ADDRESS_FIELDS : self::NATIONAL_ADDRESS_FIELDS;

        $errors = [];

        foreach ($requiredFields as $field) {
            if (blank($payload[$field] ?? null)) {
                $errors[$prefix.$field] = sprintf(
                    'Este campo é obrigatório para %s.',
                    $isBrazil ? 'um endereço nacional' : 'um endereço estrangeiro'
                );
            }
        }

        foreach ($prohibitedFields as $field) {
            if (filled($payload[$field] ?? null)) {
                $errors[$prefix.$field] = sprintf(
                    'Este campo não pode ser informado em %s.',
                    $isBrazil ? 'um endereço nacional' : 'um endereço estrangeiro'
                );
            }
        }

        if ($errors !== []) {
            throw new BusinessRuleException(
                message: 'Um ou mais campos do endereço não foram preenchidos corretamente.',
                errors: $errors
            );
        }
    }

    protected function beforeStore(mixed $data): void
    {
        self::assertValidAddressData($data->toArray());
        $this->assertNoOtherPrimaryFor($data->toArray());
    }

    protected function beforeUpdate(Model $entity, mixed $data): void
    {
        self::assertValidAddressData($data->toArray());
        $this->assertNoOtherPrimaryFor($data->toArray(), $entity->getKey());
    }

    /**
     * Garante, no CRUD próprio, que apenas um endereço por registro dono
     * (addressable) seja marcado como principal.
     */
    private function assertNoOtherPrimaryFor(array $payload, mixed $ignoreId = null): void
    {
        if (! (bool) ($payload['is_primary'] ?? false)) {
            return;
        }

        $exists = Address::query()
            ->where('addressable_type', $payload['addressable_type'])
            ->where('addressable_id', $payload['addressable_id'])
            ->where('is_primary', true)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw new BusinessRuleException(
                message: 'Apenas um endereço pode ser definido como principal.',
                errors: ['is_primary' => 'Apenas um endereço pode ser definido como principal para este registro.']
            );
        }
    }
}
