<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PessoaFisicaController;
use App\Http\Controllers\PessoaJuridicaController;
use App\Http\Controllers\ContaBancariaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ExtratoController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');

    // Rotas para Usuários
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/reprove', [UserController::class, 'reprove'])->name('users.reprove');
    Route::post('users/{user}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

    // Rotas para Pessoa Física
    Route::resource('pessoa-fisica', PessoaFisicaController::class);

    // Rotas para Pessoa Jurídica
    Route::resource('pessoa-juridica', PessoaJuridicaController::class);

    // Rotas para Contas Bancárias (Protegidas pelo Middleware Admin)
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('contas-bancarias', ContaBancariaController::class)->except(['create', 'store', 'edit', 'update', 'destroy', 'show']);
        Route::post('contas-bancarias/{conta}/approve', [ContaBancariaController::class, 'approve'])->name('contas-bancarias.approve');
        Route::post('contas-bancarias/{conta}/reprove', [ContaBancariaController::class, 'reprove'])->name('contas-bancarias.reprove');
    });

    // Rotas do Cliente
    Route::prefix('conta')->name('conta.')->group(function () {
        Route::get('/abrir', [ContaController::class, 'abrirContaForm'])->name('abrir.form');
        Route::post('/abrir', [ContaController::class, 'abrirConta'])->name('abrir.store');
        Route::get('/extrato/{conta}', [ContaController::class, 'extrato'])->name('extrato');
        Route::get('/transferencia/{conta}', [ContaController::class, 'transferenciaForm'])->name('transferencia.form');
        Route::post('/transferencia/{conta}', [ContaController::class, 'transferencia'])->name('transferencia.store');
    });

    // Rotas de Extrato
    Route::prefix('extrato')->name('extrato.')->group(function () {
        Route::get('/', [ExtratoController::class, 'index'])->name('index');
        Route::get('/carteira/{carteira}', [ExtratoController::class, 'carteira'])->name('carteira');
    });
});

// Rotas de Autenticação
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rotas de Registro
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
