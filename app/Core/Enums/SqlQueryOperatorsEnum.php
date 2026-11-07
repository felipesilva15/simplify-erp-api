<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="SqlQueryOperatorsEnum",
 *   type="string",
 *   description="SQL query operators: =, like, <, <=, >, >=, !=",
 *   enum={"=", "like", "<", "<=", ">", ">=", "!="}
 * )
 */
enum SqlQueryOperatorsEnum: string
{
    case Equal = '=';
    case Like = 'like';
    case LessThan = '<';
    case LessThanEqual = '<=';
    case GreaterThan = '>';
    case GreaterThanEqual = '>=';
    case NotEqual = '!=';
}
