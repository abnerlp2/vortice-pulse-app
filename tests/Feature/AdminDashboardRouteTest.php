<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the admin dashboard at the /admin route when authenticated', function () {
    $this->withSession(['admin_authenticated' => true]);

    $response = $this->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Dashboard de Organización');
});
