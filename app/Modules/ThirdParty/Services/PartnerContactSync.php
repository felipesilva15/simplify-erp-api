<?php

namespace App\Modules\ThirdParty\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\Children\BaseChildSync;
use Illuminate\Database\Eloquent\Model;

class PartnerContactSync extends BaseChildSync
{
    private const CONTACT_CHANNELS = ['email', 'mobile', 'phone'];

    protected function validateItem(array $payload, int $index, ?Model $current = null): void
    {
        $hasChannel = false;

        foreach (self::CONTACT_CHANNELS as $field) {
            if (filled($payload[$field] ?? null)) {
                $hasChannel = true;
                break;
            }
        }

        if ($hasChannel) {
            return;
        }

        throw new BusinessRuleException(
            message: sprintf('O contato [%d] do parceiro deve possuir ao menos um meio de contato.', $index),
            errors: [
                $this->itemPath($index, 'email') => sprintf(
                    'Informe ao menos um meio de contato (%s).',
                    implode(', ', self::CONTACT_CHANNELS)
                ),
            ]
        );
    }

    protected function validatePayload(array $payloads, array $existing): void
    {
        $mainPaths = [];

        foreach ($payloads as $index => $payload) {
            $current = $this->findCurrentItem($payload, $existing);

            $isMain = array_key_exists('main', $payload)
                ? (bool) $payload['main']
                : (bool) ($current?->main);

            if ($isMain) {
                $mainPaths[] = $this->itemPath($index, 'main');
            }
        }

        if (count($mainPaths) <= 1) {
            return;
        }

        throw new BusinessRuleException(
            message: 'Apenas um contato pode ser definido como principal do parceiro.',
            errors: array_fill_keys(
                $mainPaths,
                'Apenas um contato pode ser definido como principal do parceiro.'
            )
        );
    }
}
