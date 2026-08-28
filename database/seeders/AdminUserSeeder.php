<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates the initial Admin account described in the project brief.
     * IMPORTANT: the password is hashed with bcrypt/argon (never stored
     * as plain text) and the account is flagged so the admin panel forces
     * a password change on first login.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'madinashopandfood@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'must_change_password' => true,
            ]
        );
    }
}
