<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PessoaFisicaController;
use App\Http\Controllers\PessoaJuridicaController;
use App\Http\Controllers\ContaBancariaController;
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');

    // Rotas para Usuários
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/reprove', [UserController::class, 'reprove'])->name('users.reprove');

    // Rotas para Pessoa Física
    Route::resource('pessoa-fisica', PessoaFisicaController::class);

    // Rotas para Pessoa Jurídica
    Route::resource('pessoa-juridica', PessoaJuridicaController::class);

    // Rotas para Contas Bancárias
    Route::resource('contas-bancarias', ContaBancariaController::class);
});

// Rotas de Autenticação
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
