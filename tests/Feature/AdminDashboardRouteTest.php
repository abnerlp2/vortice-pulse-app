<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the admin dashboard at the /admin route', function () {
    $response = $this->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Dashboard de Organización');
});
