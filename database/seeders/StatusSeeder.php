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
            ['color' => '#FF0000']
        );

        Status::query()->firstOrCreate(
            ['title' => 'в работе'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на проверке'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на доработке'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'на гарантии'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'успешно закрыт'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'отменен'],
            ['color' => '#FFFF00']
        );

        Status::query()->firstOrCreate(
            ['title' => 'отказ клиента'],
            ['color' => '#FFFF00']
        );
    }
}
