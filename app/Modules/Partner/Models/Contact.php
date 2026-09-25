<?php

namespace App\Modules\Partner\Models;

use App\Core\Models\BaseModel;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Contact",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="partner_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="department", type="string", example="Sample"),
 *     @OA\Property(property="email", type="string", example="Sample"),
 *     @OA\Property(property="mobile", type="string", example="Sample"),
 *     @OA\Property(property="phone", type="string", example="Sample"),
 *     @OA\Property(property="main", type="boolean", example=false),
 *     @OA\Property(property="notes", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true)
 * )
 */
#[UseFactory(ContactFactory::class)]
class Contact extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'partner_id',
        'name',
        'department',
        'email',
        'mobile',
        'phone',
        'main',
        'notes'
    ];

    protected $casts = [
        'main' => 'boolean'
    ];
}