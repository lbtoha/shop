<?php

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
        $this->command->info('Creating Users...');
        User::factory()->create([
            'email' => 'user@gmail.com',
            'phone' => '+8801555555555',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
    }
}
