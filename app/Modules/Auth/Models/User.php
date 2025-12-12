<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *      schema="User",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="email", type="string", example="Sample"),
 *      @OA\Property(property="email_verified_at", type="string", format="date-time", example="2025-12-11T11:13:03.678895Z", nullable=true),
 *      @OA\Property(property="password", type="string", example="Sample"),
 *      @OA\Property(property="remember_token", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-11T11:13:03.678895Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-11T11:13:03.678895Z", nullable=true),
 *      @OA\Property(property="username", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-11T11:13:03.678895Z", nullable=true)
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use SoftDeletes, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone_number'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function getAuthPassword() {
        return $this->password;
    }
}