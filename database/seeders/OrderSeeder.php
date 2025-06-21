<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::query()->firstOrCreate(
            ['title' => 'Первый заказ'],
            [
                'type_work_id' => 1,
                'subject_id' => 1,
                'description' => 'Текст первого заказа с антиплагиатом. Сразу для эксперта.',
                'hidden_field' => 'тут скрытый текст',

                'manager_id' => 3,
                'expert_id' => 2,

                'plagiarism_platform_id' => 1,
                'text_uniqueness' => 99,

                'price' => 100,
                'status_id' => 1,
                'deadline_at' => now()->addDay(),
            ]
        );

        Order::query()->firstOrCreate(
            ['title' => 'Второй заказ'],
            [
                'type_work_id' => 2,
                'subject_id' => 2,
                'description' => 'Текст второго заказа без антиплагиата и выбранного эксперта.',
                'hidden_field' => 'тут скрытый текст',

                'manager_id' => 3,

                'price' =>50,
                'status_id' => 1,
                'deadline_at' => now()->addDays(3),
            ]
        );

    }
}
