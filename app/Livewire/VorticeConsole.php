<?php

namespace App\Livewire;

use App\Core\Evaluation\Services\EvaluationService;
use App\Models\Talk;
use Livewire\Component;

class VorticeConsole extends Component
{
    public string $talkId;

    public function mount(string $talkId)
    {
        $this->talkId = $talkId;
    }

    public function render(EvaluationService $service)
    {
        $talk = Talk::findOrFail($this->talkId);
        $statistics = $service->getTalkStatistics($this->talkId);

        return view('livewire.vortice-console', [
            'talk' => $talk,
            'average' => $statistics['average'],
            'std_dev' => $statistics['standard_deviation'],
            'total_votes' => $statistics['total_votes'],
        ]);
    }
}
