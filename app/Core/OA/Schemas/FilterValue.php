<?php

namespace App\Core\OA\Schemas;

/**
 * @OA\Schema(
 *     schema="FilterValue",
 *     oneOf={
 *         @OA\Schema(type="string"),
 *         @OA\Schema(type="number"),
 *         @OA\Schema(type="boolean")
 *     }
 * )
 */
class FilterValue {

}