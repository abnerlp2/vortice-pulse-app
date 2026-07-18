<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the admin dashboard at the root route', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Dashboard de Organización');
});
