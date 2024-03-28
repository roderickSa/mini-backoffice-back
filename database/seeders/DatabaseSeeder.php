<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin12345'),
            'role' => RoleEnum::ADMIN->value
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('12345')
        ]);

        Category::factory(3)->create();
    }
}
