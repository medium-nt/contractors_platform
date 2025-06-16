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
        Role::query()->create([
            'name' => 'manager',
            'title' => 'Менеджер',
        ]);

        Role::query()->create([
            'name' => 'contractor',
            'title' => 'Эксперт',
        ]);

        Role::query()->create([
            'name' => 'admin',
            'title' => 'Администратор',
        ]);
    }
}
