<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PessoaFisicaController;
use App\Http\Controllers\PessoaJuridicaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas para Usuários
Route::apiResource('users', UserController::class);

// Rotas para Pessoa Física
Route::apiResource('pessoa-fisica', PessoaFisicaController::class);

// Rotas para Pessoa Jurídica
Route::apiResource('pessoa-juridica', PessoaJuridicaController::class);

