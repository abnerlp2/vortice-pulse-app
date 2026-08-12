<?php

namespace Tests\Feature;

use App\Core\Evaluation\Services\EvaluationService;
use App\Livewire\ActiveAgendaLanding;
use App\Livewire\AdminDashboard;
use App\Livewire\MobileEvaluator;
use App\Livewire\PublicLeaderboard;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TalkRoomInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_evaluation_service_parses_and_persists_room()
    {
        $service = app(EvaluationService::class);

        $payload = json_encode([
            'time_blocks' => [
                [
                    'id' => 'block-1',
                    'start_time' => '2026-08-11 09:00:00',
                    'end_time' => '2026-08-11 10:00:00',
                ],
            ],
            'talks' => [
                [
                    'id' => 'talk-1',
                    'time_block_id' => 'block-1',
                    'title' => 'Charlas Reactivas',
                    'speaker' => 'Ana Martínez',
                    'room' => 'Auditorio Principal',
                    'start_time' => '2026-08-11 09:00:00',
                    'end_time' => '2026-08-11 10:00:00',
                ],
                [
                    'id' => 'talk-2',
                    'time_block_id' => 'block-1',
                    'title' => 'Redis Avanzado',
                    'speaker' => 'Carlos Gómez',
                    'start_time' => '2026-08-11 09:00:00',
                    'end_time' => '2026-08-11 10:00:00',
                ],
            ],
        ]);

        $service->importAgenda($payload);

        $talk1 = Talk::find('talk-1');
        $talk2 = Talk::find('talk-2');

        $this->assertEquals('Auditorio Principal', $talk1->room);
        $this->assertNull($talk2->room);
    }

    public function test_active_agenda_landing_displays_room_and_fallback()
    {
        $activeBlock = TimeBlock::create([
            'id' => 'active-block',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        Talk::create([
            'id' => 'talk-room-specified',
            'time_block_id' => $activeBlock->id,
            'title' => 'Charla con Sala',
            'speaker' => 'Speaker A',
            'room' => 'Auditorio A',
            'start_time' => $activeBlock->start_time,
            'end_time' => $activeBlock->end_time,
        ]);

        Talk::create([
            'id' => 'talk-room-null',
            'time_block_id' => $activeBlock->id,
            'title' => 'Charla sin Sala',
            'speaker' => 'Speaker B',
            'room' => null,
            'start_time' => $activeBlock->start_time,
            'end_time' => $activeBlock->end_time,
        ]);

        Livewire::test(ActiveAgendaLanding::class)
            ->assertSee('Charla con Sala')
            ->assertSee('Auditorio A')
            ->assertSee('Charla sin Sala')
            ->assertSee('Por confirmar');
    }

    public function test_mobile_evaluator_displays_room_and_fallback()
    {
        $activeBlock = TimeBlock::create([
            'id' => 'active-block',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        Talk::create([
            'id' => 'talk-evaluator-room',
            'time_block_id' => $activeBlock->id,
            'title' => 'Charla Evaluador',
            'speaker' => 'Speaker C',
            'room' => 'Sala 101',
            'start_time' => $activeBlock->start_time,
            'end_time' => $activeBlock->end_time,
        ]);

        Livewire::test(MobileEvaluator::class, ['talk' => 'talk-evaluator-room'])
            ->assertSee('Charla Evaluador')
            ->assertSee('Speaker C')
            ->assertSee('Sala 101');
    }

    public function test_public_leaderboard_displays_room_info()
    {
        $block = TimeBlock::create([
            'id' => 'block-1',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        Talk::create([
            'id' => 'talk-leaderboard',
            'time_block_id' => $block->id,
            'title' => 'Charla Leaderboard',
            'speaker' => 'Speaker D',
            'room' => 'Sala VIP',
            'start_time' => $block->start_time,
            'end_time' => $block->end_time,
        ]);

        Livewire::test(PublicLeaderboard::class)
            ->assertSee('Charla Leaderboard')
            ->assertSee('Speaker D')
            ->assertSee('Sala VIP');
    }

    public function test_admin_dashboard_displays_edits_room_info()
    {
        session(['admin_authenticated' => true]);

        $block = TimeBlock::create([
            'id' => 'block-1',
            'start_time' => now()->subMinutes(10),
            'end_time' => now()->addMinutes(50),
        ]);

        $talk = Talk::create([
            'id' => 'talk-admin',
            'time_block_id' => $block->id,
            'title' => 'Charla Admin',
            'speaker' => 'Speaker E',
            'room' => 'Sala Inicial',
            'start_time' => $block->start_time,
            'end_time' => $block->end_time,
        ]);

        Livewire::test(AdminDashboard::class)
            ->assertSee('Charla Admin')
            ->assertSee('Sala Inicial')
            ->call('editTalk', $talk->id)
            ->set('editRoom', 'Auditorio Magna')
            ->call('updateTalk')
            ->assertHasNoErrors();

        $this->assertEquals('Auditorio Magna', $talk->fresh()->room);
    }
}
