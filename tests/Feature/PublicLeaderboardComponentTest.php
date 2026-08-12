<?php

namespace Tests\Feature;

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicLeaderboardComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully()
    {
        $response = $this->get('/public');
        $response->assertStatus(200);
        $response->assertSee('vortice-logo.svg');
        $response->assertSee('bg-white/95');
        $response->assertDontSee('VORTICE PULSE');
    }

    public function test_it_displays_the_talk_time_range_in_the_public_leaderboard_response()
    {
        $timeBlock = TimeBlock::create([
            'id' => 'block-time-range',
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(10, 0),
        ]);

        $talk = Talk::create([
            'id' => 'talk-time-range',
            'time_block_id' => $timeBlock->id,
            'title' => 'Tiempo de Charla',
            'speaker' => 'Ponente',
            'room' => 'Sala 1',
            'start_time' => now()->setTime(9, 15),
            'end_time' => now()->setTime(9, 45),
        ]);

        Livewire::test(\App\Livewire\PublicLeaderboard::class)
            ->assertSee($talk->formatted_start_time . ' - ' . $talk->formatted_end_time);

        $response = $this->get('/public');

        $response->assertStatus(200);
        $response->assertSee($talk->formatted_start_time . ' - ' . $talk->formatted_end_time);
        $response->assertSee('Tiempo de Charla');
        $response->assertSee('Ponente');
    }

    public function test_it_updates_stats_when_evaluation_is_received()
    {
        $timeBlock = TimeBlock::create([
            'id' => 'block-1',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        $talk = Talk::create([
            'id' => 'talk-1',
            'time_block_id' => $timeBlock->id,
            'title' => 'Talk 1',
            'speaker' => 'Speaker 1',
            'start_time' => now()->setTime(9, 15),
            'end_time' => now()->setTime(9, 45),
        ]);

        Livewire::test(\App\Livewire\PublicLeaderboard::class)
            ->assertSee('Talk 1')
            ->dispatch('echo:modules.dashboard,.evaluation.received', [
                'talk_id' => 'talk-1',
                'average' => 4.5,
                'total_votes' => 10,
                'time_block_id' => 'block-1'
            ])
            ->assertSee($talk->formatted_start_time . ' - ' . $talk->formatted_end_time)
            ->assertSet('talkStats.talk-1.average', 4.5)
            ->assertSet('talkStats.talk-1.total_votes', 10);
    }
}
