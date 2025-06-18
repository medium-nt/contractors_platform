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
        User::query()->firstOrCreate(
            ['email' => '1@1.ru'],
            [
                'name' => 'Тестовый',
                'last_name' => 'Админ',
                'password' => bcrypt('111111'),
                'is_approved' => 1,
                'role_id' => 3
            ]
        );

        User::query()->firstOrCreate(
            ['email' => '2@2.ru'],
            [
                'name' => 'Тестовый',
                'last_name' => 'Эксперт',
                'password' => bcrypt('222222'),
                'is_approved' => 1,
                'role_id' => 2
            ]
        );

        User::query()->firstOrCreate(
            ['email' => '3@3.ru'],
            [
                'name' => 'Тестовый',
                'last_name' => 'Менеджер',
                'password' => bcrypt('333333'),
                'is_approved' => 1,
                'role_id' => 1
            ]
        );

//        User::factory(10)->create();
    }
}
