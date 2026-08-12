<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminSetup;
use App\Livewire\MobileEvaluator;
use App\Livewire\ActiveAgendaLanding;
use App\Livewire\PublicLeaderboard;

Route::get('/', ActiveAgendaLanding::class)->name('landing');

Route::prefix('admin')->group(function () {
    Route::get('/login', function () {
        return view('admin.login');
    })->name('admin.login');

    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $password = (string) $request->input('password');

        if ($password === config('app.admin_password')) {
            $request->session()->put('admin_authenticated', true);
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('admin.login')->with('error', 'Contraseña incorrecta.');
    });

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/setup', AdminSetup::class)->name('admin.setup');
    });
});

Route::get('/talk/{talk}', MobileEvaluator::class)->name('talk.show');
Route::get('/public', PublicLeaderboard::class)->name('public-leaderboard');
