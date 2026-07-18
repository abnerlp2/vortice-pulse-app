<?php

namespace App\Livewire;

use App\Models\Talk;
use Livewire\Component;

class AdminDashboard extends Component
{
    public array $talks = [];
    public array $talkTitles = [];
    public array $talkStats = [];
    public array $podiumOrder = [];
    public bool $hasOfflineAlert = false;
    public array $offlineAlert = [];
    public bool $isBlockActive = true;
    public string $blockStatus = 'En vivo';

    public function mount()
    {
        $talks = Talk::with('timeBlock')->get();

        $this->talks = $talks->map(function (Talk $talk) {
            $this->talkTitles[$talk->id] = $talk->title;

            return [
                'id' => $talk->id,
                'title' => $talk->title,
                'speaker' => $talk->speaker,
                'time_block_id' => $talk->time_block_id,
                'end_time' => $talk->timeBlock?->end_time,
            ];
        })->toArray();

        $this->evaluateBlockStatus();
    }

    public function render()
    {
        return view('livewire.admin-dashboard')->layout('components.layouts.app');
    }

    public function onEvaluationReceived(array $payload): void
    {
        if (!$this->isBlockActive && isset($payload['time_block_id'])) {
            return;
        }

        $this->talkStats[$payload['talk_id']] = [
            'average' => round($payload['average'], 1),
            'total_votes' => $payload['total_votes'],
        ];
    }

    public function onRankingOrderAltered(array $payload): void
    {
        $this->hasOfflineAlert = true;
        $this->offlineAlert = [
            'message' => $payload['message'] ?? 'Se ha detectado una permutación en el podio tras procesar votos fuera de línea.',
            'affected_talks' => $payload['affected_talks'] ?? [],
        ];
        $this->podiumOrder = $payload['after_order'] ?? [];
    }

    private function evaluateBlockStatus(): void
    {
        $now = now();

        foreach ($this->talks as $talk) {
            if (isset($talk['end_time']) && $talk['end_time'] instanceof \DateTimeInterface) {
                if ($now->greaterThan($talk['end_time'])) {
                    $this->isBlockActive = false;
                    $this->blockStatus = 'Inactivo';

                    return;
                }
            }
        }

        $this->isBlockActive = true;
        $this->blockStatus = 'En vivo';
    }
}
