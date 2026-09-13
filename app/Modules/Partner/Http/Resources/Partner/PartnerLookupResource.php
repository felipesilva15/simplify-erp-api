<?php

namespace App\Modules\Partner\Http\Resources\Partner;

use App\Modules\Partner\Enums\PersonTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Sample Company LTDA", minLength=1, maxLength=120),
 *          @OA\Property(property="trade_name", type="string", example="Sample", minLength=1, maxLength=150),
 *          @OA\Property(property="document_number", type="string", example="11.222.333/0001-81", minLength=1, maxLength=20),
 *          @OA\Property(property="person_type", type="string", example="Pessoa Jurídica", minLength=1, maxLength=30)
 *      )
 * )
 */
class PartnerLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        switch ($this->person_type) {
            case PersonTypeEnum::Person:
                $documentLabel = 'CPF';
                break;

            case PersonTypeEnum::Company:
                $documentLabel = 'CNPJ';
                break;
            
            default:
                $documentLabel = 'ID ext.';
                break;
        }

        $documentLabel = match ($this->person_type) {
            PersonTypeEnum::Person => 'CPF',
            PersonTypeEnum::Company => 'CNPJ',
            default => 'ID ext.'
        };

        $nameLabel = match ($this->person_type) {
            PersonTypeEnum::Company => 'Razão',
            default => 'Nome'
        };

        return [
            'key' => $this->id,
            'label' => $this->trade_name,
            'sublabel' => "Cod.: {$this->id} | {$documentLabel}: {$this->document_number} | {$nameLabel}: {$this->name}",
            'meta' => $this->only('id', 'name', 'trade_name', 'document_number', 'person_type')
        ];
    }
}