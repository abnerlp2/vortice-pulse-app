<?php

namespace App\Livewire;

use App\Core\Evaluation\Services\EvaluationService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MobileEvaluator extends Component
{
    public string $talkId;
    public ?int $rating = null;
    public ?string $deviceSignature = null;
    public ?string $likedAspects = null;
    public ?string $improvementAspects = null;
    
    public bool $hasSubmitted = false;

    public function mount(string $talkId)
    {
        $this->talkId = $talkId;
    }

    public function submitEvaluation(EvaluationService $service)
    {
        $this->validate([
            'talkId' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'deviceSignature' => 'required|string|min:10',
            'likedAspects' => 'nullable|string|max:1000',
            'improvementAspects' => 'nullable|string|max:1000',
        ]);

        try {
            $service->registerVote(
                $this->talkId, 
                $this->rating, 
                $this->deviceSignature, 
                $this->likedAspects, 
                $this->improvementAspects
            );
            $this->hasSubmitted = true;
        } catch (ValidationException $e) {
            // Re-lanzar las excepciones de validación atrapadas (como la de duplicado) para que Livewire las intercepte
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.mobile-evaluator');
    }
}
