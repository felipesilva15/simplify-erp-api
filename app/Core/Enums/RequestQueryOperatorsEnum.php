<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="RequestQueryOperatorsEnum",
 *   type="string",
 *   description="Request query operators: eq, like, lt, lte, gt, gte, ne",
 *   enum={"eq", "like", "lt", "lte", "gt", "gte", "ne"}
 * )
 */
enum RequestQueryOperatorsEnum: string
{
    case Equal = 'eq';
    case Like = 'like';
    case LessThan = 'lt';
    case LessThanEqual = 'lte';
    case GreaterThan = 'gt';
    case GreaterThanEqual = 'gte';
    case NotEqual = 'ne';
}
