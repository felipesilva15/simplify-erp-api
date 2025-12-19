<?php

namespace Database\Seeders;

use App\Modules\Security\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassowrd = Hash::make('Admin@123');

        if ($admin = User::where('username', 'admin')->first()) {
            $admin->update([
                'password' => $defaultPassowrd
            ]);
            return;
        }

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@simplify.com',
            'username' => 'admin',
            'password' => $defaultPassowrd,
            'phone_number' => '11983432682',
            'is_admin' => true
        ]);
    }
}
