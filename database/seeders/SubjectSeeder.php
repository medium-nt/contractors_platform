<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::query()->firstOrCreate([
            'title' => 'Алгебра'
        ]);

        Subject::query()->firstOrCreate([
            'title' => 'Геометрия'
        ]);

        Subject::query()->firstOrCreate([
            'title' => 'Физика'
        ]);

        Subject::query()->firstOrCreate([
            'title' => 'Химия'
        ]);
    }
}
