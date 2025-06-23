<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::query()->firstOrCreate(
            ['title' => 'новый'],
            ['color' => '#ced4da']
        );

        Status::query()->firstOrCreate(
            ['title' => 'в работе'],
            ['color' => '#a8cbfe']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на проверке'],
            ['color' => '#ffc107']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на доработке'],
            ['color' => '#fea75f']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на гарантии'],
            ['color' => '#9d7ed5']
        );

        Status::query()->firstOrCreate(
            ['title' => 'успешно закрыт'],
            ['color' => '#28a745']
        );

        Status::query()->firstOrCreate(
            ['title' => 'отменен'],
            ['color' => '#dc3545']
        );

        Status::query()->firstOrCreate(
            ['title' => 'отказ клиента'],
            ['color' => '#e77681']
        );
    }
}
