<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="SqlQueryOperators",
 *   type="string",
 *   description="SQL query operators:
 *              - Equal: =
 *              - Less than: <
 *              - Less than equal: <=
 *              - Greater than: >
 *              - Greater than equal: >=
 *              - Not equal: !=",
 *   enum={"=", "<", "<=", ">", ">=", "!="}
 * )
 */
enum SqlQueryOperators: string
{
    case Equal = '=';
    case LessThan = '<';
    case LessThanEqual = '<=';
    case GreaterThan = '>';
    case GreaterThanEqual = '>=';
    case NotEqual = '!=';
}
