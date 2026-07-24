<?php

namespace App\Livewire;

use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Models\Talk;
use Livewire\Attributes\On;
use Livewire\Component;

class PublicLeaderboard extends Component
{
    public array $talks = [];
    public array $talkStats = [];
    public array $podiumOrder = [];

    public function mount(EvaluationRepositoryInterface $repository)
    {
        $talks = Talk::all();

        $this->talks = $talks->map(function (Talk $talk) use ($repository) {
            $ratings = $repository->getTalkRatings($talk->id);
            $totalVotes = count($ratings);
            $average = $totalVotes > 0 ? array_sum($ratings) / $totalVotes : 0;
            
            $this->talkStats[$talk->id] = [
                'average' => round($average, 1),
                'total_votes' => $totalVotes,
            ];

            return [
                'id' => $talk->id,
                'title' => $talk->title,
                'speaker' => $talk->speaker,
                'average' => $average,
            ];
        })->toArray();
        
        $this->updatePodiumOrder();
    }

    #[On('echo:modules.dashboard,.evaluation.received')]
    public function onEvaluationReceived(array $payload): void
    {
        $this->talkStats[$payload['talk_id']] = [
            'average' => round($payload['average'], 1),
            'total_votes' => $payload['total_votes'],
        ];

        foreach ($this->talks as &$talk) {
            if ($talk['id'] === $payload['talk_id']) {
                $talk['average'] = $payload['average'];
            }
        }

        $this->updatePodiumOrder();
    }

    private function updatePodiumOrder(): void
    {
        $this->podiumOrder = collect($this->talks)
            ->sortByDesc(fn($talk) => $this->talkStats[$talk['id']]['average'])
            ->pluck('id')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.public-leaderboard')->layout('components.layouts.app');
    }
}
