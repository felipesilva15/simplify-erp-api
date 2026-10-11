<?php

namespace App\Modules\ThirdParty\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\Children\BaseChildSync;
use App\Modules\Geography\Services\AddressService;
use Illuminate\Database\Eloquent\Model;

class PartnerAddressSync extends BaseChildSync
{
    protected function validateItem(array $payload, int $index, ?Model $current = null): void
    {
        AddressService::assertValidAddressData($payload, $this->itemPath($index, ''));
    }

    protected function validatePayload(array $payloads, array $existing): void
    {
        $mainPaths = [];

        foreach ($payloads as $index => $payload) {
            $current = $this->findCurrentItem($payload, $existing);

            $isPrimary = array_key_exists('is_primary', $payload)
                ? (bool) $payload['is_primary']
                : (bool) ($current?->is_primary);

            if ($isPrimary) {
                $mainPaths[] = $this->itemPath($index, 'is_primary');
            }
        }

        if (count($mainPaths) <= 1) {
            return;
        }

        throw new BusinessRuleException(
            message: 'Apenas um endereço pode ser definido como principal.',
            errors: array_fill_keys(
                $mainPaths,
                'Apenas um endereço pode ser definido como principal.'
            )
        );
    }
}
