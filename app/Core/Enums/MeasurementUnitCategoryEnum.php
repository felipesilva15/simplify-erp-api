<?php 

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="MeasurementUnitCategoryEnum",
 *   type="string",
 *   description="Measurement unit categories:
 *      Count = 'COUNT'
 *      Mass = 'MASS'
 *      Volume = 'VOLUME'
 *      Length = 'LENGTH'
 *      Area = 'AREA'
 *      Time = 'TIME'"
 *   enum={"COUNT", "MASS", "VOLUME", "LENGTH", "AREA", "TIME"}
 * )
 */
enum MeasurementUnitCategoryEnum: string
{
    case Count  = 'COUNT';
    case Mass   = 'MASS';
    case Volume = 'VOLUME';
    case Length = 'LENGTH'; 
    case Area   = 'AREA';
    case Time   = 'TIME'; 

    public function label(): string
    {
        return match ($this) {
            self::Count  => 'Contagem',
            self::Mass   => 'Massa',
            self::Volume => 'Volume',
            self::Length => 'Comprimento',
            self::Area   => 'Área',
            self::Time   => 'Tempo',
        };
    }
}