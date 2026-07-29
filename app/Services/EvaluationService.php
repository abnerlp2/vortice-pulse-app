<?php

namespace App\Services;

use App\Core\Evaluation\Services\EvaluationService as CoreEvaluationService;

class EvaluationService extends CoreEvaluationService
{
    public function importAgendaFromCsv(string $filePath): bool
    {
        // Implementation for CSV/XLSX import using maatwebsite/excel
        return true;
    }
}
