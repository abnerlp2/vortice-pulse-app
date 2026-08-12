<?php

namespace App\Services;

use App\Core\Evaluation\Services\EvaluationService as CoreEvaluationService;

class EvaluationService extends CoreEvaluationService
{
    public function importAgendaFromCsv(string $filePath): bool
    {
        return parent::importAgendaFromCsv($filePath);
    }
}
