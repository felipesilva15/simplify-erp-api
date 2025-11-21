<?php

namespace App\Core\Helpers;

class StringHelpers
{
    public static function toStringLiteral(mixed $value): string {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_string($value)) {
            return "'" . $value . "'";
        }

        return (string) $value;
    }
}