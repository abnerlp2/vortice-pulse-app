<?php

namespace App\Console\Commands;

use App\Core\Event\Contracts\QrGeneratorInterface;
use App\Models\Talk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class GenerateQrs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pulse:generate-qrs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all talks';

    /**
     * Execute the console command.
     */
    public function handle(QrGeneratorInterface $qrGenerator)
    {
        $talks = Talk::all();
        
        $this->info("Generating QRs for {$talks->count()} talks...");

        foreach ($talks as $talk) {
            $url = route('talk.show', ['talk' => $talk->id]);
            
            $qrGenerator->generate($url, "{$talk->id}.svg");
            
            $this->line("QR generated for: {$talk->title}");
        }

        $this->info('All QRs generated successfully.');
    }
}
