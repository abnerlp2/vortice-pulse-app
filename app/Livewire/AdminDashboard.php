<?php

namespace App\Livewire;

use App\Core\Evaluation\Contracts\EvaluationRepositoryInterface;
use App\Core\Evaluation\Services\RedisCacheHelper;
use App\Models\Talk;
use App\Models\TimeBlock;
use Livewire\Attributes\On;
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
    public bool $showSlideOver = false;
    public ?string $selectedTalkId = null;
    public array $qualitativeComments = [
        'liked' => [],
        'improvement' => [],
    ];

    public bool $showEditModal = false;
    public ?string $editingTalkId = null;
    public string $editTitle = '';
    public string $editSpeaker = '';
    public string $editSpeakers = '';
    public ?string $editRoom = null;
    public string $editTimeBlockId = '';
    public array $availableTimeBlocks = [];

    public function mount(?EvaluationRepositoryInterface $repository = null): void
    {
        $this->loadTalks($repository);
    }

    public function loadTalks(?EvaluationRepositoryInterface $repository = null): void
    {
        $repository = $repository ?? app(EvaluationRepositoryInterface::class);
        $talks = Talk::with('timeBlock')->get();

        $this->talks = $talks->map(function (Talk $talk) use ($repository) {
            $this->talkTitles[$talk->id] = $talk->title;

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
                'room' => $talk->room,
                'time_block_id' => $talk->time_block_id,
                'formatted_start_time' => $talk->formatted_start_time,
                'formatted_end_time' => $talk->formatted_end_time,
                'end_time' => $talk->timeBlock?->end_time,
                'average' => $average,
            ];
        })->toArray();
        
        $sortedTalks = collect($this->talks)->sortByDesc('average')->values();
        $this->podiumOrder = $sortedTalks->pluck('id')->toArray();

        $this->availableTimeBlocks = TimeBlock::all()->toArray();

        $this->evaluateBlockStatus();
    }

    public function editTalk(string $talkId): void
    {
        $talk = Talk::find($talkId);

        if (!$talk) {
            return;
        }

        $this->editingTalkId = $talk->id;
        $this->editTitle = $talk->title;
        $this->editSpeaker = $talk->speaker;
        $this->editSpeakers = $talk->speaker;
        $this->editRoom = $talk->room;
        $this->editTimeBlockId = $talk->time_block_id;
        $this->availableTimeBlocks = TimeBlock::all()->toArray();
        $this->showEditModal = true;
    }

    public function updatedEditSpeaker(string $value): void
    {
        $this->editSpeakers = $value;
    }

    public function updatedEditSpeakers(string $value): void
    {
        $this->editSpeaker = $value;
    }

    public function updateTalk(): void
    {
        $this->validate([
            'editTitle' => 'required|string|min:1|max:255',
            'editSpeaker' => 'required|string|min:1|max:255',
            'editRoom' => 'nullable|string|max:255',
            'editTimeBlockId' => 'required|string|exists:time_blocks,id',
        ], [
            'editTitle.required' => 'El título es obligatorio.',
            'editSpeaker.required' => 'El conferencista es obligatorio.',
            'editTimeBlockId.required' => 'El bloque de tiempo es obligatorio.',
            'editTimeBlockId.exists' => 'El bloque de tiempo no es válido.',
        ]);

        if (!$this->editingTalkId) {
            return;
        }

        $talk = Talk::findOrFail($this->editingTalkId);
        $talk->update([
            'title' => $this->editTitle,
            'speaker' => $this->editSpeaker,
            'room' => $this->editRoom ?: null,
            'time_block_id' => $this->editTimeBlockId,
        ]);

        $cacheHelper = app(RedisCacheHelper::class);
        $cacheHelper->delete("vortice:pulse:talk:{$talk->id}");

        $this->loadTalks();

        $this->showEditModal = false;
        $this->editingTalkId = null;
        $this->resetValidation();
        $this->reset(['editTitle', 'editSpeaker', 'editSpeakers', 'editRoom', 'editTimeBlockId']);
    }

    public function cancelEdit(): void
    {
        $this->showEditModal = false;
        $this->editingTalkId = null;
        $this->resetValidation();
        $this->reset(['editTitle', 'editSpeaker', 'editSpeakers', 'editRoom', 'editTimeBlockId']);
    }

    public function render()
    {
        return view('livewire.admin-dashboard')->layout('components.layouts.app');
    }

    #[On('echo:modules.dashboard,.evaluation.received')]
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

    #[On('echo:modules.dashboard,.ranking.order.altered')]
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

    public function loadQualitativeData(string $talkId): void
    {
        $this->selectedTalkId = $talkId;
        
        $evaluations = \App\Models\Evaluation::where('talk_id', $talkId)
            ->where(function ($query) {
                $query->whereNotNull('liked_aspects')
                      ->orWhereNotNull('improvement_aspects');
            })
            ->get();

        $this->qualitativeComments = [
            'liked' => $evaluations->pluck('liked_aspects')->filter()->values()->toArray(),
            'improvement' => $evaluations->pluck('improvement_aspects')->filter()->values()->toArray(),
        ];

        $this->showSlideOver = true;
    }
}
