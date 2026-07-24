<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AdminDashboard;
use App\Livewire\MobileEvaluator;
use App\Livewire\ActiveAgendaLanding;
use App\Livewire\PublicLeaderboard;

Route::get('/', ActiveAgendaLanding::class)->name('landing');
Route::get('/admin', AdminDashboard::class)->name('dashboard');
Route::get('/talk/{talk}', MobileEvaluator::class)->name('talk.show');
Route::get('/public', PublicLeaderboard::class)->name('public-leaderboard');
