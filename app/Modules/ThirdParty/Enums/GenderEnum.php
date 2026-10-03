<?php 

namespace App\Modules\ThirdParty\Enums;

/**
 * @OA\Schema(
 *   schema="GenderEnum",
 *   type="string",
 *   description="Gender",
 *   enum={"MALE", "FEMALE", "OTHER"}
 * )
 */
enum GenderEnum: string
{
    case Male   = 'MALE';
    case Female = 'FEMALE';
    case Other  = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::Male      => 'Masculino',
            self::Female    => 'Feminino',
            self::Other     => 'Outro',
        };
    }
}