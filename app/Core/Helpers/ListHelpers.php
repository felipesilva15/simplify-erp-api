<?php

namespace App\Core\Helpers;

class ListHelpers
{
    public static function groupListByProperty(array $list, string $propertyName): array {
        $grouped = collect($list)
                        ->pluck($propertyName)
                        ->all();

        return $grouped;
    }
}