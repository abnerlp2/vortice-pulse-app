<?php

namespace Tests\Unit;

use App\Core\Event\Services\QrGeneratorService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrGeneratorServiceTest extends TestCase
{
    public function test_it_generates_an_svg_qr_code_file()
    {
        Storage::fake('public');
        $service = new QrGeneratorService();
        $filename = 'test-qr.svg';
        $content = 'https://example.com';

        $path = $service->generate($content, $filename);

        $this->assertEquals('qrs/test-qr.svg', $path);
        Storage::disk('public')->assertExists('qrs/test-qr.svg');
        
        $fileContent = Storage::disk('public')->get('qrs/test-qr.svg');
        $this->assertStringContainsString('<svg', $fileContent);
    }
}
