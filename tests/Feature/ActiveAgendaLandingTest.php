<?php

namespace Tests\Feature;

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveAgendaLandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure tests run with a valid timezone
        date_default_timezone_set('UTC');
    }

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
            'start_time' => now()->subMinutes(9),
            'end_time' => now()->addMinutes(40),
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
        $response->assertSee($activeTalk->formatted_start_time . ' - ' . $activeTalk->formatted_end_time);
        $response->assertDontSee('Inactive Talk');
    }

    public function test_it_shows_recently_finished_section_with_active_and_grace_blocks()
    {
        \Carbon\Carbon::setTestNow(now());

        $activeBlock = TimeBlock::create([
            'id' => 'active-block-2',
            'start_time' => now()->subMinutes(5),
            'end_time' => now()->addMinutes(25),
        ]);

        $recentBlock = TimeBlock::create([
            'id' => 'recent-block',
            'start_time' => now()->subHour(),
            'end_time' => now()->subMinutes(10), // ended 10 minutes ago
        ]);

        $activeTalk = Talk::create([
            'id' => 'active-talk-2',
            'time_block_id' => $activeBlock->id,
            'title' => 'Active Now',
            'speaker' => 'Speaker A',
            'start_time' => now()->subMinutes(4),
            'end_time' => now()->addMinutes(20),
        ]);

        $recentTalk = Talk::create([
            'id' => 'recent-talk',
            'time_block_id' => $recentBlock->id,
            'title' => 'Recently Ended',
            'speaker' => 'Speaker B',
            'start_time' => $recentBlock->start_time,
            'end_time' => $recentBlock->end_time,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Active Now');
        $response->assertSee('Terminadas recientemente');
        $response->assertSee('Recently Ended');

        \Carbon\Carbon::setTestNow();
    }

    public function test_it_shows_message_when_no_active_block()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('No hay charlas activas en este momento');
    }

    public function test_it_renders_sticky_header_and_max_w_md_container()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('vortice-logo.svg');
        $response->assertSee('sticky top-0');
        $response->assertSee('max-w-md');
    }
}
