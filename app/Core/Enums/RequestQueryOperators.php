<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="RequestQueryOperators",
 *   type="string",
 *   description="Request query operators:
 *              - Equal: 
 *              - Less than: lt
 *              - Less than equal: lte
 *              - Greater than: gt
 *              - Greater than equal: gte
 *              - Not equal: ne",
 *   enum={"", "lt", "lte", "gt", "gte", "ne"}
 * )
 */
enum RequestQueryOperators: string
{
    case Equal = '';
    case LessThan = 'lt';
    case LessThanEqual = 'lte';
    case GreaterThan = 'gt';
    case GreaterThanEqual = 'gte';
    case NotEqual = 'ne';
}
