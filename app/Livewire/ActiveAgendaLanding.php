<?php

namespace App\Livewire;

use App\Models\TimeBlock;
use Livewire\Component;

class ActiveAgendaLanding extends Component
{
    public $currentBlock;
    public $graceBlocks = [];

    public function render()
    {
        $now = now();

        $this->currentBlock = TimeBlock::with('talks')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        $graceThreshold = $now->copy()->subMinutes(30);

        $graceBlocksQuery = TimeBlock::with('talks')
            ->where('end_time', '<', $now)
            ->where('end_time', '>=', $graceThreshold);

        if (! $this->currentBlock) {
            $this->currentBlock = $graceBlocksQuery->first();
            $this->graceBlocks = collect()->toArray();
        } else {
            $this->graceBlocks = $graceBlocksQuery->where('id', '!=', $this->currentBlock->id)->get()->toArray();
        }

        $activeTalks = collect();
        $recentlyFinishedTalks = collect();

        if ($this->currentBlock) {
            $activeTalks = $this->currentBlock->talks->sort(function ($a, $b) use ($now) {
                $aActive = $a->end_time >= $now;
                $bActive = $b->end_time >= $now;

                if ($aActive && ! $bActive) {
                    return -1;
                }

                if (! $aActive && $bActive) {
                    return 1;
                }

                return $a->start_time->timestamp <=> $b->start_time->timestamp;
            })->values();
        }

        if (! empty($this->graceBlocks)) {
            foreach ($this->graceBlocks as $gb) {
                $block = TimeBlock::find($gb['id']);
                if ($block) {
                    $recentlyFinishedTalks = $recentlyFinishedTalks->concat($block->talks);
                }
            }
        }

        return view('livewire.active-agenda-landing', [
            'activeBlock' => $this->currentBlock,
            'activeTalks' => $activeTalks,
            'recentlyFinishedTalks' => $recentlyFinishedTalks->values(),
        ])->layout('components.layouts.app');
    }
}
