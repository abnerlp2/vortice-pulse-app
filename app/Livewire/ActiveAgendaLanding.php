<?php

namespace App\Livewire;

use App\Models\TimeBlock;
use Livewire\Component;

class ActiveAgendaLanding extends Component
{
    public function render()
    {
        $now = now();
        
        $activeBlock = TimeBlock::with('talks')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        return view('livewire.active-agenda-landing', [
            'activeBlock' => $activeBlock,
            'talks' => $activeBlock ? $activeBlock->talks : collect(),
        ])->layout('components.layouts.app');
    }
}
