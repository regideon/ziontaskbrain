<?php

use App\Http\Controllers\AuthController;
use App\Livewire\AgentsLab;
use App\Livewire\McpPlayground;
use App\Livewire\TaskBoard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
});

Route::middleware('auth')->group(function () {
    Route::get('/task-board', TaskBoard::class)->name('task-board');
    Route::get('/agents', AgentsLab::class)->name('agents-lab');
    Route::get('/mcp-playground', McpPlayground::class)->name('mcp-playground');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
