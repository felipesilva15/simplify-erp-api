<?php

namespace Database\Seeders;

use App\Modules\Security\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $defaultAdminPassowrd = Hash::make('Admin@123');

        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@simplify.com.br',
                'username' => 'admin',
                'password' => $defaultAdminPassowrd,
                'phone_number' => '11983432682',
                'is_admin' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                [
                    'username' => $userData['username']
                ],
                ['name' => $userData['name'], 'email' => $userData['email'], 'password' => $userData['password'], 'phone_number' => $userData['phone_number'], 'is_admin' => $userData['is_admin'], 'created_at' => $userData['created_at'], 'updated_at' => $userData['updated_at']]
            );
        }
    }
}
