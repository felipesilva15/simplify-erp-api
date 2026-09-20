<?php

namespace App\Core\Helpers;

class EnumHelpers
{
    public static function mapLabelsWithKeys(array $enumCases): array {
        if (!$enumCases || !count($enumCases))
            return $enumCases;
        
        return collect($enumCases)->mapWithKeys(function ($enum) {
            return [$enum->value => $enum->label()]; 
        })->all();
    }
}