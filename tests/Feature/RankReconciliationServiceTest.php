<?php

use App\Core\Evaluation\Services\RankReconciliationService;
use App\Core\Evaluation\Services\RedisCacheHelper;
use App\Core\Evaluation\Repositories\EvaluationRepository;
use App\Models\Talk;
use App\Models\TimeBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Redis::flushdb();
    $this->timeBlock = TimeBlock::create([
        'id' => 'block-rank',
        'start_time' => now()->subMinutes(20),
        'end_time' => now()->addMinutes(20),
    ]);

    $this->talkA = Talk::create([
        'id' => 'talk-a',
        'title' => 'Talk A',
        'speaker' => 'Speaker A',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
    $this->talkB = Talk::create([
        'id' => 'talk-b',
        'title' => 'Talk B',
        'speaker' => 'Speaker B',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
    $this->talkC = Talk::create([
        'id' => 'talk-c',
        'title' => 'Talk C',
        'speaker' => 'Speaker C',
        'time_block_id' => $this->timeBlock->id,
        'start_time' => now()->subMinutes(10),
        'end_time' => now()->addMinutes(10),
    ]);
});

it('creates a ranking alert when offline sync changes the top positions of the time block', function () {
    DB::table('evaluations')->insert([
        ['talk_id' => $this->talkA->id, 'rating' => 5, 'device_signature' => hash('sha256', 'toka-a-1'), 'created_at' => now(), 'updated_at' => now()],
        ['talk_id' => $this->talkB->id, 'rating' => 2, 'device_signature' => hash('sha256', 'talk-b-1'), 'created_at' => now(), 'updated_at' => now()],
        ['talk_id' => $this->talkC->id, 'rating' => 1, 'device_signature' => hash('sha256', 'talk-c-1'), 'created_at' => now(), 'updated_at' => now()],
    ]);

    $service = new RankReconciliationService(app(EvaluationRepository::class), app(RedisCacheHelper::class));
    $service->reconcile($this->timeBlock->id, [
        ['talk_id' => $this->talkC->id, 'rating' => 5, 'device_signature' => hash('sha256', 'talk-c-2'), 'liked_aspects' => null, 'improvement_aspects' => null, 'created_at' => now()->toDateTimeString()],
    ]);

    expect(DB::table('ranking_alerts')->count())->toBeGreaterThan(0);
});

it('excludes duplicate offline evaluations using Redis cache idempotency checks', function () {
    $service = new RankReconciliationService(app(EvaluationRepository::class), app(RedisCacheHelper::class));
    $signature = hash('sha256', 'duplicate-sig-1');
    
    // Simulate first insertion
    $service->reconcile($this->timeBlock->id, [
        ['talk_id' => $this->talkA->id, 'rating' => 5, 'device_signature' => $signature, 'liked_aspects' => null, 'improvement_aspects' => null, 'created_at' => now()->toDateTimeString()],
    ]);
    
    $countAfterFirst = DB::table('evaluations')->where('device_signature', $signature)->count();
    expect($countAfterFirst)->toBe(1);
    
    // Set redis cache key manually to simulate idempotency check (which we will implement in T0029)
    Redis::setex("vortice:pulse:eval:{$this->talkA->id}:{$signature}", 3600, 1);
    
    // Simulate duplicate payload
    $service->reconcile($this->timeBlock->id, [
        ['talk_id' => $this->talkA->id, 'rating' => 5, 'device_signature' => $signature, 'liked_aspects' => null, 'improvement_aspects' => null, 'created_at' => now()->toDateTimeString()],
    ]);
    
    $countAfterSecond = DB::table('evaluations')->where('device_signature', $signature)->count();
    expect($countAfterSecond)->toBe(1);
});

it('rejects offline evaluations that exceed the 10-minute expiration window', function () {
    $service = new RankReconciliationService(app(EvaluationRepository::class), app(RedisCacheHelper::class));
    
    // Create an expired payload (11 minutes after the block's end_time)
    $expiredTime = Carbon\Carbon::parse($this->timeBlock->end_time)->addMinutes(11);
    $signature = hash('sha256', 'expired-sig-1');
    
    $service->reconcile($this->timeBlock->id, [
        ['talk_id' => $this->talkB->id, 'rating' => 4, 'device_signature' => $signature, 'liked_aspects' => null, 'improvement_aspects' => null, 'created_at' => $expiredTime->toDateTimeString()],
    ]);
    
    $count = DB::table('evaluations')->where('device_signature', $signature)->count();
    expect($count)->toBe(0);
});
