<?php

use App\Models\Evaluation;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Redis::spy();

    $this->timeBlock = TimeBlock::create([
        'id' => 'block-edit-1',
        'start_time' => now()->subHour(),
        'end_time' => now()->addHour(),
    ]);

    $this->secondTimeBlock = TimeBlock::create([
        'id' => 'block-edit-2',
        'start_time' => now()->addHours(2),
        'end_time' => now()->addHours(3),
    ]);

    $this->talk = Talk::create([
        'id' => 'talk-edit-1',
        'title' => 'Original Talk Title',
        'speaker' => 'Original Speaker',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subHour(),
        'end_time' => now()->addHour(),
    ]);
});

it('loads talk data into component state when editTalk is called', function () {
    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->call('editTalk', $this->talk->id)
        ->assertSet('editingTalkId', $this->talk->id)
        ->assertSet('editTitle', 'Original Talk Title')
        ->assertSet('editSpeaker', 'Original Speaker')
        ->assertSet('editTimeBlockId', $this->timeBlock->id)
        ->assertSet('showEditModal', true);
});

it('updates talk fields in MySQL database and purges Redis cache when valid data is submitted', function () {
    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->call('editTalk', $this->talk->id)
        ->set('editTitle', 'Updated Talk Title')
        ->set('editSpeaker', 'Updated Speaker Name')
        ->set('editTimeBlockId', $this->secondTimeBlock->id)
        ->call('updateTalk')
        ->assertHasNoErrors()
        ->assertSet('showEditModal', false);

    $this->assertDatabaseHas('talks', [
        'id' => $this->talk->id,
        'title' => 'Updated Talk Title',
        'speaker' => 'Updated Speaker Name',
        'time_block_id' => $this->secondTimeBlock->id,
    ]);

    Redis::shouldHaveReceived('del')->with("vortice:pulse:talk:{$this->talk->id}");
});

it('rejects blank fields when editing a talk', function () {
    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->call('editTalk', $this->talk->id)
        ->set('editTitle', '')
        ->set('editSpeaker', '')
        ->set('editTimeBlockId', 'invalid-time-block-id')
        ->call('updateTalk')
        ->assertHasErrors(['editTitle', 'editSpeaker', 'editTimeBlockId']);

    $this->assertDatabaseHas('talks', [
        'id' => $this->talk->id,
        'title' => 'Original Talk Title',
        'speaker' => 'Original Speaker',
        'time_block_id' => $this->timeBlock->id,
    ]);
});

it('preserves existing evaluations when talk fields are updated', function () {
    Evaluation::create([
        'talk_id' => $this->talk->id,
        'rating' => 5,
        'device_signature' => 'device-sig-123',
        'liked_aspects' => 'Great content',
        'improvement_aspects' => 'None',
    ]);

    expect(Evaluation::where('talk_id', $this->talk->id)->count())->toBe(1);

    Livewire::test(\App\Livewire\AdminDashboard::class)
        ->call('editTalk', $this->talk->id)
        ->set('editTitle', 'Renamed Talk Title')
        ->set('editSpeaker', 'Renamed Speaker')
        ->call('updateTalk')
        ->assertHasNoErrors();

    expect(Evaluation::where('talk_id', $this->talk->id)->count())->toBe(1);
    $this->assertDatabaseHas('evaluations', [
        'talk_id' => $this->talk->id,
        'device_signature' => 'device-sig-123',
        'rating' => 5,
    ]);
});
