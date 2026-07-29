<?php

declare(strict_types=1);

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Livewire\AdminSetup;
use App\Services\EvaluationService;
use Mockery\MockInterface;

test('admin setup component requires authenticated session', function () {
    Livewire::test(AdminSetup::class)
        ->assertStatus(403);
});

test('admin can upload a valid csv file', function () {
    $this->session(['admin_authenticated' => true]);
    Storage::fake('local');

    $file = UploadedFile::fake()->create('agenda.csv', 100, 'text/csv');

    $this->mock(EvaluationService::class, function (MockInterface $mock) {
        $mock->shouldReceive('importAgendaFromCsv')->once()->andReturn(true);
    });

    Livewire::test(AdminSetup::class)
        ->set('file', $file)
        ->call('import')
        ->assertHasNoErrors()
        ->assertSee('Importación completada con éxito');
});

test('admin cannot upload invalid file extensions', function () {
    $this->session(['admin_authenticated' => true]);
    Storage::fake('local');

    $file = UploadedFile::fake()->create('malicious.php', 100);

    Livewire::test(AdminSetup::class)
        ->set('file', $file)
        ->call('import')
        ->assertHasErrors(['file']);
});

test('renders the admin setup page at /admin/setup route when authenticated', function () {
    $this->withSession(['admin_authenticated' => true]);

    $response = $this->get('/admin/setup');

    $response->assertStatus(200);
    $response->assertSee('Configuración Inicial de Agenda');
    $response->assertSee('Volver al Dashboard');
    $response->assertSee(route('dashboard'));
    $response->assertSee('vortice-logo.svg');
});
