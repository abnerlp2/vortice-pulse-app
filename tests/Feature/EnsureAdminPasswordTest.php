<?php

declare(strict_types=1);

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    Config::set('app.admin_password', 'secret123');
    $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
});

test('unauthenticated admin access redirects to login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('admin can login with correct password', function () {
    $this->post('/admin/login', [
        'password' => 'secret123',
    ])
        ->assertRedirect('/admin')
        ->assertSessionHas('admin_authenticated', true);

    $this->get('/admin')
        ->assertStatus(200);
});

test('admin cannot login with incorrect password', function () {
    // Vuelve al formulario con el error: expulsar al home público perdía el
    // contexto de quien solo se equivocó de contraseña.
    $this->post('/admin/login', [
        'password' => 'wrong-password',
    ])
        ->assertRedirect('/admin/login')
        ->assertSessionHas('error')
        ->assertSessionMissing('admin_authenticated');
});

test('authenticated admin can access admin routes', function () {
    Session::put('admin_authenticated', true);

    $this->get('/admin')
        ->assertStatus(200);
});
