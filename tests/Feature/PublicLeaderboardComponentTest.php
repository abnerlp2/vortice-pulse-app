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
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);

        Livewire::test(\App\Livewire\PublicLeaderboard::class)
            ->assertSee('Talk 1')
            ->dispatch('echo:modules.dashboard,.evaluation.received', [
                'talk_id' => 'talk-1',
                'average' => 4.5,
                'total_votes' => 10,
                'time_block_id' => 'block-1'
            ])
            ->assertSet('talkStats.talk-1.average', 4.5)
            ->assertSet('talkStats.talk-1.total_votes', 10);
    }
}
