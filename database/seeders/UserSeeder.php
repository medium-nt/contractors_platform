<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Тестовый',
            'last_name' => 'Админ',
            'email' => '1@1.ru',
            'password' => bcrypt('111111'),
            'is_approved' => 1,
            'role_id' => 3
        ]);

        User::query()->create([
            'name' => 'Тестовый',
            'last_name' => 'Эксперт',
            'email' => '2@2.ru',
            'password' => bcrypt('222222'),
            'is_approved' => 1,
            'role_id' => 2
        ]);

        User::query()->create([
            'name' => 'Тестовый',
            'last_name' => 'Менеджер',
            'email' => '3@3.ru',
            'password' => bcrypt('333333'),
            'is_approved' => 1,
            'role_id' => 1
        ]);

        User::factory(10)->create();
    }
}
