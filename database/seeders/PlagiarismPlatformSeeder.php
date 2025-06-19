<?php

namespace Database\Seeders;

use App\Models\PlagiarismPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlagiarismPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlagiarismPlatform::query()->firstOrCreate([
            'title' => 'eTXT'
        ]);

        PlagiarismPlatform::query()->firstOrCreate([
            'title' => 'Антиплагиат.РУ'
        ]);
    }
}
