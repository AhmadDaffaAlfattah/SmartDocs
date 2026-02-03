<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@pln.co.id'],
            [
                'name' => 'Super Admin',
                'password' => 'password123', // Plain text as requested
                'role' => 'super_admin',
                'bidang' => null,
            ]
        );
    }
}
