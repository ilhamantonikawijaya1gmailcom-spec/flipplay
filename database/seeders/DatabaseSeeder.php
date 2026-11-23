<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'alif@example.test'],
            ['name' => 'Alif', 'password' => 'demo12345']
        );

        User::firstOrCreate(
            ['email' => 'nisrina@example.test'],
            ['name' => 'Nisrina', 'password' => 'demo12345']
        );

        Media::firstOrCreate(
            ['title' => 'Langit Senja'],
            [
                'type' => 'film',
                'genre' => 'Drama',
                'release_year' => 2025,
                'description' => 'Film drama fiktif untuk latihan API FlipPlay.',
            ]
        );

        Media::firstOrCreate(
            ['title' => 'Jejak Digital'],
            [
                'type' => 'series',
                'genre' => 'Misteri',
                'release_year' => 2026,
                'description' => 'Serial misteri fiktif untuk latihan API FlipPlay.',
            ]
        );
    }
}
