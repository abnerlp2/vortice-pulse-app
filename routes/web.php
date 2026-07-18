<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AdminDashboard;

Route::get('/', AdminDashboard::class)->name('dashboard');
