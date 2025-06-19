<?php

namespace Database\Seeders;

use App\Models\TypeWork;
use Illuminate\Database\Seeder;

class TypesWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeWork::query()->firstOrCreate([
            'title' => 'Задача'
        ]);

        TypeWork::query()->firstOrCreate([
            'title' => 'Контрольная работа',
        ]);

        TypeWork::query()->firstOrCreate([
            'title' => 'Курсовая работа',
        ]);
    }
}
