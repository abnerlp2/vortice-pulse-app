<?php

namespace App\Core\Event\Services;

use App\Core\Event\Contracts\QrGeneratorInterface;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrGeneratorService implements QrGeneratorInterface
{
    public function generate(string $content, string $filename): string
    {
        $path = "qrs/{$filename}";
        
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($content);

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
