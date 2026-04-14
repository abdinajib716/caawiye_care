<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Karshe',
            'last_name' => 'Yare',
            'email' => 'karsheyare152@gmail.com',
            'username' => 'superadmin',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        // Removed demo users to keep database clean
        // Only superadmin user is created above
        
        $this->command->info('Users table seeded with 1 superadmin user!');
    }
}
