<?php

namespace Tests\Feature;

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveAgendaLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_talks_for_the_active_time_block()
    {
        $activeBlock = TimeBlock::create([
            'id' => 'active-block',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        $inactiveBlock = TimeBlock::create([
            'id' => 'inactive-block',
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
        ]);

        $activeTalk = Talk::create([
            'id' => 'active-talk',
            'time_block_id' => $activeBlock->id,
            'title' => 'Active Talk',
            'speaker' => 'Speaker A',
            'start_time' => $activeBlock->start_time,
            'end_time' => $activeBlock->end_time,
        ]);

        $inactiveTalk = Talk::create([
            'id' => 'inactive-talk',
            'time_block_id' => $inactiveBlock->id,
            'title' => 'Inactive Talk',
            'speaker' => 'Speaker B',
            'start_time' => $inactiveBlock->start_time,
            'end_time' => $inactiveBlock->end_time,
        ]);

        $response = $this->withoutExceptionHandling()->get('/');

        $response->assertStatus(200);
        $response->assertSee('Active Talk');
        $response->assertDontSee('Inactive Talk');
    }

    public function test_it_shows_message_when_no_active_block()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('No hay charlas activas en este momento');
    }
}
