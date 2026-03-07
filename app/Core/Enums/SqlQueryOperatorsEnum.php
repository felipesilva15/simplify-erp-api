<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="SqlQueryOperatorsEnum",
 *   type="string",
 *   description="SQL query operators:
 *      Equal = '='
 *      Like = 'like'
 *      Less than = '<'
 *      Less than equal = '<='
 *      Greater than = '>'
 *      Greater than equal = '>='
 *      Not equal = '!='",
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
