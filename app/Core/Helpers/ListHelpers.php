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

    public static function removeNullProperties(mixed $item): mixed {
        if (!is_array($item)) {
            return $item;
        }

        return collect($item)
                ->reject(function ($item) {
                    return is_null($item);
                })
                ->flatMap(function ($item, $key) {
                    return is_numeric($key)
                        ? [ListHelpers::removeNullProperties($item)]
                        : [$key => ListHelpers::removeNullProperties($item)];
                })
                ->toArray();
    }
}