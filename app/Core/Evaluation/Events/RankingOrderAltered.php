<?php

namespace App\Core\Evaluation\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RankingOrderAltered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $timeBlockId;
    public array $beforeOrder;
    public array $afterOrder;
    public array $affectedTalks;
    public string $message;

    public function __construct(string $timeBlockId, array $beforeOrder, array $afterOrder, array $affectedTalks, string $message)
    {
        $this->timeBlockId = $timeBlockId;
        $this->beforeOrder = $beforeOrder;
        $this->afterOrder = $afterOrder;
        $this->affectedTalks = $affectedTalks;
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('modules.dashboard');
    }

    public function broadcastAs(): string
    {
        return 'ranking.order.altered';
    }

    public function broadcastWith(): array
    {
        return [
            'time_block_id' => $this->timeBlockId,
            'before_order' => $this->beforeOrder,
            'after_order' => $this->afterOrder,
            'affected_talks' => $this->affectedTalks,
            'message' => $this->message,
        ];
    }
}
