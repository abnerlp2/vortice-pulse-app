<?php

namespace App\Core\Evaluation\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvaluationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $talkId;
    public string $timeBlockId;
    public float $average;
    public int $totalVotes;
    public int $rating;

    public function __construct(string $talkId, string $timeBlockId, float $average, int $totalVotes, int $rating)
    {
        $this->talkId = $talkId;
        $this->timeBlockId = $timeBlockId;
        $this->average = $average;
        $this->totalVotes = $totalVotes;
        $this->rating = $rating;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('modules.dashboard');
    }

    public function broadcastAs(): string
    {
        return 'evaluation.received';
    }

    public function broadcastWith(): array
    {
        return [
            'talk_id' => $this->talkId,
            'time_block_id' => $this->timeBlockId,
            'average' => $this->average,
            'total_votes' => $this->totalVotes,
            'rating' => $this->rating,
        ];
    }
}
