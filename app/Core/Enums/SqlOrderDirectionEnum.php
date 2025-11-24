<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="SqlOrderDirectionEnum",
 *   type="string",
 *   description="SQL order direction:
 *      Ascending = 'asc'
 *      Descending = 'desc'",
 *   enum={"asc", "desc"}
 * )
 */
enum SqlOrderDirectionEnum: string
{
    case Ascending = 'asc';
    case Descending = 'desc';
}
