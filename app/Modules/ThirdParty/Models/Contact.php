<?php

namespace App\Modules\ThirdParty\Models;

use App\Core\Models\BaseModel;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Contact",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="partner_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Roberto Almeida"),
 *     @OA\Property(property="department", type="string", example="TI"),
 *     @OA\Property(property="email", type="string", format="email", example="roberto.almeida@simplify.com.br"),
 *     @OA\Property(property="mobile", type="string", example="11985984268"),
 *     @OA\Property(property="phone", type="string", example="1159856859"),
 *     @OA\Property(property="main", type="boolean", example=false),
 *     @OA\Property(property="notes", type="string", example="Entrar em contato somente para suporte"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-25T14:06:03.522794Z", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="ContactItem",
 *     description="Item da lista contacts enviada junto do parceiro. O id ausente ou zero (0) indica um contato novo; ausente no payload remove o contato existente.",
 *
 *     @OA\Property(property="id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="name", type="string", example="Roberto Almeida", minLength=1, maxLength=120),
 *     @OA\Property(property="department", type="string", example="TI", minLength=1, maxLength=80, nullable=true),
 *     @OA\Property(property="email", type="string", format="email", example="roberto.almeida@simplify.com.br", minLength=1, maxLength=180, nullable=true),
 *     @OA\Property(property="mobile", type="string", example="11985984268", minLength=10, maxLength=11, nullable=true),
 *     @OA\Property(property="phone", type="string", example="1159856859", minLength=10, maxLength=10, nullable=true),
 *     @OA\Property(property="main", type="boolean", example=true, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Entrar em contato somente para suporte", nullable=true)
 * )
 */
#[UseFactory(ContactFactory::class)]
class Contact extends BaseModel
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'name',
        'department',
        'email',
        'mobile',
        'phone',
        'main',
        'notes',
    ];

    protected $casts = [
        'main' => 'boolean',
    ];
}
