<?php

namespace Tests\Feature\Commands;

use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateQrsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_qrs_for_all_talks()
    {
        Storage::fake('public');
        
        $timeBlock = TimeBlock::create([
            'id' => 'block-1',
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);

        $talk1 = Talk::create([
            'id' => 'talk-uuid-1',
            'time_block_id' => $timeBlock->id,
            'title' => 'Talk 1',
            'speaker' => 'Speaker 1',
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);

        $talk2 = Talk::create([
            'id' => 'talk-uuid-2',
            'time_block_id' => $timeBlock->id,
            'title' => 'Talk 2',
            'speaker' => 'Speaker 2',
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);

        $this->artisan('pulse:generate-qrs')
            ->expectsOutput('Generating QRs for 2 talks...')
            ->expectsOutput('QR generated for: Talk 1')
            ->expectsOutput('QR generated for: Talk 2')
            ->assertExitCode(0);

        Storage::disk('public')->assertExists("qrs/talk-uuid-1.svg");
        Storage::disk('public')->assertExists("qrs/talk-uuid-2.svg");
    }
}
