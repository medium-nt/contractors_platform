<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->firstOrCreate(
            ['name' => 'manager'],
            ['title' => 'Менеджер']
        );

        Role::query()->firstOrCreate(
            ['name' => 'expert'],
            ['title' => 'Эксперт']
        );

        Role::query()->firstOrCreate(
            ['name' => 'admin'],
            ['title' => 'Администратор']
        );
    }
}
