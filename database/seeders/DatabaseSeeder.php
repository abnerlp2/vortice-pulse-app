<?php

namespace Database\Seeders;

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeBlockId = Str::uuid()->toString();

        TimeBlock::create([
            'id' => $timeBlockId,
            'start_time' => now()->subMinutes(30),
            'end_time' => now()->addMinutes(30),
        ]);

        Talk::create([
            'id' => Str::uuid()->toString(),
            'time_block_id' => $timeBlockId,
            'title' => 'Introducción a Laravel Reverb',
            'speaker' => 'Taylor Otwell',
            'start_time' => now()->subMinutes(30),
            'end_time' => now()->subMinutes(10),
        ]);

        Talk::create([
            'id' => Str::uuid()->toString(),
            'time_block_id' => $timeBlockId,
            'title' => 'Escalando con Laravel Echo y WebSockets',
            'speaker' => 'Joe Dixon',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(10),
        ]);

        Talk::create([
            'id' => Str::uuid()->toString(),
            'time_block_id' => $timeBlockId,
            'title' => 'Aplicaciones reactivas con Livewire 3',
            'speaker' => 'Caleb Porzio',
            'start_time' => now()->addMinutes(10),
            'end_time' => now()->addMinutes(30),
        ]);
    }
}
