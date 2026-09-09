<?php 

namespace App\Modules\Partner\Enums;

/**
 * @OA\Schema(
 *   schema="PixTypeEnum",
 *   type="string",
 *   description="Pix type",
 *   enum={"EMAIL", "DOCUMENT", "RANDOM_KEY", "PHONE_NUMBER"}
 * )
 */
enum PixTypeEnum: string
{
    case Email          = 'EMAIL';
    case Document       = 'DOCUMENT';
    case RandomKey      = 'RANDOM_KEY';
    case PhoneNumber    = 'PHONE_NUMBER';

    public function label(): string
    {
        return match ($this) {
            self::Email         => 'E-mail',
            self::Document      => 'CPF/CNPJ',
            self::RandomKey     => 'Chave aleatória',
            self::PhoneNumber   => 'Telefone',
        };
    }
}