<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::query()->firstOrCreate(
            [
                'order_id' => 1,
                'title' => 'Первая задача',
            ],
            [
                'deadline_at' => now()->addDay(),
            ]
        );

        Task::query()->firstOrCreate(
            [
                'order_id' => 1,
                'title' => 'Вторая задача',
            ],
            [
                'deadline_at' => now()->addDays(2),
            ]
        );
    }
}
